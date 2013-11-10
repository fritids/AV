<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");


$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

function getItemTourneeinfo($odetail) {
    global $db;
    $r = $db->where("id_order_detail", $odetail)
            ->where("status", 2)
            ->get("av_tournee");
    if ($r)
        return($r[0]);
}

if (isset($_GET["id_order"]))
    $oid = $_GET["id_order"];

if (isset($_POST["id_order"]))
    $oid = $_POST["id_order"];

$updated = FALSE;

/* Update des status fournisseur */
if (isset($_POST) && !empty($_POST)) {
    foreach ($_POST["product_current_state"] as $id => $state) {
        $r = $db->where("id_order_detail", $id)
                ->update("av_order_detail", array("product_current_state" => $state));
    }
    foreach ($_POST["id_supplier"] as $id => $supplier) {
        $r = $db->where("id_order_detail", $id)
                ->update("av_order_detail", array("id_supplier" => $supplier));
    }
    foreach ($_POST["supplier_date_delivery"] as $id => $date_delivery) {
        if (!empty($date_delivery))
            $r = $db->where("id_order_detail", $id)
                    ->update("av_order_detail", array("supplier_date_delivery" => $date_delivery));
    }

//maj status order
    $r = $db->where("id_order", $oid)
            ->update("av_orders", array("current_state" => $_POST["current_state"]));
    $updated = TRUE;
}


$r = $db->rawQuery("select id_customer from av_orders where id_order = ?", array($oid));
$suppliers = $db->get("av_supplier");
$orderStates = $db->get("av_order_status");

$cid = $r[0]["id_customer"];

//contact
$customer_info = getCustomerDetail($cid);

//commande 
$orderDetail = getUserOrdersDetail($oid);
$orderinfo = getOrderInfos($oid);

//paiement
$orderPayment = getOrderPayment($orderinfo["id_order"]);

//Adresse
$customer_delivery = getAdresseById($orderinfo["id_address_delivery"]);
$customer_invoice = getAdresseById($orderinfo["id_address_invoice"]);


$a = $db->rawQuery("select id_order from av_orders where ? > id_order order by id_order desc", array($oid));
$b = $db->rawQuery("select id_order from av_orders where id_order > ? order by id_order asc", array($oid));

$order_precedent = @$a[0]["id_order"];
$order_suivant = @$b[0]["id_order"];
?>


<form method="post">
    <div class="container">
        <?
        if ($updated) {
            ?>
            <div class="row">
                <div class="col-xs-12" >
                    <div class="alert alert-success">
                        Modification a été effectuée avec succés.   
                    </div>                   

                </div>

            </div>
            <?
        }
        ?>
        <div class="row">
            <div class="col-xs-1">
                <?
                if ($order_precedent) {
                    ?>
                    <a href="?id_order=<?= $order_precedent ?>"><span class="glyphicon glyphicon-arrow-left"></span></a>
                    <?
                }
                ?>
            </div>
            <div class="col-xs-1">
                <?
                if ($order_suivant) {
                    ?>
                    <a href="?id_order=<?= $order_suivant ?>"><span class="glyphicon glyphicon-arrow-right"></span></a>
                    <?
                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="col-xs-8">
                    <h3>Commande : <?= $oid ?></h3>
                </div>  
                <div class="text-center col-xs-4 alert alert-<?= $orderinfo["current_state"] ?>" >
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

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Contact</div>
                    <div class="panel-body">
                        Nom : <?= @$customer_info["firstname"] ?> <br>
                        Prénom :<?= @$customer_info["lastname"] ?> <br>
                        Email : <?= @$customer_info["email"] ?> <br>                        

                    </div>
                </div>            
            </div>

            <div class="col-xs-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Adresse Livraison</div>
                    <div class="panel-body">
                        <span class="glyphicon glyphicon-earphone" ></span> <?= @$customer_delivery["phone"] ?><br>
                        <span class="glyphicon glyphicon-phone" ></span> <?= @$customer_delivery["phone_mobile"] ?> <br>
                        <?= @$customer_delivery["address1"] ?><br>
                        <?= @$customer_delivery["address2"] ?><br>
                        <?= @$customer_delivery["postcode"] ?> <?= @$customer_delivery["city"] ?><br>    
                    </div>
                </div>  
            </div>
            <div class="col-xs-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Adresse Facturation</div>
                    <div class="panel-body">
                        <span class="glyphicon glyphicon-earphone" ></span> <?= @$customer_invoice["phone"] ?> <br>
                        <span class="glyphicon glyphicon-phone" ></span> <?= @$customer_invoice["phone_mobile"] ?> <br>
                        <?= @$customer_invoice["address1"] ?><br>
                        <?= @$customer_invoice["address2"] ?><br>
                        <?= @$customer_invoice["postcode"] ?> <?= @$customer_invoice["city"] ?><br>
                    </div>
                </div> 
            </div>

        </div>

        <div class="row">
            <div class="col-xs-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Paiement</div>
                    <div class="panel-body <?= ($orderinfo["current_state"] == 2) ? 'alert alert-2' : ''; ?>">
                        Mode : <?= $orderinfo["payment"] ?><br>                        
                        Payé le : <?= (!empty($orderPayment["date_add"])) ? strftime("%a %d %b %y %T", strtotime($orderPayment["date_add"])) :"" ?><br>
                        Total : <?= $orderPayment["amount"] ?> €<br>
                    </div>
                </div> 
            </div>
            <div class="col-xs-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Commande</div>
                    <div class="panel-body">
                        N° :  <?= $orderinfo["id_order"] ?> reference :  <?= $orderinfo["reference"] ?><br>
                        Création :  <?= strftime("%a %d %b %y %T", strtotime($orderinfo["date_add"])) ?><br>                     
                        Total :  <?= $orderinfo["total_paid"] ?>€<br>
                    </div>
                </div> 
            </div>

            <div class="col-xs-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Commentaire client</div>
                    <div class="panel-body">
                        <?= $orderinfo["order_comment"] ?>
                    </div>
                </div>
            </div>
        </div>

        <?
        if (!empty($orderDetail)) {
            ?>


            <div class="row">
                <div class="col-xs-12">
                    <h2>Produits</h2>

                    <input type="hidden" name="id_order" value="<?= $oid ?>">
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
                            <th>L x H x P (mm)</th>
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
                        foreach ($orderDetail as $od) {
                            $t = getItemTourneeinfo($od["id_order_detail"]);
                            ?>
                            <tr id="id0">
                                <td><?= $od["id_order_detail"] ?></td>
                                <td><?= $od["id_product"] ?> <?= $od["product_name"] ?> </td>
                                <td>
                                    <?
                                    $attributes = getOrdersDetailAttribute($od["id_order_detail"]);
                                    foreach ($attributes as $attribute) {
                                        echo $attribute["name"] . "<br>";
                                    }
                                    ?>
                                </td>
                                <td><?= $od["product_width"] ?> x <?= $od["product_height"] ?> </td>
                                <td><?= $od["product_price"] + $od["attribute_price"] ?> €</td>                                
                                <td><?= $od["product_quantity"] ?></td>
                                <td><?= $od["total_price_tax_incl"] ?> €</td>
                                <td>
                                    <select style="width: 120px"  name="product_current_state[<?= $od["id_order_detail"] ?>]" class="pme-input-0">
                                        <option value="">--</option>
                                        <?
                                        foreach ($orderStates as $orderState) {
                                            ?>
                                            <option value="<?= $orderState["id_statut"] ?>"
                                            <?= ($od["product_current_state"] == $orderState["id_statut"]) ? "selected" : "" ?>
                                                    ><?= $orderState["title"] ?> </option>
                                                    <?
                                                }
                                                ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="id_supplier[<?= $od["id_order_detail"] ?>]" class="pme-input-0">
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
                            </tr>
                            <?
                        }
                        ?>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12" >
                    <div class="pull-right">
                        <input type="submit" name ="devis_save"  class="btn-lg btn-warning">
                    </div>
                </div>

            </div>
            <?
        }
        ?>
        <div class="row">
            <div class="col-xs-2">
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


</div>
</form>
<script>

    $(function() {
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd"
        });
    });
</script>
