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
    <h1>Le client</h1>
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
    <h1>Details du compte client</h1>

    <div class="row">
        <div class="col-xs-12">

            <ul class="nav nav-pills">
                <li class="active"><a href="#commandes" data-toggle="tab">Commandes</a></li>
                <li><a href="#devis" data-toggle="tab">Devis</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="commandes">
                    <?
                    foreach ($orders as $order) {
                        $orderPayment = getOrderPayment($order["id_order"]);
                        ?>

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
                                <th>#</th>                            
                                <th>Produit</th>
                                <th>Option</th>
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
                                    <td><?= $od["id_order_detail"] ?></td>
                                    <td><?= $od["id_product"] ?> <?= $od["product_name"] ?> </td>
                                    <td>
                                        <?
                                        foreach ($od["attributes"] as $attribute) {
                                            echo $attribute["name"] . "<br>";
                                        }
                                        ?>
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
                    <?
                    $i = 0;
                    foreach ($deviss as $devis) {
                        $i++
                        ?>

                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $i ?>">
                                    <table class="table table-bordered table-condensed" style="margin-bottom: 0px" >
                                        <tr>
                                            <th>Devis :</th>
                                            <td><?= $devis["id_devis"] ?></td>
                                            <th>Montant :</th>
                                            <td><?= $devis["total_paid"] ?> €</td>
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
                                            }
                                            ?> "
                                                >
                                                    <?= $devis["current_state"] ?>
                                            </td>
                                        </tr>
                                    </table>

                                </a>
                            </h4>
                        </div>
                        <div id="collapse<?= $i ?>" class="panel-collapse collapse in">
                            <div class="panel-body">

                                <table class="table table-bordered table-condensed col-xs-12" id="tab_devis">
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
                                            <td><?= $line["total_price_tax_incl"] ?></td>                    
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


        </div>
    </div>


</div>
