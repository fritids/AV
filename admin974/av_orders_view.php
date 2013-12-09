<?php
require_once "../configs/settings.php";
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");
include ("functions/supplier.php");
require('../libs/Smarty.class.php');
require('../classes/class.phpmailer.php');
require('../classes/tcpdf.php');
require "../classes/php-export-data.class.php";

define("COMMANDE_FOURNISSEUR", 16);

$smarty = new Smarty;
$smarty->caching = 0;
//$smarty->error_reporting = E_ALL & ~E_NOTICE;
$smarty->setTemplateDir(array('../templates', '../templates/mails/', '../templates/mails/admin', '../templates/pdf', '../templates/pdf/admin'));

//Create a new PHPMailer instance
$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';
//Set who the message is to be sent from

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

if (isset($_GET["id_order"]))
    $oid = $_GET["id_order"];

if (isset($_POST["id_order"]))
    $oid = $_POST["id_order"];

$updated = array();

/* id client */
$r = $db->rawQuery("select id_customer from av_orders where id_order = ?", array($oid));
$suppliers = $db->get("av_supplier");
$orderStates = $db->where("id_level", 0)
        ->get("av_order_status");
$productStates = $db->where("id_level", 1)
        ->get("av_order_status");
$cid = $r[0]["id_customer"];

//contact
$customer_info = getCustomerDetail($cid);

//commande 
$orderinfo = getOrderInfos($oid);


//paiement
$orderPayment = getOrderPayment($orderinfo["id_order"]);

//Adresse
$customer_delivery = getAdresseById($orderinfo["id_address_delivery"]);
$customer_invoice = getAdresseById($orderinfo["id_address_invoice"]);

// pour le system de nav.
$a = $db->rawQuery("select id_order, current_state from mv_orders where ? > id_order  order by id_order desc", array($oid));
$b = $db->rawQuery("select id_order, current_state from mv_orders where id_order > ? order by id_order asc", array($oid));

/* $order_precedent = @$a[0]["id_order"];
  $order_suivant = @$b[0]["id_order"];
 */
$order_precedent = @$a;
$order_suivant = @$b;

//print_r($_POST);

if (isset($_POST) && isset($_POST["add_notes"])) {

    $params = array(
        "id_order" => $oid,
        "id_admin" => $_SESSION["user_id"],
        "message" => $_POST["order_note"],
        "private" => 1,
        "date_add" => date("y-m-d H:i:s")
    );
    $r = $db->insert("av_order_note", $params);
    if ($r)
        $updated["text"] = "Note a été ajouté";

    $orderinfo = getOrderInfos($oid);
}


/* Update des combobox */
if (isset($_POST) && !empty($_POST) && isset($_POST["order_action_modify"]) || isset($_POST["supplier_date_delivery"]) || isset($_POST["order_state"])) {

    if (isset($_POST["id_supplier"]) && !empty($_POST["id_supplier"]))
        if (is_array($_POST["id_supplier"])) {
            foreach ($_POST["id_supplier"] as $id => $supplier) {
                $r = $db->where("id_order_detail", $id)
                        ->update("av_order_detail", array("id_supplier" => $supplier));
            }
        } else {
            foreach ($orderinfo["details"] as $od) {
                $r = $db->where("id_order_detail", $od["id_order_detail"])
                        ->update("av_order_detail", array("id_supplier" => $_POST["id_supplier"]));
            }
        }
    if (isset($_POST["supplier_date_delivery"]) && !empty($_POST["supplier_date_delivery"]))
        if (is_array($_POST["supplier_date_delivery"])) {
            foreach ($_POST["supplier_date_delivery"] as $id => $date_delivery) {
                if (!empty($date_delivery))
                    $r = $db->where("id_order_detail", $id)
                            ->update("av_order_detail", array("supplier_date_delivery" => $date_delivery));
            }
        }else {
            foreach ($orderinfo["details"] as $od) {
                $r = $db->where("id_order_detail", $od["id_order_detail"])
                        ->update("av_order_detail", array("supplier_date_delivery" => $_POST["supplier_date_delivery"]));
            }
        }
    if (isset($_POST["product_current_state"]) && !empty($_POST["product_current_state"]))
        if (is_array($_POST["product_current_state"])) {
            foreach ($_POST["product_current_state"] as $id => $state) {
                $r = $db->where("id_order_detail", $id)
                        ->update("av_order_detail", array("product_current_state" => $state));
                //casse ou SAV
                if ($state == 21 || $state == 22) {
                    $r = $db->where("id_order_detail", $id)
                            ->delete("av_tournee");

                    $r = $db->where("id_order_detail", $id)
                            ->update("av_order_detail", array("supplier_date_delivery" => null));
                }
            }
        } else {
            foreach ($orderinfo["details"] as $od) {
                $r = $db->where("id_order_detail", $od["id_order_detail"])
                        ->update("av_order_detail", array("product_current_state" => $_POST["product_current_state"]));
                //casse ou SAV
                if ($_POST["product_current_state"] == 21 || $_POST["product_current_state"] == 22) {
                    $r = $db->where("id_order_detail", $od["id_order_detail"])
                            ->delete(("av_tournee"));

                    $r = $db->where("id_order_detail", $od["id_order_detail"])
                            ->update("av_order_detail", array("supplier_date_delivery" => null));
                }
            }
        }

//maj status order  
    if (isset($_POST["current_state"]) && !empty($_POST["current_state"])) {

        $r = $db->where("id_order", $oid)
                ->update("av_orders", array("current_state" => $_POST["current_state"]));

        if ($r) {

            switch ($_POST["current_state"]) {
                case 7:
                    $order_mail_subject = "Allovitre - votre commande #" . $orderinfo["id_order"] . " a été remboursé";
                    $order_mail_from = "service.commercial@allovitres.com";
                    $order_mail_tpl = "notif_order_refund";
                    $order_sms_tpl = "notif_sms_refund";
                    break;
                case 6:
                    $order_mail_subject = "Allovitre - votre commande #" . $orderinfo["id_order"] . " a été annulé";
                    $order_mail_from = "service.commercial@allovitres.com";
                    $order_mail_tpl = "notif_order_cancel";
                    $order_sms_tpl = "notif_sms_cancel";
                    break;
                case 3:
                    $order_mail_subject = "Allovitre - votre commande #" . $orderinfo["id_order"] . " est en cours de préparation";
                    $order_mail_from = "livraison@allovitres.com";
                    $order_mail_tpl = "notif_order_preparation";
                    $order_sms_tpl = "notif_sms_preparation";
                    break;
                default :
                    $order_mail_subject = "";
                    $order_mail_from = "";
                    $order_mail_tpl = "";
                    break;
            }

            if ($orderinfo . alert_sms == 1) {

                //
                $param = array(
                    "id_order" => $orderinfo["id_order"],
                    "id_user" => $_SESSION["user_id"],
                    "category" => $order_sms_tpl,
                );
                $r = $db->insert("av_order_bdc", $param);
            }

            if (!empty($order_mail_tpl)) {

                $mail->ClearAllRecipients();
                $mail->ClearAttachments();

                foreach ($monitoringEmails as $bccer) {
                    $mail->AddbCC($bccer);
                }
                $mail->AddAddress($orderinfo["customer"]["email"]);

                $mail->SetFrom($order_mail_from);
                $mail->Subject = $order_mail_subject;
                $mail_body = $smarty->fetch($order_mail_tpl . ".tpl");

                $mail->MsgHTML($mail_body);
                if ($mail->Send()) {
                    $updated["text"] = "mail a été envoyé <br>";
                    $param = array(
                        "id_order" => $orderinfo["id_order"],
                        "id_user" => $_SESSION["user_id"],
                        "category" => $order_mail_tpl,
                    );
                    $r = $db->insert("av_order_bdc", $param);
                }
            }

            addLog(array("tabs" => "mv_orders",
                "rowkey" => $orderinfo["id_order"],
                "col" => "current_state",
                "operation" => "update",
                "oldval" => $orderinfo["current_state"],
                "newval" => $_POST["current_state"]
            ));
        }
    }

    @$updated["text"] .= "Modification a été effectuée.";

//suite aux update on recharge les infos    
    $orderinfo = getOrderInfos($oid);
}


if (isset($_POST) && !empty($_POST["order_action_send_supplier"])) {

    $orderSuppliers = getOrdersDetailSupplier($oid);
    $tmp = "";
    foreach ($orderSuppliers as $k => $orderSupplier) {

        $mail->ClearAllRecipients();
        $mail->ClearAttachments();
        $mail->SetFrom($confmail["from"]);
        foreach ($monitoringEmails as $bccer) {
            $mail->AddbCC($bccer);
        }
        $mail->AddAddress($orderSupplier["email"]);
        $mail->Subject = "Bon de commande : #" . $orderinfo["id_order"];

        $orderDetailSupplier = getUserOrdersDetail($oid, $orderSupplier["id_supplier"]);
        $orderDetailSupplierXLS = $orderDetailSupplier;

        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Allovitre');
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetFont('times', '', 11);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->AddPage("L", "A4");
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING);


        $smarty->assign("supplier", $orderSupplier);
        $smarty->assign("orderinfo", $orderinfo);
        $smarty->assign("user_email", $_SESSION["email"]);
        $smarty->assign("orderdetail", $orderDetailSupplier);
        $mail_body = $smarty->fetch('admin_supplier_ask_order.tpl');
        $bdc_pdf_body = $smarty->fetch('admin_bon_commande.tpl');

        $pdf->writeHTML($bdc_pdf_body, true, false, true, false, '');
        $pdf->lastPage();

        $path = "./ressources/bon_de_commandes";
        $order_path = $path . "/" . $orderinfo["id_order"];

        //$bdc_commande_filename = "BDC_" . $orderSupplier["id_supplier"] . "_" . $orderinfo["id_order"] . "_" . date("dMy") ;
        $bdc_commande_filename = md5(rand());
        @mkdir($order_path);
        $pdf->Output($order_path . "/" . $bdc_commande_filename . ".pdf", 'F');

        //Excel


        $fp = fopen($order_path . "/" . $bdc_commande_filename . ".xls", 'w');
        fwrite($fp, iconv("utf-8", "ISO-8859-1", $bdc_pdf_body));
        fclose($fp);


        // fin excel

        $mail->addAttachment($order_path . "/" . $bdc_commande_filename . ".pdf");
        $mail->addAttachment($order_path . "/" . $bdc_commande_filename . ".xls");
        $mail->MsgHTML($mail_body);

        if ($mail->Send()) {
            $tmp .= "<li> " . $orderSupplier["name"] . " ( " . count($orderDetailSupplier) . " article(s) commandé(s) )</li>";

            $params = array("product_current_state" => COMMANDE_FOURNISSEUR);
            $r = $db->where("id_order", $oid)
                    ->where("id_supplier", $orderSupplier["id_supplier"])
                    ->update("av_order_detail", $params);

            foreach ($orderDetailSupplier as $k => $ods) {
                $param = array(
                    "id_order" => $oid,
                    "id_user" => $_SESSION["user_id"],
                    "id_order_detail" => $ods["id_order_detail"],
                    "supplier_name" => $orderSupplier["name"],
                    "bdc_filename" => $bdc_commande_filename
                );
                $r = $db->insert("av_order_bdc", $param);
            }

            $orderinfo = getOrderInfos($oid);
        }
    }
    $updated["text"] = "Bon de commande envoyé au(x) founisseur(s) suivant(s) : <ul>" . $tmp . "</ul>";
}


if (isset($_POST["split_order"]) && isset($_POST["qte"])) {

    $p_qte = $_POST["qte"];

    foreach ($p_qte as $odid => $qte) {
        if (!empty($qte))
            $oid = splitOrderDetail($odid, $qte);
    }
    $orderinfo = getOrderInfos($oid);
    $updated["text"] = "La ligne a été 'splitté'";
}
?>



<div class="container">    
    <div class="row">
        <div class="col-xs-1">
            <?
            if ($order_precedent) {
                ?>
                <a href="?id_order=<?= $order_precedent[0]["id_order"] ?>" data-toggle="tooltip" title="Commande précédente"><span class="glyphicon glyphicon-arrow-left"></span></a>
                <?
            }
            ?>
        </div>
        <div class="col-xs-1">
            <?
            if ($order_suivant) {
                ?>
                <a href="?id_order=<?= $order_suivant[0]["id_order"] ?>" data-toggle="tooltip" title="Commande suivante"><span class="glyphicon glyphicon-arrow-right"></span></a>
                <?
            }
            ?>
        </div>
        <div class="col-xs-10">
            <ul class="pagination">
                <?
                for ($i = 5; $i >= 0; $i--) {
                    if (isset($order_precedent[$i])) {
                        ?>
                        <li ><a href="?id_order=<?= $order_precedent[$i]["id_order"] ?>" class="alert-<?= @$order_precedent[$i]["current_state"] ?>"><?= @$order_precedent[$i]["id_order"] ?></a></li>
                        <?
                    }
                }
                ?>

                <li><a href="#" class="alert-<?= $orderinfo["current_state"] ?>"><strong><?= $oid ?></strong></a></li>
                <?
                for ($i = 0; $i <= 5; $i++) {
                    if (isset($order_suivant[$i])) {
                        ?>
                        <li><a href="?id_order=<?= $order_suivant[$i]["id_order"] ?>" class="alert-<?= @$order_suivant[$i]["current_state"] ?>"><?= @$order_suivant[$i]["id_order"] ?></a></li>
                        <?
                    }
                }
                ?>

            </ul>
        </div>


    </div>
    <?
    if (!empty($updated)) {
        ?>
        <div class="row">
            <div class="col-xs-12" >
                <div class="alert alert-success">
                    <?= $updated["text"] ?>
                </div>                   

            </div>

        </div>
        <?
    }
    ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-5">
                <h3>Commande : <?= $oid ?></h3>
            </div>  
            <div class="col-xs-3">
                <form action="av_download_pdf.php?order" method="post" target="blank">
                    <input type="hidden" value="<?= $oid ?>" name="id_order">
                    <button class="btn btn-default" data-toggle="tooltip" title="Télécharger la facture au format PDF"><span class="glyphicon glyphicon-floppy-save"></span></button>
                </form> 
            </div>
            <div class="text-center col-xs-4 alert alert-<?= $orderinfo["current_state"] ?>" >
                <form method="post">
                    <select name="current_state" class="pme-input-0">
                        <option value="">--</option>
                        <?
                        foreach ($orderStates as $orderState) {
                            ?>
                            <option value="<?= $orderState["id_statut"] ?>"
                            <?= ($orderinfo["current_state"] == $orderState["id_statut"]) ? "selected" : "" ?>
                                    ><?= $orderState["title"] ?> </option>
                                    <?
                                }
                                ?> 
                    </select>

                    <button type="submit" class="btn btn-sm" name="order_state">Ok</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-3">
            <div class="panel panel-default">
                <div class="panel-heading">Contact <div class="pull-right">
                        <a href="av_customer_view.php?id_customer=<?= $customer_info["id_customer"] ?>" data-toggle="tooltip" title="Consulter la fiche client"><span class="glyphicon glyphicon-user"></span></a>
                        <a href="av_customer.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $customer_info["id_customer"] ?>" data-toggle="tooltip" title="Modifier les infos personnelles"><span class="glyphicon glyphicon-edit"></span></a>
                    </div>
                </div>
                <div class="panel-body">
                    Nom : <?= @$customer_info["firstname"] ?> <br>
                    Prénom :<?= @$customer_info["lastname"] ?> <br>
                    Email : <?= @$customer_info["email"] ?> <br>                        
                    Type : <?= (@$customer_info["customer_group"] == 1) ? "PRO" : "Normal"; ?> <br>                        
                </div>
            </div>            
        </div>

        <div class="col-xs-3">
            <div class="panel panel-default">
                <div class="panel-heading">Adresse Livraison <div class="pull-right"><a href="av_address.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= @$customer_delivery["id_address"] ?>" data-toggle="tooltip" title="Modifier l'adresse de livraison"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                <div class="panel-body">
                    <span class="glyphicon glyphicon-earphone" ></span> <?= @$customer_delivery["phone"] ?><br>
                    <span class="glyphicon glyphicon-phone" ></span> <?= @$customer_delivery["phone_mobile"] ?> <br>
                    <?= @$customer_delivery["address1"] ?><br>
                    <?= @$customer_delivery["address2"] ?><br>
                    <?= @$customer_delivery["postcode"] ?> <?= @$customer_delivery["city"] ?><br>    
                </div>
            </div>  
        </div>
        <div class="col-xs-3">
            <div class="panel panel-default">
                <div class="panel-heading">Adresse Facturation <div class="pull-right"><a href="av_address.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= @$customer_invoice["id_address"] ?>" data-toggle="tooltip" title="Modifier l'adresse de facturation"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                <div class="panel-body">
                    <span class="glyphicon glyphicon-earphone" ></span> <?= @$customer_invoice["phone"] ?> <br>
                    <span class="glyphicon glyphicon-phone" ></span> <?= @$customer_invoice["phone_mobile"] ?> <br>
                    <?= @$customer_invoice["address1"] ?><br>
                    <?= @$customer_invoice["address2"] ?><br>
                    <?= @$customer_invoice["postcode"] ?> <?= @$customer_invoice["city"] ?><br>
                </div>
            </div> 
        </div>
        <div class="col-xs-3">
            <div class="panel panel-default">
                <div class="panel-heading">Commentaire client <div class="pull-right"><a href="av_orders.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $orderinfo["id_order"] ?>"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                <div class="panel-body">
                    <?= $orderinfo["order_comment"] ?>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-3">
            <div class="panel panel-default">
                <div class="panel-heading">Commande <div class="pull-right"><a href="av_orders.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $orderinfo["id_order"] ?>"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                <div class="panel-body">
                    Référence :  <?= $orderinfo["reference"] ?><br>
                    Création :  <?= strftime("%a %d %b %y %T", strtotime($orderinfo["date_add"])) ?><br>                     
                    Total :  <?= $orderinfo["total_paid"] ?>€<br>
                </div>
            </div> 
        </div>
        <div class="col-xs-3">
            <div class="panel panel-default">
                <div class="panel-heading">Paiement </div>
                <div class="panel-body <?= ($orderinfo["current_state"] == 2) ? 'alert alert-2' : ''; ?>">
                    Mode : <?= $orderinfo["payment"] ?><br>                        
                    Payé le : <?= (!empty($orderPayment["date_add"])) ? strftime("%a %d %b %y %T", strtotime($orderPayment["date_add"])) : "" ?><br>
                    Total : <?= $orderPayment["amount"] ?> €<br>
                </div>
            </div> 
        </div>       

        <div class="col-xs-3">
            <div class="panel panel-default">
                <div class="panel-heading">Commentaire interne livraison <div class="pull-right"><a href="av_orders.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $orderinfo["id_order"] ?>"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                <div class="panel-body">
                    <?= $orderinfo["delivery_comment"] ?>
                </div>
            </div>
        </div>
        <div class="col-xs-3">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>ACTION SUR TOUS PRODUITS</strong></div>
                <div class="panel-body">
                    <form method="post">
                        <p>Statuts: 
                            <select style="width: 150px"  name="product_current_state" class="pme-input-0">
                                <option value=""></option>
                                <?
                                foreach ($productStates as $pstate) {
                                    ?>
                                    <option value="<?= $pstate["id_statut"] ?>"                                        
                                            ><?= $pstate["title"] ?> </option>
                                            <?
                                        }
                                        ?>
                            </select>
                        </p>                        
                        <p>
                            Fournisseur :
                            <select name="id_supplier" class="pme-input-0">
                                <option value=""></option>
                                <?
                                foreach ($suppliers as $supplier) {
                                    ?>
                                    <option value="<?= $supplier["id_supplier"] ?>"><?= $supplier["name"] ?> </option>
                                    <?
                                }
                                ?>
                            </select>
                        </p>
                        <p>Date liv. Fournisseur: 
                            <input type="text" style="width: 90px" class="datepicker" value="" name="supplier_date_delivery"> 
                        </p>
                        <p>
                            <button type="submit" class="btn btn-sm btn-warning btn-block">Ok</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?
    if (!empty($orderinfo["details"])) {
        ?>
        <div class="row">
            <div class="col-xs-12">
                <h2>Produits</h2>
                <ul class="nav nav-pills">
                    <li class="active"><a href="#Produits" data-toggle="tab">Produits</a></li>
                    <li><a href="#bdc" data-toggle="tab">Bon de commande</a></li>
                    <li><a href="#history" data-toggle="tab">Historique</a></li>
                </ul>
                <form method="post">
                    <div class="tab-content">
                        <div class="tab-pane active" id="Produits">
                            <input type="hidden" name="id_order" value="<?= $oid ?>">

                            <table class="table table-bordered table-condensed col-xs-12" id="tab_devis">
                                <tr>
                                    <th colspan="7" class="text-center">PRODUIT</th>
                                    <th colspan="2" class="text-center">FOURNISSEUR</th>
                                    <th colspan="6" class="text-center">LIVRAISON</th>                          
                                </tr>
                                <tr>
                                    <th>Produit</th>
                                    <th>Long x Larg</th>
                                    <th>Qte</th>
                                    <th>Prix TTC</th>
                                    <th>Statuts</th>
                                    <th>Fournisseur</th>
                                    <th>Date Livraison</th>
                                    <th>Nb Livré</th>    
                                    <th>Date Livraison camion</th>    
                                    <th>Horaire</th>    
                                    <th>commentaire</th>    
                                    <th>commentaire</th>    
                                    <th>commentaire</th>    
                                    <th></th>    
                                </tr>
                                <?
                                foreach ($orderinfo["details"] as $od) {
                                    $t = getItemTourneeinfo($od["id_order_detail"]);

                                    $isFournisseurOk = true;
                                    $isFournisseurOk = ($od["id_supplier"] != '' ) ? true : false;
                                    ?>
                                    <tr id="id0">

                                        <td nowrap> 
                                            <?= $od["product_name"] ?> <br>
                                            <?
                                            foreach ($od["attributes"] as $attribute) {
                                                echo " - " . $attribute["attribute_name"] . " : " . $attribute["attribute_value"] . "<br>";
                                            }
                                            ?>
                                            <font color="red">
                                            <?
                                            foreach ($od["custom"] as $custom) {
                                                echo " - " . $custom["main_item_name"];
                                                foreach ($custom["sub_item"] as $sub_item) {
                                                    echo " - " . $sub_item["sub_item_name"] . "<br>";
                                                    foreach ($sub_item["item_values"] as $item_value) {
                                                        echo $item_value["item_value_name"] . " : " . $item_value["custom_value"] . "<br>";
                                                    }
                                                }
                                            }
                                            ?>
                                            </font>
                                            <em>ref#<?= $od["id_order_detail"] ?> - <?= $od["id_product"] ?></em>
                                        </td>
                                        <td nowrap><?= $od["product_width"] ?> x <?= $od["product_height"] ?> </td>
                                        <td>
                                            <?= ($od["product_quantity"] > 1 ) ? "<font color='red' size='3'><b>" . $od["product_quantity"] . "</b></font>" : $od["product_quantity"] ?>
                                        </td>
                                        <td nowrap><?= $od["total_price_tax_incl"] ?> €</td>
                                        <td>
                                            <select style="width: 120px"  name="product_current_state[<?= $od["id_order_detail"] ?>]" class="pme-input-0">
                                                <option value=""></option>
                                                <?
                                                foreach ($productStates as $pState) {
                                                    ?>
                                                    <option value="<?= $pState["id_statut"] ?>"
                                                    <?= ($od["product_current_state"] == $pState["id_statut"]) ? "selected" : "" ?>
                                                            ><?= $pState["title"] ?> </option>
                                                            <?
                                                        }
                                                        ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="id_supplier[<?= $od["id_order_detail"] ?>]" class="pme-input-0 supplier">
                                                <option value=""></option>
                                                <?
                                                foreach ($suppliers as $supplier) {
                                                    ?>
                                                    <option value="<?= $supplier["id_supplier"] ?>"
                                                    <?= ($od["id_supplier"] == $supplier["id_supplier"]) ? "selected" : "" ?>
                                                            ><?= $supplier["name"] ?> </option>
                                                            <?
                                                        }
                                                        ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" style="width: 75px" class="datepicker" value="<?= @$od["supplier_date_delivery"] ?>" name="supplier_date_delivery[<?= $od["id_order_detail"] ?>]"> 

                                        </td>
                                        <td><?= $t["nb_product_delivered"] ?></td>
                                        <td><?= ( $t["date_livraison"]) ? strftime("%a %d %b %y", strtotime($t["date_livraison"])) : ""; ?></td>                                
                                        <td><?= $t["horaire"] ?></td>
                                        <td><?= $t["comment1"] ?></td>
                                        <td><?= $t["comment2"] ?></td>
                                        <td><?= $t["comment3"] ?></td>
                                        <td>
                                            <?
                                            if ($od["product_quantity"] > 1) {
                                                ?>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <select name="qte[<?= $od["id_order_detail"] ?>]">
                                                            <option></option>
                                                            <?
                                                            for ($n = 1; $n < $od["product_quantity"]; $n++) {
                                                                ?>
                                                                <option value="<?= $n ?>"><?= $n ?></option>
                                                                <?
                                                            }
                                                            ?>
                                                        </select>
                                                    </span>
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-default" type="submit" name="split_order">Split!</button>
                                                    </span>
                                                </div><!-- /input-group -->
                                                <?
                                            }
                                            ?>
                                        </td>

                                    </tr>
                                    <?
                                }
                                ?>
                            </table>                            
                            <div class="col-xs-3 pull-right">
                                <div class="col-xs-12" >
                                    <p id="idmsg"></p>
                                </div>
                                <div class="col-xs-12" >
                                    <p>
                                        <input type="submit" name="order_action_modify" value="Modifier" class="btn-lg btn-warning btn-block">
                                    </p>
                                    <?
                                    if ($isFournisseurOk) {
                                        ?>
                                        <p>
                                            <input type="submit" name="order_action_send_supplier" value="Envoi fournisseur"   class="btn-lg btn-block btn-primary" id="btn_send_supplier">
                                        </p>
                                        <?
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="bdc">
                            <div class="row">
                                <table>
                                    <tr>
                                        <th>Utilisateur</th>                                        
                                        <th>Date envoi</th>
                                        <th>Fournisseur</th>
                                        <th>Bdc</th>

                                    </tr>
                                    <?
                                    if ($orderinfo["history"])
                                        foreach ($orderinfo["history"] as $odh) {
                                            if ($odh["supplier_name"]) {
                                                ?>
                                                <tr>
                                                    <td><?= $odh["prenom"] ?></td>
                                                    <td><?= strftime("%a %d %b %y %T", strtotime($odh["date_add"])) ?></td>
                                                    <td><?= $odh["supplier_name"] ?></td>
                                                    <td><a href="ressources/bon_de_commandes/<?= $odh["id_order"] ?>/<?= $odh["bdc_filename"] ?>.pdf" target="_blank">Download</a></td>
                                                </tr>
                                                <?
                                            }
                                        }
                                    ?>

                                </table>

                            </div>
                        </div>
                        <div class="tab-pane" id="history">
                            <div class="row">
                                <table>
                                    <tr>
                                        <th>Utilisateur</th>                                        
                                        <th>Date envoi</th>
                                        <th>Objet</th>
                                        <th>Fichier</th>
                                    </tr>
                                    <?
                                    foreach ($orderinfo["history"] as $odh) {
                                        if (empty($odh["supplier_name"])) {
                                            ?>
                                            <tr>
                                                <td><?= $odh["prenom"] ?></td>
                                                <td><?= strftime("%a %d %b %y %T", strtotime($odh["date_add"])) ?></td>
                                                <td><?= $odh["category"] ?></td>  
                                                <td>

                                                    <?
                                                    if ($odh["bdc_filename"]) {
                                                        if ($odh["category"] == "roadmap")
                                                            $folder = "roadmap";
                                                        if ($odh["category"] == "bl")
                                                            $folder = "bon_de_livraison";
                                                        ?>
                                                        <a href="ressources/<?= $folder ?>/<?= $odh["bdc_filename"] ?>.pdf" target="_blank">Download</a>
                                                        <?
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
                <form method="post">
                    <input type="hidden" name="id_order" value="<?= $oid ?>">

                    <h2>Bloc note</h2>
                    <div class="col-xs-5 ">
                        <table class="well">
                            <?
                            if ($orderinfo["notes"])
                                foreach ($orderinfo["notes"] as $k => $on) {
                                    ?>
                                    <tr>
                                        <td><?= strftime("%a %d %b %y %T", strtotime($on["date_add"])) ?></td>
                                        <td><?= $on["prenom"] ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><?= $on["message"] ?></td>
                                    </tr>

                                    <?
                                }
                            ?>
                        </table>

                        <textarea name="order_note" cols="20" rows="5" ></textarea>
                        <p>
                            <button type="submit" name="add_notes" class="btn btn-lg btn-default btn-block ">Ajouter note</button> 
                        </p>

                    </div>

                </form>
            </div>
        </div>        
        <?
    }
    ?>


    <div class = "row">
        <div class = "col-xs-2">
            <?
            foreach ($orderStates as $orderState) {
                ?>
                <div class="row">
                    <div class="alert-<?= $orderState["id_statut"] ?>" >
                        <?= $orderState["title"] ?>
                    </div>
                </div>
                <?
            }
            ?>
        </div>
    </div>

</div>

<script>
    $(function() {
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd"
        });
    });

    $(".supplier").change(function() {
        $("#btn_send_supplier").attr("disabled", "disabled");
        $("#idmsg").text("Un fournisseur a été modifié,vous devez cliquer sur 'Modifier' pour valider les changements.")
        $("#idmsg").addClass("alert alert-info");
    })
</script>
