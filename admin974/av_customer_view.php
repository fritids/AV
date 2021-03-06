<?php
require_once "../configs/settings.php";
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/devis.php");
include ("../functions/users.php");
include ("../functions/tools.php");


$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);


if (isset($_GET["id_customer"]))
    $cid = $_GET["id_customer"];

$customer_info = getCustomerDetail($cid);
$orders = getUserOrders($cid);
$deviss = getUserDevis($cid);

$suppliers = $db->get("av_supplier");
$orderStates = $db->where("id_level", 0)
        ->get("av_order_status");
$productStates = $db->where("id_level", 1)
        ->get("av_order_status");
?>

<div class="container">    
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
        <div class="col-xs-8">
            <h1><span class="text-info"><?= @$customer_info["firstname"] ?> <?= @$customer_info["lastname"] ?> </span></h1>
        </div>
        <div class="col-xs-4">

            <form class="form-horizontal"  method="get">
                <div class="input-group">
                    <span class="input-group-addon"><span class="glyphicon glyphicon-search"></span> </span>
                    <input type="text" id ="ajax_customer" class="form-control" placeholder="Entrer le nom ou email client"/>
                    <input type="hidden" name ="id_customer"  id ="id_customer"/>
                    <span class="input-group-btn">
                        <input type="submit" value="Go" class="btn btn-primary" >
                    </span>

                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <div class="panel panel-default">
                <div class="panel-heading">Contact <div class="pull-right"><a href="av_customer.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $customer_info["id_customer"] ?>"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                <div class="panel-body">
                    Nom : <?= @$customer_info["firstname"] ?> <br>
                    Prénom :<?= @$customer_info["lastname"] ?> <br>
                    Email : <?= @$customer_info["email"] ?> <br>                        
                    Type : <?= (@$customer_info["customer_group"] == 1) ? "PRO" : "Normal"; ?> <br>                        

                </div>
            </div>            
        </div>
        <div class="col-xs-4">
            <div class="panel panel-default">
                <div class="panel-heading">Adresse Livraison <div class="pull-right"><a href="av_address.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= @$customer_info["delivery"]["id_address"] ?>"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                <div class="panel-body">
                    <span class="glyphicon glyphicon-earphone" ></span> <?= @$customer_info["delivery"]["phone"] ?><br>
                    <span class="glyphicon glyphicon-phone" ></span> <?= @$customer_info["delivery"]["phone_mobile"] ?> <br>
                    <?= @$customer_info["delivery"]["address1"] ?><br>
                    <?= @$customer_info["delivery"]["address2"] ?><br>
                    <?= @$customer_info["delivery"]["postcode"] ?> <?= @$customer_info["delivery"]["city"] ?><br>    
                </div>
            </div>  
        </div>
        <div class="col-xs-4">
            <div class="panel panel-default">
                <div class="panel-heading">Adresse Facturation <div class="pull-right"><a href="av_address.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= @$customer_info["invoice"]["id_address"] ?>"><span class="glyphicon glyphicon-edit"></span></a></div></div>
                <div class="panel-body">
                    <span class="glyphicon glyphicon-earphone" ></span> <?= @$customer_info["invoice"]["phone"] ?> <br>
                    <span class="glyphicon glyphicon-phone" ></span> <?= @$customer_info["invoice"]["phone_mobile"] ?> <br>
                    <?= @$customer_info["invoice"]["address1"] ?><br>
                    <?= @$customer_info["invoice"]["address2"] ?><br>
                    <?= @$customer_info["invoice"]["postcode"] ?> <?= @$customer_info["invoice"]["city"] ?><br>
                </div>
            </div> 
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <div class="panel panel-info">
                <div class="panel-heading">Son compte</div>
                <div class="panel-body">

                    <?
                    $orders_count = 0;
                    $orders_amount = 0;
                    $orders_discount = 0;
                    foreach ($orders as $order) {
                        switch ($order["current_state"]) {
                            case 2:
                            case 3:
                            case 4:
                            case 5:
                                $orders_count += 1;
                                $orders_amount += $order["total_paid"];
                                $orders_discount+= $order["total_discount"];
                                break;
                        }
                    }
                    ?>
                    Nombre de commandes validés: <?= $orders_count ?> <br>
                    Argent dépensés : <?= $orders_amount ?> €<br>
                    Montant des réductions attribuées: <?= $orders_discount ?> €<br>
                </div>
            </div>  
        </div>
    </div>
    <hr>

    <h1>Ses opérations</h1>

    <div class="row">
        <div class="col-xs-12">

            <ul class="nav nav-pills">
                <li class="active"><a href="#commandes" data-toggle="tab">Commandes</a></li>
                <li><a href="#devis" data-toggle="tab">Devis</a></li>
                <li><a href="#voucher" data-toggle="tab">Coupons de réduction</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="commandes">
                    <?
                    foreach ($orders as $order) {
                        $orderPayment = getOrderPayment($order["id_order"]);
                        ?>
                        <div class="row">
                            <div class="col-md-offset-3 col-xs-6">                            
                                <h2 class="well">#   <?= $order["id_order"] ?></h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-3">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><strong>Commande </strong>
                                        <div class="pull-right">
                                            <a href="av_orders_view.php?id_order=<?= $order["id_order"] ?>"><span class="glyphicon glyphicon-zoom-in"></span></a>
                                            <a href="av_orders.php?PME_sys_fl=0&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $order["id_order"] ?>"><span class="glyphicon glyphicon-edit"></span></a></div>
                                    </div>
                                    <div class="panel-body alert alert-<?= $order["current_state"] ?>">
                                        Référence :  <?= $order["reference"] ?><br>
                                        Création :  <?= strftime("%a %d %b %y %T", strtotime($order["date_add"])) ?><br>                     
                                        Total :  <?= $order["total_paid"] ?>€<br>
                                        Statuts :  <?= $order["statut_label"] ?><br>
                                    </div>
                                </div> 
                            </div>
                            <div class="col-xs-3">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Paiement </div>
                                    <div class="panel-body">
                                        Mode : <?= $order["payment"] ?><br>                        
                                        Payé le : <?= (!empty($orderPayment["date_add"])) ? strftime("%a %d %b %y %T", strtotime($orderPayment["date_add"])) : "" ?><br>
                                        Total : <?= $orderPayment["amount"] ?> €<br>
                                    </div>
                                </div> 
                            </div>

                            <div class="col-xs-3">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Commentaire client</div>
                                    <div class="panel-body">
                                        <?= $order["order_comment"] ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered table-condensed col-xs-12" id="tab_devis">
                            <tr>
                                <th colspan="9" class="text-center">PRODUIT</th>
                                <th colspan="2" class="text-center">FOURNISSEUR</th>
                                <th colspan="6" class="text-center">LIVRAISON</th>                          
                            </tr>
                            <tr>
                                <th>Produit</th>
                                <th>Long x Larg</th>
                                <th>P.U.</th>
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
                            </tr>
                            <?
                            foreach ($order["details"] as $od) {
                                $t = getItemTourneeinfo($od["id_order_detail"]);
                                ?>
                                <tr id="id0">
                                    <td>
                                        <?= $od["product_name"] ?> <br>
                                        <?
                                        foreach ($od["attributes"] as $attribute) {
                                            echo " - " . $attribute["attribute_name"] . " : " . $attribute["attribute_value"] . "<br>";
                                        }
                                        ?>
                                        <em>ref#<?= $od["id_order_detail"] ?> - <?= $od["id_product"] ?></em>

                                    </td>
                                    <td><?= $od["product_width"] ?> x <?= $od["product_height"] ?> </td>
                                    <td><?= $od["product_price"] + $od["attribute_price"] ?> €</td>                                
                                    <td><?= $od["product_quantity"] ?></td>
                                    <td><?= $od["total_price_tax_incl"] ?> €</td>
                                    <td class="alert alert-<?= $od["product_current_state"] ?>"><?= $od["product_state_label"] ?></td>
                                    <td><?= $od["supplier_name"] ?></td>
                                    <td><?= @$od["supplier_date_delivery"] ?></td>
                                    <td><?= $t["nb_product_delivered"] ?></td>
                                    <td><?= ( $t["date_livraison"]) ? strftime("%a %d %b %y", strtotime($t["date_livraison"])) : ""; ?></td>                                
                                    <td><?= $t["horaire"] ?></td>
                                    <td><?= $t["comment1"] ?></td>
                                    <td><?= $t["comment2"] ?></td>
                                    <td><?= $t["comment3"] ?></td>
                                </tr>
                                <?
                            }
                            ?>
                        </table>     
                        <hr>
                        <?
                    }
                    ?>
                </div>

                <div class="tab-pane" id="devis">
                    <div class="panel panel-default">        
                        <?
                        $i = 0;
                        foreach ($deviss as $devis) {
                            $i++
                            ?>

                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <table class="table table-bordered table-condensed" style="margin-bottom: 0px;" >
                                        <tr>
                                            <th>
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $i ?>">
                                                    <table class="table table-bordered table-condensed" style="margin-bottom: 0px" >
                                                        <tr>
                                                            <th>Devis :</th>
                                                            <td><?= $devis["id_devis"] ?></td>
                                                            <th>Montant :</th>
                                                            <td><?= round($devis["total_paid"] * $config["vat_rate"], 2) ?> €</td>
                                                            <th>Date ajout :</th>
                                                            <td><?= $devis["date_add"] ?></td>
                                                            <th>Etat :</th>
                                                            <td class="
                                                            <?
                                                            switch ($devis["current_state"]) {
                                                                case 1:
                                                                    echo "alert alert-warning";
                                                                    break;

                                                                case 2:
                                                                    echo "alert alert-danger";
                                                                    break;
                                                                case 3:
                                                                    echo "alert alert-success";
                                                                    break;
                                                                case 4:
                                                                    echo "alert alert-success";
                                                                    break;
                                                                case 5:
                                                                    echo "alert alert-danger";
                                                                    break;
                                                            }
                                                            ?> "
                                                                >
                                                                    <?
                                                                    switch ($devis["current_state"]) {
                                                                        case 1:
                                                                            echo "En attente";
                                                                            break;

                                                                        case 2:
                                                                            echo "Rejeté";
                                                                            break;
                                                                        case 3:
                                                                            echo "Validé";
                                                                            break;
                                                                        case 4:
                                                                            echo "Commande créée: " . $devis["id_order"];
                                                                            break;
                                                                        case 5:
                                                                            echo "Annulé";
                                                                            break;
                                                                    }
                                                                    ?>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                </a>
                                            </th>
                                        </tr>
                                    </table>
                                </h4>
                            </div>
                            <div id="collapse<?= $i ?>" class="panel-collapse collapse in">
                                <div class="panel-body">

                                    <table class="table table-bordered table-condensed col-xs-12" id="tab_devis" style="margin-bottom: 0px;">
                                        <tr>
                                            <th>Produit</th>
                                            <th>Attributs</th>
                                            <th>Larg x Long</th>
                                            <th>Prix Unit.</th>
                                            <th>Poids Unit.</th>
                                            <th>Quantity</th>
                                            <th>Prix ttc</th>                        
                                        </tr>
                                        <?
                                        foreach ($devis["details"] as $line) {
                                            ?>

                                            <tr id="id0">
                                                <td><?= $line["product_name"] ?></td>
                                                <td>
                                                    <?
                                                    foreach ($line["combinations"] as $attribute) {
                                                        ?>
                                                        <?= $attribute["name"] ?><br>
                                                        <?
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= $line["product_width"] ?> x <?= $line["product_height"] ?></td>
                                                <td><?= $line["product_price"] ?></td>
                                                <td><?= $line["product_weight"] ?></td>
                                                <td><?= $line["product_quantity"] ?></td>                                                             
                                                <td><?= round(($line["total_price_tax_incl"] * $config["vat_rate"]), 2) ?></td>                    
                                            </tr>
                                            <?
                                        }
                                        ?>
                                    </table>

                                </div>
                            </div>

                            <?
                        }
                        ?>
                    </div>
                </div>
                <div class="tab-pane" id="voucher">
                    <table class="table table-bordered table-condensed col-xs-12" id="tab_devis">
                        <tr>
                            <th class="text-center">Code</th>
                            <th class="text-center">Début</th>
                            <th class="text-center">Fin</th>                          
                            <th class="text-center">Quantité</th>                          
                            <th class="text-center">Montant réduction</th>                          
                            <th class="text-center">Pourcentage réduction</th>                          
                            <th class="text-center">Actif</th>                          
                            <th class="text-center"></th>                          
                        </tr>

                        <?
                        foreach ($customer_info["voucher"] as $voucher) {
                            ?>
                            <tr>
                                <td><?= $voucher["code"] ?></td>
                                <td><?= $voucher["start_date"] ?></td>
                                <td><?= $voucher["end_date"] ?></td>
                                <td><?= $voucher["quantity"] ?></td>
                                <td><?= $voucher["reduction_amount"] ?></td>
                                <td><?= $voucher["reduction_percent"] ?></td>
                                <td><?= ($voucher["active"] == 0) ? "Non" : "Oui" ?></td>
                                <td><a href="av_voucher.php?PME_sys_fl=1&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $voucher["id_voucher"] ?>"><span class="glyphicon glyphicon-edit"></span></a></td>
                            </tr>
                            <?
                        }
                        ?>
                    </table> 
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $("#ajax_customer").autocomplete({
            source: 'functions/ajax_customer.php',
            select: function(event, ui) {
                $("#id_customer").val(ui.item.id_customer);
            }
        });
    })
</script>