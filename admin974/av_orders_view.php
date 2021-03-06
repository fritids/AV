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
require('../classes/sms.inc.php');

define("ATTENTE_CHEQUE", 1);
define("PAIEMENT_ACCEPTE", 2);
define("PREPARATION_EN_COURS", 3);
define("EN_COURS_LIVRAISON", 4);
define("LIVREE", 5);
define("ANNULE", 6);
define("REMBOURSE", 7);
define("ERREUR_PAIEMENT", 8);
define("ATTENTE_VIREMENT", 10);

define("PRODUIT_COMMANDE_FOURNISSEUR", 16);
define("PRODUIT_CASSE", 21);
define("PRODUIT_SAV", 22);
define("PRODUIT_REMBOURSE", 23);
define("PRODUIT_ANNULE_CLIENT", 24);


//SMS

$user_login = 'pei73hyl8trvtivx8rduvg2p@sms-accounts.com';
$api_key = 'PLYvbMEbIhW5zfnQy0Xi';
$sms_type = QUALITE_PRO; // ou encore QUALITE_PRO
$sms_mode = INSTANTANE; // ou encore DIFFERE
$sms_sender = 'ALLOVITRES';


$smarty = new Smarty;
$smarty->caching = 0;
//$smarty->error_reporting = E_ALL & ~E_NOTICE;
$smarty->setTemplateDir(array('../templates', '../templates/mails/', '../templates/mails/admin', '../templates/pdf', '../templates/pdf/admin', '../templates/pdf/front'));

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
$orderStates = $db->where("id_level", 0)
        ->get("av_order_status");
$productStates = $db->rawQuery("select * from av_order_status where id_level=1 and id_statut not in (23)");

$cid = $r[0]["id_customer"];

//contact
$customer_info = getCustomerDetail($cid);

//commande 
$orderinfo = getOrderInfos($oid);

$suppliers = $db->get("av_supplier");
$suppliersWarehouse = getSupplierWarehouses();
$warehouses = $db->get("av_warehouse");
//$suppliers = getSupplierZone($orderinfo["address"]["delivery"]["warehouse"]["id_zone"]);
//print_r($orderinfo);
//paiementtoi t
$orderPayment = getOrderPayment($orderinfo["id_order"]);

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

$orderLocked = false;
$orderRefund = false;

/* Update des combobox */
if (!isset($_POST["order_action_refund"]))
    if (isset($_POST) && !empty($_POST) && isset($_POST["order_action_modify"]) || isset($_POST["supplier_date_delivery"]) || isset($_POST["order_state"])) {

        if (isset($_POST["id_supplier"]) && !empty($_POST["id_supplier"]))
            if (is_array($_POST["id_supplier"])) {
                foreach ($_POST["id_supplier"] as $id => $supplier) {
                    $r = $db->where("id_order_detail", $id)
                            ->update("av_order_detail", array("id_supplier" => $supplier));
                    addLog(array("tabs" => "av_order_detail",
                        "rowkey" => $id,
                        "col" => "id_supplier",
                        "operation" => "update",
                        "oldval" => '',
                        "newval" => $supplier
                    ));
                }
            } else {
                foreach ($orderinfo["details"] as $od) {
                    $r = $db->where("id_order_detail", $od["id_order_detail"])
                            ->update("av_order_detail", array("id_supplier" => $_POST["id_supplier"]));

                    addLog(array("tabs" => "av_order_detail",
                        "rowkey" => $od["id_order_detail"],
                        "col" => "id_supplier",
                        "operation" => "update",
                        "oldval" => $od["id_supplier"],
                        "newval" => $_POST["id_supplier"]
                    ));
                }
            }
        if (isset($_POST["id_supplier_warehouse"]) && !empty($_POST["id_supplier_warehouse"]))
            if (is_array($_POST["id_supplier_warehouse"])) {
                foreach ($_POST["id_supplier_warehouse"] as $id => $warehouse) {
                    $r = $db->where("id_order_detail", $id)
                            ->update("av_order_detail", array("id_supplier_warehouse" => $warehouse));
                    addLog(array("tabs" => "av_order_detail",
                        "rowkey" => $id,
                        "col" => "id_supplier_warehouse",
                        "operation" => "update",
                        "oldval" => '',
                        "newval" => $warehouse
                    ));

//degreve du stock
                    if (updQuantityWarehouse($id))
                        @$updated["text"] .= "Le stock a été mise à jour<br>";
                }
            } else {
                foreach ($orderinfo["details"] as $od) {
                    $r = $db->where("id_order_detail", $od["id_order_detail"])
                            ->update("av_order_detail", array("id_supplier_warehouse" => $_POST["id_supplier_warehouse"]));

                    addLog(array("tabs" => "av_order_detail",
                        "rowkey" => $od["id_order_detail"],
                        "col" => "id_supplier_warehouse",
                        "operation" => "update",
                        "oldval" => $od["id_supplier_warehouse"],
                        "newval" => $_POST["id_supplier_warehouse"]
                    ));
                }
            }
        if (isset($_POST["supplier_date_delivery"]) && !empty($_POST["supplier_date_delivery"]))
            if (is_array($_POST["supplier_date_delivery"])) {
                foreach ($_POST["supplier_date_delivery"] as $id => $date_delivery) {
                    if (!empty($date_delivery))
                        $r = $db->where("id_order_detail", $id)
                                ->update("av_order_detail", array("supplier_date_delivery" => $date_delivery));
                    addLog(array("tabs" => "av_order_detail",
                        "rowkey" => $id,
                        "col" => "supplier_date_delivery",
                        "operation" => "update",
                        "oldval" => '',
                        "newval" => $date_delivery
                    ));
                }
            }else {
                foreach ($orderinfo["details"] as $od) {
                    $r = $db->where("id_order_detail", $od["id_order_detail"])
                            ->update("av_order_detail", array("supplier_date_delivery" => $_POST["supplier_date_delivery"]));
                    addLog(array("tabs" => "av_order_detail",
                        "rowkey" => $od["id_order_detail"],
                        "col" => "supplier_date_delivery",
                        "operation" => "update",
                        "oldval" => $od["supplier_date_delivery"],
                        "newval" => $_POST["supplier_date_delivery"]
                    ));
                }
            }
        if (isset($_POST["product_current_state"]) && !empty($_POST["product_current_state"]))
            if (is_array($_POST["product_current_state"])) {
                foreach ($_POST["product_current_state"] as $id => $state) {
                    $r = $db->where("id_order_detail", $id)
                            ->update("av_order_detail", array("product_current_state" => $state));
//casse ou SAV
                    if ($state == PRODUIT_CASSE || $state == PRODUIT_SAV || $state == PRODUIT_ANNULE_CLIENT) {
                        $r = $db->where("id_order_detail", $id)
                                ->delete("av_tournee");

                        $r = $db->where("id_order_detail", $id)
                                ->update("av_order_detail", array("supplier_date_delivery" => null/* , "id_warehouse" => null */));

                        $r = $db->where("id_order", $oid)
                                ->update("av_orders", array("current_state" => PREPARATION_EN_COURS, "date_upd" => date("Y-m-d H:i:s")));

                        addLog(array("tabs" => "mv_orders",
                            "rowkey" => $oid,
                            "col" => "current_state",
                            "operation" => "update",
                            "oldval" => '',
                            "newval" => PREPARATION_EN_COURS
                        ));
                    }

                    addLog(array("tabs" => "av_order_detail",
                        "rowkey" => $id,
                        "col" => "product_current_state",
                        "operation" => "update",
                        "oldval" => '',
                        "newval" => $state
                    ));
                }
            } else {
                foreach ($orderinfo["details"] as $od) {
                    $r = $db->where("id_order_detail", $od["id_order_detail"])
                            ->update("av_order_detail", array("product_current_state" => $_POST["product_current_state"]));
//casse ou SAV
                    if ($_POST["product_current_state"] == PRODUIT_CASSE || $_POST["product_current_state"] == PRODUIT_SAV || $_POST["product_current_state"] == PRODUIT_ANNULE_CLIENT) {
                        $r = $db->where("id_order_detail", $od["id_order_detail"])
                                ->delete(("av_tournee"));

                        $r = $db->where("id_order_detail", $od["id_order_detail"])
                                ->update("av_order_detail", array("supplier_date_delivery" => null/* , "id_warehouse" => null */));

                        $r = $db->where("id_order", $oid)
                                ->update("av_orders", array("current_state" => PREPARATION_EN_COURS, "date_upd" => date("Y-m-d H:i:s")));

                        addLog(array("tabs" => "mv_orders",
                            "rowkey" => $oid,
                            "col" => "current_state",
                            "operation" => "update",
                            "oldval" => '',
                            "newval" => PREPARATION_EN_COURS
                        ));
                    }

                    addLog(array("tabs" => "av_order_detail",
                        "rowkey" => $od["id_order_detail"],
                        "col" => "supplier_date_delivery",
                        "operation" => "update",
                        "oldval" => $od["product_current_state"],
                        "newval" => $_POST["product_current_state"]
                    ));
                }
            }
        if (isset($_POST["product_supplier_comment"]) && !empty($_POST["product_supplier_comment"]))
            if (is_array($_POST["product_supplier_comment"])) {
                foreach ($_POST["product_supplier_comment"] as $id => $product_supplier_comment) {

                    $r = $db->where("id_order_detail", $id)
                            ->update("av_order_detail", array("product_supplier_comment" => $product_supplier_comment));


                    addLog(array("tabs" => "av_order_detail",
                        "rowkey" => $id,
                        "col" => "product_supplier_comment",
                        "operation" => "update",
                        "oldval" => '',
                        "newval" => $product_supplier_comment
                    ));
                }
            }

        @$updated["text"] .= "Modification a été effectuée.<br>";

//suite aux update on recharge les infos    
        $orderinfo = getOrderInfos($oid);
    }

if ($orderinfo["LIV_GLOBAL_INFO"] == 5) {
    $r = $db->where("id_order", $oid)
            ->update("av_orders", array("current_state" => LIVREE, "date_upd" => date("Y-m-d H:i:s")));
    if ($r) {
        @$updated["text"] .= "La commande a été passé en statut LIVREE.<br>";
        $orderinfo = getOrderInfos($oid);
    }
}


if (isset($_POST) && !empty($_POST["order_action_send_supplier"])) {

    $orderSuppliers = getOrdersDetailSupplier($oid);
    $tmp = "";
    foreach ($orderSuppliers as $k => $orderSupplier) {

        $id_supplier_warehouse = $orderSupplier["id_supplier_warehouse"];

        $mail->ClearAllRecipients();
        $mail->ClearAttachments();
        $mail->SetFrom($confmail["from"]);
        foreach ($monitoringEmails as $bccer) {
            $mail->AddbCC($bccer);
        }
        $mail->AddAddress($orderSupplier["email"]);
        $mail->Subject = "Bon de commande: #" . $orderinfo["id_order"];

        $orderDetailSupplier = getUserOrdersDetail($oid, $id_supplier_warehouse);
        $orderDetailSupplierXLS = $orderDetailSupplier;


        if (!empty($orderDetailSupplier)) {
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

//print_r($orderinfo);

            $smarty->assign("supplier", $orderSupplier);
            $smarty->assign("warehouse", getWarehouseInfos($id_supplier_warehouse));
            $smarty->assign("orderinfo", $orderinfo);
            $smarty->assign("user_email", $_SESSION["email"]);
            $smarty->assign("orderdetail", $orderDetailSupplier);
            $mail_body = $smarty->fetch('admin_supplier_ask_order.tpl');
            $bdc_pdf_body = $smarty->fetch('admin_bon_commande.tpl');
            $pdf->writeHTML($bdc_pdf_body, true, false, true, false, '');
            if ($orderinfo["nb_custom_product"] > 0) {
                $annexe_body = $smarty->fetch('front_annexe.tpl');
                $pdf->AddPage('P', 'A4');
                $pdf->writeHTML($annexe_body, true, false, true, false, '');
            }

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
//$mail->AddStringAttachment(utf8_encode($mail_body), $bdc_commande_filename . ".html");
            $mail->MsgHTML($mail_body);

            if ($mail->Send()) {
                $tmp .= "<li> " . $orderSupplier["name"] . " ( " . count($orderDetailSupplier) . " article(s) commandé(s) )</li>";

                $params = array(PRODUIT_COMMANDE_FOURNISSEUR, $oid, $orderSupplier["id_supplier"]);

                $q = "update av_order_detail 
                set product_current_state = " . PRODUIT_COMMANDE_FOURNISSEUR . "
                where IFNULL(product_current_state,0) in (0,21, 22) 
                and id_order = " . $oid . "                 
                and id_supplier_warehouse =  " . $id_supplier_warehouse;

                mysql_query($q);

                foreach ($orderDetailSupplier as $k => $ods) {
                    addLog(array("tabs" => "av_order_detail",
                        "rowkey" => $ods["id_order_detail"],
                        "col" => "product_current_state",
                        "operation" => "update",
                        "oldval" => '',
                        "newval" => PRODUIT_COMMANDE_FOURNISSEUR
                    ));
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
    }
    if ($tmp)
        $updated["text"] = "Bon de commande envoyé au(x) founisseur(s) suivant(s): <ul>" . $tmp . "</ul>";
}


//maj status order  
if ((isset($_POST["current_state"]) && !empty($_POST["current_state"])) || ($orderinfo["COMMANDE_INFO"] == 5 && $orderinfo["current_state"] == PAIEMENT_ACCEPTE)) {

    if (isset($_POST["current_state"]) && !empty($_POST["current_state"]))
        $new_state = $_POST["current_state"];

    if ($orderinfo["COMMANDE_INFO"] == 5 && $orderinfo["current_state"] == PAIEMENT_ACCEPTE)
        $new_state = PREPARATION_EN_COURS;

    $r = $db->where("id_order", $oid)
            ->update("av_orders", array("current_state" => $new_state, "date_upd" => date("Y-m-d H:i:s")));

    if ($new_state == PAIEMENT_ACCEPTE) {
        createInvoice($oid);
    }

    addLog(array("tabs" => "mv_orders",
        "rowkey" => $orderinfo["id_order"],
        "col" => "current_state",
        "operation" => "update",
        "oldval" => $orderinfo["current_state"],
        "newval" => $new_state
    ));

    if ($r) {
        switch ($new_state) {
            case 7:
                $order_mail_subject = "Allovitre - votre commande #" . $orderinfo["id_order"] . " a été remboursé";
                $order_mail_from = "service.commercial@allovitres.com";
                $order_mail_tpl = "notif_order_refund";
                $order_sms_tpl = "notif_sms_refund";
                break;
            case 5:
                $order_sms_text = "Bonjour, Votre commande vous a été livrée. Nous espérons avoir répondu convenablement à vos attentes.A bientôt sur notre site allovitres.com";
                $order_sms_tpl = "notif_sms_delivered";
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
                $order_sms_text = "Bonjour, Votre commande est en phase de fabrication. Vous recevrez sous 7 à 15 j ouvrés un mail et sms pour la livraison. L'équipe Allovitres.";
                break;


            default:
                $order_mail_subject = "";
                $order_mail_from = "";
                $order_mail_tpl = "";
                break;
        }

        if ($orderinfo["alert_sms"] == 1) {

            if (isset($order_sms_text) && $order_sms_text != '') {

                $sms = new SMS();
                $sms->set_user_login($user_login);
                $sms->set_api_key($api_key);
                $sms->set_sms_mode($sms_mode);
                $sms->set_sms_text($order_sms_text);
                $sms->set_sms_recipients(array($orderinfo["alert_sms_phone"]));
                $sms->set_sms_type($sms_type);
                $sms->set_sms_sender($sms_sender);
                $sms->send();

                @$updated["text"] .= "un SMS de notification a été envoyé au " . $orderinfo["alert_sms_phone"] . "<br>";

//log
                $param = array(
                    "id_order" => $orderinfo["id_order"],
                    "id_user" => $_SESSION["user_id"],
                    "category" => $order_sms_tpl,
                );
                $r = $db->insert("av_order_bdc", $param);
            }
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
                @$updated["text"] .= "un mail de notification a été envoyé à l'adresse " . $orderinfo["customer"]["email"] . "<br>";
                $param = array(
                    "id_order" => $orderinfo["id_order"],
                    "id_user" => $_SESSION["user_id"],
                    "category" => $order_mail_tpl,
                );
                $r = $db->insert("av_order_bdc", $param);
            }
        }
        $orderinfo = getOrderInfos($oid);
    }
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

if ((isset($new_state) && $new_state == REMBOURSE) || isset($_POST["order_action_refund"])) {
    $RefundPayment = $orderinfo["payment"];
    $RefundComment = "";
    $refundDetails = array();
    $RefundShipping = 0;
    $RefundSuppl = 0;
    if (isset($_POST["RefundPayment"]) && $_POST["RefundPayment"] != "") {
        $RefundPayment = $_POST["RefundPayment"];
    }
    if (isset($_POST["RefundComment"]) && $_POST["RefundComment"] != "") {
        $RefundComment = $_POST["RefundComment"];
    }
    if (isset($_POST["RefundDetails"]) && $_POST["RefundDetails"] != "") {
        $refundDetails = $_POST["RefundDetails"];
    }
    if (isset($_POST["RefundShipping"]) && $_POST["RefundShipping"] != "") {
        $RefundShipping = $_POST["RefundShipping"];
    }
    if (isset($_POST["RefundSuppl"]) && $_POST["RefundSuppl"] != "") {
        $RefundSuppl = $_POST["RefundSuppl"];
    }

    refundOrder($oid, $RefundPayment, $RefundComment, $RefundShipping, $RefundSuppl, $refundDetails);
    $orderinfo = getOrderInfos($oid);
}
?>


<?
if ($orderinfo["current_state"] != ATTENTE_CHEQUE && $orderinfo["current_state"] != LIVREE && $orderinfo["current_state"] != ANNULE && $orderinfo["current_state"] != REMBOURSE && $orderinfo["current_state"] != ERREUR_PAIEMENT && $orderinfo["current_state"] != ATTENTE_VIREMENT && $orderinfo["current_state"] != 11)
    $orderLocked = true;
if ($orderinfo["current_state"] != REMBOURSE && $orderinfo["current_state"] != ANNULE )
    $orderRefund = true;


if ($orderinfo) {
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

            <div class="col-xs-3">
                <div class="alert">
                    <b class="h3">#<?= $oid ?></b>   
                </div>
            </div>  
            <div class="col-xs-2">
                <div class="alert">
                    <form action="av_download_pdf.php?order" method="post" target="blank">
                        <input type="hidden" value="<?= $oid ?>" name="id_order">
                        <button class="btn btn-default" data-toggle="tooltip" title="Télécharger la facture au format PDF"><span class="glyphicon glyphicon-floppy-save"></span></button>
                    </form> 
                </div>
            </div>
            <div class="col-xs-3">
                <table class="table table-condensed  table-bordered">
                    <tr class="text-center">
                        <th class="text-center" style="width: 50px">COMM.</th>
                        <th class="text-center" style="width: 50px">ARC</th>
                        <th class="text-center" style="width: 50px">RECU</th>
                        <th class="text-center" style="width: 50px">LIV. PROG</th>
                        <th class="text-center" style="width: 50px">LIVREE</th>
                    </tr>
                    <tr>
                        <td class="text-center alert-<?= $orderinfo["COMMANDE_INFO"] ?>">
                            <?
                            switch ($orderinfo["COMMANDE_INFO"]) {
                                case 5:
                                    echo '<span class="glyphicon glyphicon-ok"></span>';
                                    break;
                                case 6:
                                    echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                                    break;
                                case 8:
                                    echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                                    break;
                                default:
                            }
                            ?>                        
                        </td>
                        <td class="text-center alert-<?= $orderinfo["ARC_INFO"] ?>">
                            <?
                            switch ($orderinfo["ARC_INFO"]) {
                                case 5:
                                    echo '<span class="glyphicon glyphicon-ok"></span>';
                                    break;
                                case 6:
                                    echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                                    break;
                                case 8:
                                    echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                                    break;
                                default:
                            }
                            ?>                        
                        </td>
                        <td class="text-center alert-<?= $orderinfo["RECU_INFO"] ?>">
                            <?
                            switch ($orderinfo["RECU_INFO"]) {
                                case 5:
                                    echo '<span class="glyphicon glyphicon-ok"></span>';
                                    break;
                                case 6:
                                    echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                                    break;
                                case 8:
                                    echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                                    break;
                                default:
                            }
                            ?>                        
                        </td>                   
                        <td class="text-center alert-<?= $orderinfo["LIV_INFO"] ?>">
                            <?
                            switch ($orderinfo["LIV_INFO"]) {
                                case 5:
                                    echo '<span class="glyphicon glyphicon-ok"></span>';
                                    break;
                                case 6:
                                    echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                                    break;
                                case 8:
                                    echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                                    break;
                                default:
                            }
                            ?>                        
                        </td>                   
                        <td class="text-center alert-<?= $orderinfo["LIV_GLOBAL_INFO"] ?>">
                            <?
                            switch ($orderinfo["LIV_GLOBAL_INFO"]) {
                                case 5:
                                    echo '<span class="glyphicon glyphicon-ok"></span>';
                                    break;
                                case 6:
                                    echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                                    break;
                                case 8:
                                    echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                                    break;
                                default:
                            }
                            ?>                        
                        </td>                   

                    </tr>
                </table>

            </div>  

            <div class="col-xs-4">
                <div class="alert alert-<?= $orderinfo["current_state"] ?>">
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

        <div class="row infoGeneral">

            <div class="col-xs-3">
                <div class="panel panel-default">
                    <div class="panel-heading">Contact <div class="pull-right">
                            <a href="av_customer_view.php?id_customer=<?= $customer_info["id_customer"] ?>" data-toggle="tooltip" title="Consulter la fiche client"><span class="glyphicon glyphicon-user"></span></a>
                            <a href="av_customer.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $customer_info["id_customer"] ?>" data-toggle="tooltip" title="Modifier les infos personnelles"><span class="glyphicon glyphicon-edit"></span></a>
                        </div>
                    </div>     
                    <div class="panel-body">
                        <table class="table table-condensed">
                            <tr>
                                <td>Nom</td>
                                <td><?= @$customer_info["firstname"] ?></td>
                            </tr>
                            <tr>
                                <td>Prénom</td>
                                <td><?= @$customer_info["lastname"] ?></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td><?= @$customer_info["email"] ?></td>
                            </tr>
                            <tr>
                                <td>Type</td>
                                <td><?= (@$customer_info["customer_group"] == 1) ? "PRO" : "Normal"; ?></td>
                            </tr>
                        </table>
                    </div>            
                </div>            
            </div>

            <div class="col-xs-3">
                <div class="panel panel-default">
                    <div class="panel-heading">Adresse Livraison <div class="pull-right"><a href="av_address.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $orderinfo["address"]["delivery"]["id_address"] ?>" data-toggle="tooltip" title="Modifier l'adresse de livraison"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                    <div class="panel-body">
                        <span class="glyphicon glyphicon-phone-alt" ></span> <?= $orderinfo["address"]["delivery"]["phone"] ?><br>
                        <span class="glyphicon glyphicon-phone" ></span> <?= $orderinfo["address"]["delivery"]["phone_mobile"] ?> <br>
                        <?= $orderinfo["address"]["delivery"]["address1"] ?><br>
                        <?= $orderinfo["address"]["delivery"]["address2"] ?><br>
                        <?= $orderinfo["address"]["delivery"]["postcode"] ?> <?= $orderinfo["address"]["delivery"]["city"] ?>
                        <table class="table table-condensed" style="margin-bottom: 0px">
                            <tr>
                                <td class="text-center"><?= $orderinfo["address"]["delivery"]["zone"] ?></td>                            
                            </tr>
                        </table>
                    </div> 

                </div>
            </div>
            <div class="col-xs-3">
                <div class="panel panel-default">
                    <div class="panel-heading">Adresse Facturation <div class="pull-right"><a href="av_address.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $orderinfo["address"]["invoice"]["id_address"] ?>" data-toggle="tooltip" title="Modifier l'adresse de facturation"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                    <div class="panel-body">
                        <span class="glyphicon glyphicon-phone-alt" ></span> <?= $orderinfo["address"]["invoice"]["phone"] ?> <br>
                        <span class="glyphicon glyphicon-phone" ></span> <?= $orderinfo["address"]["invoice"]["phone_mobile"] ?> <br>
                        <?= $orderinfo["address"]["invoice"]["address1"] ?><br>
                        <?= $orderinfo["address"]["invoice"]["address2"] ?><br>
                        <?= $orderinfo["address"]["invoice"]["postcode"] ?> <?= $orderinfo["address"]["invoice"]["city"] ?><br>
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

        <div class="row infoGeneral">
            <div class="col-xs-3">
                <div class="panel panel-default">
                    <div class="panel-heading">CO#<?= $orderinfo["id_order"] ?> <div class="pull-right">FA#<?= $orderinfo["invoice"]["ref_invoice"] ?> <a href="av_orders.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $orderinfo["id_order"] ?>"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                    <div class="panel-body">

                        <table class="table table-condensed">                           
                            <tr>
                                <td>Création</td>
                                <td><?= strftime("%a %d %b %y %T", strtotime($orderinfo["date_add"])) ?></td>
                            </tr>
                            <tr>
                                <td>Suivi SMS</td>
                                <td><?= ($orderinfo["alert_sms"] == 1) ? "<b>Oui ( " . $orderinfo["alert_sms_phone"] . " )</b>" : "Non" ?></td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td><?= $orderinfo["total_paid"] ?>€ <?= ($orderinfo["total_discount"] > 0) ? "<font color='red'>dont réduction: " . $orderinfo["total_discount"] . "€ " . $orderinfo["order_voucher"] . "</font>" : "" ?></td>
                            </tr>
                            <?
                            if (isset($orderinfo["refund"])) {
                                foreach ($orderinfo["refund"] as $k => $refund) {
                                    ?>
                                    <tr>
                                        <? /* <td><a href="av_order_refund.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $refund["id_order_refund"] ?>" target="_blank" data-toggle="tooltip" title="<?= $refund["refund_comment"] ?>">Rmbt#<?= $k + 1 ?></a></td> */ ?>
                                        <td>Rmbt#<?= $k + 1 ?></td>
                                        <td><?= $refund["total_refund"] ?>€ dont fdp <?= $refund["total_shipping"] ?><br><?= $refund["payment"] ?> <?= strftime("%a %d %b %y %T", strtotime($refund["date_refund"])) ?></td>
                                    </tr>
                                    <?
                                }
                            }
                            ?>
                        </table>
                    </div> 
                </div> 
            </div>
            <div class="col-xs-3">
                <div class="panel panel-default">
                    <div class="panel-heading">Paiement </div>
                    <div class="panel-body">
                        <table class="table table-condensed">
                            <tr>
                                <td>Mode</td>
                                <td><?= $orderinfo["payment"] ?></td>
                            </tr>
                            <tr>
                                <td>Date paiement:</td>
                                <td><?= (!empty($orderPayment["date_add"])) ? strftime("%a %d %b %y %T", strtotime($orderPayment["date_add"])) : "" ?></td>
                            </tr>
                            <tr>
                                <td>Montant</td>
                                <td><?= $orderPayment["amount"] ?> €</td>
                            </tr>
                        </table>
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
                            <p style="display: none">
                                Fournisseur:
                                <select name="id_supplier" class="pme-input-0" disabled="disabled">
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
                            <?
                            if ($orderLocked) {
                                ?>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-warning btn-block">Ok</button>
                                </p>
                                <?
                            }
                            ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?
        if ($orderRefund) {
            ?>
            <div class="row">
                <div class="col-xs-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">Remboursement Spécifique</div>
                        <div class="panel-body">
                            <form action="av_orders_view.php" method="post" class="form-horizontal">
                                <input type="hidden" name="current_state" value="7">
                                <input type="hidden" name="id_order" value="<?= $oid ?>">
                                <select name="RefundPayment" class="form-control" required="required" >
                                    <option value="">Mode de paiement</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Virement bancaire">Virement bancaire</option>
                                    <option value="Paypal">Paypal</option>
                                </select>
                                <textarea name="RefundComment" class="form-control mceNoEditor" placeholder="Commentaire interne"></textarea>

                                <input type="submit" name="refundTotal" value="Remboursement total" class="btn btn-block btn-danger">
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <?
        }
        ?>
        <?
        if (!empty($orderinfo["details"])) {
            ?>
            <div class="row">
                <div class="col-xs-12">
                    <h2>Produits</h2>
                    <ul class="nav nav-pills">
                        <li class="active"><a href="#Produits" data-toggle="tab">Produits</a></li>
                        <li><a href="#bdc" data-toggle="tab">Bon de commande</a></li>
                        <li><a href="#history_mail" data-toggle="tab">Historique e-mail</a></li>
                        <li><a href="#history_mod" data-toggle="tab">Historique Modifications</a></li>
                    </ul>
                    <form method="post">
                        <div class="tab-content">
                            <div class="tab-pane active" id="Produits">
                                <input type="hidden" name="id_order" value="<?= $oid ?>">

                                <table class="table table-bordered table-condensed col-xs-12" id="tab_devis">
                                    <tr>
                                        <th colspan="5" class="text-center">PRODUIT</th>
                                        <th colspan="2" class="text-center">FOURNISSEUR</th>
                                        <th colspan="7" class="text-center">LIVRAISON</th>                          
                                    </tr>
                                    <tr>
                                        <?/*<th>Rmbt</th>*/?>
                                        <th>Produit</th>
                                        <th>Long x Larg</th>
                                        <th>Qte</th>
                                        <th>Prix TTC</th>
                                        <th>Statuts</th>
                                        <th>Fournisseur</th>
                                        <th>Date Livraison</th>                                    
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
                                        $isFournisseurOk = ($od["id_supplier_warehouse"] != '' ) ? true : false;
                                        ?>
                                        <tr id="id0">
                                            <?/*<td>
                                                <?
                                                if ($od["product_current_state"] != 23) {
                                                  ?>
                                                  <input type="checkbox" name="RefundDetails[]" value="<?= $od["id_order_detail"] ?>" class="RefundDetails">
                                                  <?
                                                 
                                                }
                                                ?>
                                            </td>
                                             * 
                                             */?>

                                            <td nowrap> 
                                                <?= $od["product_name"] ?> <br>
                                                <?
                                                foreach ($od["attributes"] as $attribute) {
                                                    echo " - " . $attribute["attribute_name"] . ": " . $attribute["attribute_value"] . "<br>";
                                                }
                                                ?>
                                                <font color="red">
                                                <?
                                                foreach ($od["custom"] as $custom) {
                                                    echo " - " . $custom["main_item_name"];
                                                    foreach ($custom["sub_item"] as $sub_item) {
                                                        echo " - " . $sub_item["sub_item_name"] . "<br>";
                                                        foreach ($sub_item["item_values"] as $item_value) {
                                                            echo $item_value["item_value_name"] . ": " . $item_value["custom_value"] . "<br>";
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
                                            <td nowrap>
                                                <?= $od["total_price_tax_incl"] ?> €
                                                <?= ($od["discount"] > 0) ? "<br><font color='red'>dont réduction: " . $od["discount"] . "€ " . $od["voucher_code"] . "</font>" : "" ?><br>
            <?= $od["product_quantity"] * $od["product_weight"] ?> Kg
                                            </td>
                                            <td>
                                                <?
                                                if ($od["product_current_state"] != 20 && ($od["product_current_state"] != 23)) {
                                                    ?>
                                                    <select style="width: 150px"  name="product_current_state[<?= $od["id_order_detail"] ?>]" class="product_current_state form-control input-sm">
                                                        <option value="">--</option>
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

                                                    <input type="text" value="<?= $od["product_supplier_comment"] ?>" name="product_supplier_comment[<?= $od["id_order_detail"] ?>]" class="form-control input-sm product_supplier_comment" maxlength="128" placeholder="Comm. SAV/CASSE">

                                                    <?
                                                } else {
                                                    echo $od["product_state_label"];
                                                }
                                                ?>                                                
                                            </td>
                                            <td>
                                                <?
                                                if ($od["product_current_state"] != 20 && ($od["product_current_state"] != 23)) {
                                                    ?>
                                                    <select style="width: 120px"  name="id_warehouse[<?= $od["id_order_detail"] ?>]" id="warehouse_<?= $od["id_order_detail"] ?>" class="form-control input-sm warehouse">
                                                        <option value="">--</option>
                                                        <?
                                                        foreach ($warehouses as $warehouse) {
                                                            ?>
                                                            <option value="<?= $warehouse["id_warehouse"] ?>"
                                                                    <?= ($od["id_warehouse"] == $warehouse["id_warehouse"] || (($orderinfo["address"]["delivery"]["warehouse"]["id_warehouse"] == $warehouse["id_warehouse"]) && $od["id_warehouse"] == '')) ? "selected" : "" ?>
                                                                    ><?= $warehouse["name"] ?> </option>
                                                                    <?
                                                                }
                                                                ?>
                                                    </select>                                                    
                                                    <select name="id_supplier_warehouse[<?= $od["id_order_detail"] ?>]" id="supplier_<?= $od["id_order_detail"] ?>" class="form-control input-sm supplier">
                                                        <option value="">--</option>
                                                        <?
                                                        foreach ($suppliersWarehouse as $supplier) {
                                                            ?>
                                                            <option class="<?= $supplier["id_warehouse"] ?>" value="<?= $supplier["id_supplier_warehouse"] ?>"
                                                                    <?= ($od["id_supplier_warehouse"] == $supplier["id_supplier_warehouse"]) ? "selected" : "" ?>
                                                                    ><?= $supplier["supplier_name"] ?> </option>
                                                                    <?
                                                                }
                                                                ?>
                                                    </select>

                                                    <?
                                                } else {
                                                    echo getSupplierName($od["id_supplier_warehouse"]) . "<br>";
                                                    echo getWarehouseName($od["id_supplier_warehouse"]);
                                                }
                                                ?>

                                            </td>
                                            <td>
                                                <?
                                                if ($od["product_current_state"] != 20 && ($od["product_current_state"] != 23)) {
                                                    ?>
                                                    <input type="text" style="width: 100px" class="datepicker form-control input-sm " value="<?= @$od["supplier_date_delivery"] ?>" name="supplier_date_delivery[<?= $od["id_order_detail"] ?>]" placeholder="Date ARC"> 
                                                    <?
                                                } else {
                                                    echo $od["supplier_date_delivery"];
                                                }
                                                ?>
                                            </td>                                        
                                            <td><?= ( $t["date_livraison"]) ? strftime("%a %d %b%y", strtotime($t["date_livraison"])) : ""; ?></td>                                
                                            <td><?= $t["horaire"] ?></td>
                                            <td><?= $t["comment1"] ?></td>
                                            <td><?= $t["comment2"] ?></td>
                                            <td><?= $t["comment3"] ?></td>
                                            <td>
                                                <?
                                                if ($od["product_quantity"] > 1 && !($od["product_current_state"] == 20 || $od["product_current_state"] == 23)) {
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
                                                <?
                                                if ($t["date_livraison"] && !($od["product_current_state"] == 20 || $od["product_current_state"] == 23)) {
                                                    ?>
                                                    <button type="button" name="delProduitTruck" value="<?= $t["id_order_detail"] ?>" >
                                                        Retirer du camion<br>
                                                    <?= $t["truck_name"] ?>
                                                    </button>
                                                    <?
                                                }
                                                ?>
                                            </td>

                                        </tr>
                                        <script>
                                            $("#supplier_<?= $od["id_order_detail"] ?>").chainedTo("#warehouse_<?= $od["id_order_detail"] ?>");
                                        </script>
                                        <?
                                    }
                                    ?>
                                </table>   
                                <div class="col-xs-3" id="refundPartial" style="display: none">
                                    <select name="RefundPayment" class="form-control input" >
                                        <option value="">Mode de paiement</option>
                                        <option value="Chèque">Chèque</option>
                                        <option value="Virement bancaire">Virement bancaire</option>
                                        <option value="Paypal">Paypal</option>
                                    </select>

                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <input type="checkbox" name="RefundShipping" data-toggle="tooltip" title="Imputer les frais suppl. en tant que frais de port?" value="1">   
                                        </span>    
                                        <input type="text" name="RefundSuppl" class="form-control" placeholder="Montant supplémentaire" > 
                                    </div>

                                    <textarea name="RefundComment" class="form-control mceNoEditor input" placeholder="Commentaire interne"></textarea>

                                    <input type="submit" name="order_action_refund" value="Remboursement partiel" class="btn btn-danger btn-block ">

                                </div>
                                <div class="col-xs-3 pull-right">
                                    <div class="col-xs-12" >
                                        <p id="idmsg"></p>
                                    </div>
                                    <div class="col-xs-12" >
                                        <?
                                        if ($orderLocked) {
                                            ?>
                                            <p>
                                                <input type="submit" name="order_action_modify" value="Modifier" class="btn-lg btn-warning btn-block">
                                            </p>

                                            <?
                                        }
                                        ?>
                                        <?
                                        if ($isFournisseurOk) {
                                            ?>
                                            <p>
                                                <input type="submit" name="order_action_send_supplier" value="Envoi fournisseur"  class="btn-lg btn-block btn-primary" id="btn_send_supplier" data-loading-text="Envoi en cours...">
                                            </p>
                                            <?
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>                           
                            <div class="clearfix"></div>
                            <div class="tab-pane" id="bdc">
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
                            <div class="tab-pane" id="history_mail">                           
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
                            <div class="tab-pane" id="history_mod">
                                <?
                                getChangeLog('mv_orders', $oid);
//getChangeLog('av_order_detail', $oid);
                                ?>
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
            <div class = "col-xs-3">
                <?
                foreach ($orderStates as $orderState) {
                    ?>
                    <div class="alert-<?= $orderState["id_statut"] ?>" >
                    <?= $orderState["id_statut"] . " - " . $orderState["title"] ?>
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
        $(".product_current_state").change(function() {
            if ($(this).val() === 21 || $(this).val() === 22) {
                $(".product_supplier_comment").show("slow");
            }
        })

        $(".supplier").change(function() {
            $("#btn_send_supplier").attr("disabled", "disabled");
            $("#idmsg").text("Un fournisseur a été modifié,vous devez cliquer sur 'Modifier' pour valider les changements.")
            $("#idmsg").addClass("alert alert-info");
        })
        $("button[name='delProduitTruck']").click(function() {
            var btn = $(this);
            var p = $(this).val();
            var action = "del";
            var module = "ProduitTournee";
            var func = action + module;
            $.ajax({
                url: "functions/ajax_trucks.php",
                type: "POST",
                dataType: "json",
                async: false,
                data: {
                    func: func,
                    id: p,
                },
                success: function(data) {
                    console.log(data);
                    btn.attr("disabled", "disabled");
                },
            });
            location.reload();
        });

        $('#btn_send_supplier').click(function() {
            var btn = $(this);
            btn.button('loading');
        });

        $('.RefundDetails').click(function() {
            var sThisVal = 0;
            $('.RefundDetails').each(function() {
                sThisVal += parseInt((this.checked ? "1" : "0"));
                if (sThisVal) {
                    $("#refundPartial").show("slow");
                } else {
                    $("#refundPartial").hide("slow");
                }
            });
        });
        $('#hideinfoGeneral').click(function() {
            $(".infoGeneral").hide("slow");
        });
    </script>
    <?
} else {
    ?>
    <div class="container">
        <div class="alert alert-info">Il n'existe pas de commande <?= $oid ?></div>
    </div>

    <?
}
?>