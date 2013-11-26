<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

define("TRUCK_OVER_LOAD", 95);

$productStates = $db->where("id_level", 1)
        ->get("av_order_status");

function getDeliveryZone($postcode) {
    global $db;

    $query = "select b.nom 
            from av_departements a , av_zone b
            where  a.id_zone = b.id_zone
            and  a.id_departement = " . substr($postcode, 0, 2);

    $z = $db->rawQuery($query);

    if ($z)
        return ($z[0]["nom"]);
}

function getSupplierName($id_sup) {
    global $db;
    $s = $db->where("id_supplier", $id_sup)
            ->get("av_supplier");

    if ($s)
        return ($s[0]["name"]);
}

function getProductTruck($id_order_detail, $date_delivery) {
    global $db;

    $p = $db->where("id_order_detail", $id_order_detail)
            //->where("date_livraison", $date_delivery)
            ->get("av_tournee");

    if ($p)
        return ($p[0]);
}

function getTruckLoad($id) {
    global $db;

    $r = $db->rawQuery("select a.id_truck, date_livraison, capacity ,
                    if (sum(a.nb_product_delivered * product_weight / capacity * 100) >100 , 100 , sum(a.nb_product_delivered * product_weight / capacity * 100)) truck_weight_load, 
                    capacity - sum(a.nb_product_delivered * product_weight) poids_restant, 
                    sum(a.nb_product_delivered * product_weight) tot_weight,
                    count(1)
                    from av_tournee a,  av_order_detail b, av_truck c
                    where a.id_order_detail = b.id_order_detail
                    and a.id_truck = c.id_truck
                    and a.id_truck = ?
                    and a.date_livraison = ?
                    group by id_truck
                    ", $id);
    if ($r)
        return ($r[0]);
}

$nb_produits = 0;
$poids_produits = 0;
$montant_produits = 0;
$qte_remaining = 0;
?>

<?
$zones = $db->get("av_zone");
$suppliers = $db->get("av_supplier");
?>

<div class="container">
    <form method="get">
        <input type="hidden" id="planning" value="<?= @$_GET["planning"] ?>">

        <table class="table table-bordered">
            <tr>
                <th>Organiser la tournée du</th>
                <th>Références</th>
                <th>Zones</th>
                <th>Founisseur </th>
                <th>Nom client</th>                
                <th>Voulez vous retirer les produits actuellement dans le camion</th>                            
                <th></th>
            </tr>
            <tr>
                <td><input type="text" id="datepicker2" value="" name="planning"></td>
                <td><input type="text" name="reference" value="<?= @$_GET["reference"] ?>" ></td>
                <td>
                    <select name="id_zone[]" class="pme-input-1" multiple="" >
                        <?
                        foreach ($zones as $zone) {
                            ?>
                            <option value="<?= $zone["id_zone"] ?>" 
                            <? if (isset($_GET["id_zone"]) && $_GET["id_zone"] == $zone["id_zone"]) echo "selected"; ?>  
                                    ><?= $zone["nom"] ?></option>
                                    <?
                                }
                                ?>
                    </select>
                </td>
                <td>
                    <select name="id_supplier" class="pme-input-1">
                        <option value="">--</option>
                        <?
                        foreach ($suppliers as $supplier) {
                            ?>
                            <option value="<?= $supplier["id_supplier"] ?>" 
                            <? if (isset($_GET["id_zone"]) && $_GET["id_supplier"] == $supplier["id_supplier"]) echo "selected"; ?>  
                                    ><?= $supplier["name"] ?></option>
                                    <?
                                }
                                ?>
                    </select>
                </td>
                <? /* <td><input type="text" id="datepicker" value="<?= @$_GET["invoice_date"] ?>" name="invoice_date"></td> */ ?>
                <td><input type="text" value="<?= @$_GET["customer_name"] ?>" name="customer_name"></td>

                <td class="text-center">
                    <input type="checkbox" value="1" name="prod_affecte" <? if (isset($_GET["prod_affecte"])) echo "checked"; ?> /> 
                </td>
                <td><input type="submit" ></td>
            </tr>
        </table>
    </form>

    <div class = "row">
        <div class = "col-xs-3">
            <?
            foreach ($productStates as $pState) {
                ?>
                <div class="row">
                    <div class="alert-<?= $pState["id_statut"] ?>" >
                        <?= $pState["title"] ?>
                    </div>
                </div>
                <?
            }
            ?>
        </div>

        <div class = "col-xs-9">
            <h5>Critères :</h5>
            <?
            if (!empty($_GET["reference"]))
                echo "<li> Reference = " . $_GET["reference"] . "</li>";
            if (!empty($_GET["planning"]))
                echo "<li> date tournée = " . $_GET["planning"] . "</li>";
            if (!empty($_GET["invoice_date"]))
                echo "<li> date facturation >= " . $_GET["invoice_date"] . "</li>";
            if (!empty($_GET["id_supplier"]))
                echo "<li> Fournisseur = " . $_GET["id_supplier"] . "</li>";
            if (!empty($_GET["prod_affecte"]))
                echo "<li> hors produits affectés </li>";
            if (!empty($_GET["id_zone"]))
                echo "<li> Zone = " . implode(",", $_GET["id_zone"]) . "</li>";
            if (!empty($_GET["customer_name"]))
                echo "<li> Client = " . $_GET["customer_name"] . "</li>";
            ?>
        </div>
    </div>
    <?
    if (isset($_GET["planning"])) {

        $date_livraison = $_GET["planning"];

        // on recupère les camions disponible sur la date choisi
        $trucks = $db->rawQuery("select * from av_truck 
                          where id_truck not in (select id_truck from av_truck_planning where date_delivery = ?)", array($date_livraison));


        $queryOrder = "select a.id_order, id_address_delivery, a.reference, a.id_customer, total_paid, c.postcode, a.invoice_date,
                    round(sum(product_quantity * ( product_width * product_height * product_depth) / 1000000000),2) order_volume,
                    sum(product_quantity * product_weight) tot_weight
                    from av_orders a, av_order_detail b, av_address c, av_customer d
                    where a.id_order = b.id_order                   
                    and b.id_order_detail not in (select id_order_detail from av_tournee where status = 2 )
                    and a.id_address_delivery = c.id_address 
                    and a.id_customer = d.id_customer                    
                    ";

        if (isset($_GET["id_zone"]) && !empty($_GET["id_zone"])) {
            $z = implode(",", $_GET["id_zone"]);
            $queryOrder .= " and  substr(c.postcode , 1 , 2) in (select id_departement from av_departements where id_zone in ( " . $z . ") )";
        }
        if (isset($_GET["id_supplier"]) && !empty($_GET["id_supplier"]))
            $queryOrder .= " and id_supplier = " . $_GET["id_supplier"] . " ";

        if (isset($_GET["invoice_date"]) && !empty($_GET["invoice_date"])) {
            $queryOrder .= " and date(a.invoice_date) >= ? ";
            $params[] = $_GET["invoice_date"];
        }
        if (isset($_GET["planning"]) && !empty($_GET["planning"])) {
            $queryOrder .= " and ? >= date(b.supplier_date_delivery) ";
            $params[] = $date_livraison;
        }

        if (isset($_GET["prod_affecte"]) && !empty($_GET["prod_affecte"]))
            $queryOrder .= " and b.id_order_detail not in (select id_order_detail from av_tournee where status = 1 )";

        if (isset($_GET["reference"]) && !empty($_GET["reference"])) {
            $queryOrder .= " and a.reference like ? ";
            $params[] = "%" . $_GET["reference"] . "%";
        }
        if (isset($_GET["customer_name"]) && !empty($_GET["customer_name"])) {
            $queryOrder .= " and concat(lower(d.firstname),lower(d.lastname)) like ? ";
            $params[] = "%" . $_GET["customer_name"] . "%";
        }
        $queryOrder .= "group by a.id_order, id_address_delivery, a.reference, a.id_customer, total_paid, a.invoice_date
                        order by c.postcode asc";

        $orders = $db->rawQuery($queryOrder, @$params);
        ?>

        <table>
            <tr>
                <td valign="top">
                    <table border="1" class="table-bordered">  
                        <?
                        foreach ($orders as $order) {

                            $o = getOrderInfos($order["id_order"]);

                            if ($order)
                                $customer = getOrderUserDetail($order["id_customer"]);
                            $adresse = getAdresseById($order["id_address_delivery"]);
                            ?>
                            <tr>
                                <th nowrap class="alert alert-info" >
                                    <a href="av_orders_view.php?id_order=<?= $order["id_order"] ?>"><?= $order["reference"] ?></a> <br>
                                    <?= date("d/m", strtotime($order["invoice_date"])) ?>

                                </th>
                                <th><?= getDeliveryZone($order["postcode"]) ?></th>
                                <th>
                                    <?
                                    $addrs = $adresse["address1"] . "<br>";
                                    if ($adresse["address2"])
                                        $addrs .= $adresse["address2"] . "<br>";
                                    $addrs .= $adresse["postcode"] . " " . $adresse["city"];
                                    $addrs_link = str_replace(' ', '+', $addrs);
                                    $addrs_link = str_replace('<br>', '+', $addrs);
                                    ?>

                                    <?= $customer["firstname"] . " " . $customer["lastname"] . " <a href='mailto:" . $customer["email"] . "' target='_blank'>" . $customer["email"] ?></a><br>
                                    <a href="https://maps.google.fr/maps?q=<?= $addrs_link ?>" target="_blank" ><?= $addrs ?></a>
                                </th>        
                                <th><?= $order["total_paid"] ?> €</th>        
                                <th>&nbsp; </th>        
                                <th><?= $order["tot_weight"] ?> Kg</th>        
                                <th colspan="3"><?= @$o["order_comment"] ?></th>        
                            </tr>
                            <?
                            $queryOrderDetail = "select a.id_order, b.*,
                                            product_quantity * product_weight tot_prod_weight
                                            from av_orders a, av_order_detail b
                                            where a.id_order = b.id_order                                            
                                            and b.id_order_detail not in(select id_order_detail from av_tournee where status = 2)
                                            and a.id_order = ?";


                            $params = array($order["id_order"]);

                            if (isset($_GET["prod_affecte"]) && !empty($_GET["prod_affecte"]))
                                $queryOrderDetail .= " and b.id_order_detail not in (select id_order_detail from av_tournee where status = 1 )";

                            /* if (isset($_GET["planning"]) && !empty($_GET["planning"])) {
                              $queryOrderDetail .= " and date(b.supplier_date_delivery) >= ? ";
                              $params[] = $_GET["planning"];
                              } */

                            if (isset($_GET["prod_affecte"]) && !empty($_GET["prod_affecte"]))
                                $queryOrderDetail .= " and b.id_order_detail not in (select id_order_detail from av_tournee where status = 1 )";

                            $listOrderProduct = $db->rawQuery($queryOrderDetail, $params);

                            foreach ($listOrderProduct as $OrderProduct) {
                                //$product_weight = $OrderProduct["product_quantity"] * round(($OrderProduct["product_width"] * $OrderProduct["product_height"] * $OrderProduct["product_depth"]) / 1000000000, 2);
                                $mytruck = getProductTruck($OrderProduct["id_order_detail"], $date_livraison);

                                $qte_remaining = $OrderProduct["product_quantity"] - $mytruck["nb_product_delivered"];

                                $product_weight = $qte_remaining * $OrderProduct["product_weight"];
                                ?>
                                <tr id="<?= $OrderProduct["id_order_detail"] ?>"
                                <?= ($OrderProduct["supplier_date_delivery"] == null) ? "class='alert alert-danger'" : "class='alert alert-" . $OrderProduct["product_current_state"] . "'" ?>

                                    >

                                    <td colspan="3">
                                        <?= $OrderProduct["product_quantity"] ?> x <?= $OrderProduct["product_name"] ?>
                                        <?
                                        if ($qte_remaining > 0) {
                                            ?>
                                            <input type ="hidden" name="nb_<?= $OrderProduct["id_order_detail"] ?>" id="nb_<?= $OrderProduct["id_order_detail"] ?>" value="<?= $qte_remaining ?>">
                                            <?
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= getSupplierName($OrderProduct["id_supplier"]) ?> 
                                        <? if ($OrderProduct["supplier_date_delivery"] != null) echo date("d/m/Y", strtotime($OrderProduct["supplier_date_delivery"])) ?>
                                    <td><?= $OrderProduct["product_width"] ?> x <?= $OrderProduct["product_height"] ?></td>
                                    <td><?= $product_weight ?> kg</td>                                    



                                    <td>
                                        <ul class = "list-inline">
                                            <?
                                            //on bloucle sur les camions
                                            foreach ($trucks as $truck) {
                                                $truckLoad = getTruckLoad(array($truck["id_truck"], $date_livraison));
                                                ?>
                                                <li>
                                                    <?
                                                    if (isset($mytruck) && $truck ["id_truck"] != $mytruck["id_truck"] || $OrderProduct["product_current_state"] == 19) {
                                                        ?>
                                                        <button name="addtruck"  class="btn btn-xs btn btn-default" disabled="disabled"> <?= $truck["name"] ?> </button>
                                                        <?
                                                    } else {
                                                        if (!isset($mytruck) && (!empty($truckLoad) && $product_weight > $truckLoad["poids_restant"] ) /* || $product_weight > $truck["capacity"] */) {
                                                            ?>
                                                            <button name="addtruck"  class="btn btn-xs btn btn-default" disabled="disabled"><?= $truck["name"] ?> </button>
                                                            <?
                                                        } else {
                                                            ?>
                                                            <button name="addtruck"  class="<?= ($mytruck["date_livraison"]) ? "btn btn-xs alert-success" : "btn btn-primary btn-xs btn" ?>" value="add|<?= $truck["id_truck"] ?>|<?= $OrderProduct["id_order_detail"] ?>"> <?= $truck["name"] ?>                                                             
                                                                <?
                                                                if ($mytruck["date_livraison"]) {
                                                                    ?>
                                                                    <br> <span class="glyphicon glyphicon-road" > <?= date('d/m', strtotime($mytruck["date_livraison"])) ?>  </span>
                                                                    <?
                                                                }
                                                                ?>
                                                            </button>
                                                            <?
                                                        }
                                                        if ($mytruck["date_livraison"] == $date_livraison) {
                                                            if ($truckLoad["truck_weight_load"] >= TRUCK_OVER_LOAD) {
                                                                ?>
                                                                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?= $truckLoad["truck_weight_load"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $truckLoad["truck_weight_load"] ?>%; height: 2px; margin-top: 2px; float: none;">
                                                                    <span class="sr-only"><?= $truckLoad["truck_weight_load"] ?>% Complete</span>
                                                                </div>
                                                                <?
                                                            } else {
                                                                ?>
                                                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?= $truckLoad["truck_weight_load"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $truckLoad["truck_weight_load"] ?>%; height: 2px; margin-top: 2px; float: none;">
                                                                    <span class="sr-only"><?= $truckLoad["truck_weight_load"] ?>% Complete</span>
                                                                </div>
                                                                <?
                                                            }
                                                        } else {
                                                            $truckLoad = getTruckLoad(array($truck["id_truck"], $mytruck["date_livraison"]));

                                                            if ($truckLoad["truck_weight_load"] >= TRUCK_OVER_LOAD) {
                                                                ?>
                                                                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?= $truckLoad["truck_weight_load"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $truckLoad["truck_weight_load"] ?>%; height: 2px; margin-top: 2px; float: none;">
                                                                    <span class="sr-only"><?= $truckLoad["truck_weight_load"] ?>% Complete</span>
                                                                </div>
                                                                <?
                                                            } else {
                                                                ?>
                                                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?= $truckLoad["truck_weight_load"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $truckLoad["truck_weight_load"] ?>%; height: 2px; margin-top: 2px; float: none;">
                                                                    <span class="sr-only"><?= $truckLoad["truck_weight_load"] ?>% Complete</span>
                                                                </div>
                                                                <?
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </li>
                                                <?
                                            }
                                            ?>
                                        </ul>
                                    </td>
                                    <td style="text-align: center">
                                        <?
                                        if (isset($mytruck)) {
                                            ?>
                                            <button name="delProduitTruck" value="<?= $OrderProduct["id_order_detail"] ?>" >
                                                <span class="glyphicon glyphicon-remove"></span>
                                            </button>
                                            <?
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <?
                            }
                            ?>

                            <tr>
                                <td colspan="9" style="height: 30px;border-left: 0px;border-right:none;"></td>
                            </tr>
                            <?
                        }
                        ?>
                    </table>
                    <hr>                    
                </td>
                <td valign="top">

                    <?
                    if ($orders) {
// on recupère les camions

                        foreach ($trucks as $truck) {

                            $nb_produits = 0;
                            $montant_produits = 0;
                            $poids_produits = 0;
                            $volume_produit = 0;
                            $montant_transport = 0;
                            $nb_commandes = 0;
                            $tmpRef = "";



                            $truckLoad = getTruckLoad(array($truck["id_truck"], $date_livraison));
                            ?>
                            <table>
                                <tr>
                                    <td valign="top">
                                        <table  class="table-bordered">
                                            <tr>
                                                <th><?= $truck["id_truck"] ?></th>                
                                                <th><?= $truck["imma"] ?></th>
                                                <th><?= $truck["name"] ?></th>
                                            </tr>
                                            <tr>
                                                <td colspan="4">
                                                    <?
                                                    // on recupère les produits affectés au camion
                                                    $listOrderProduct = $db->rawQuery("select a.id_order, a.reference, id_product, b.*, c.*
                                                                    from av_orders a, av_order_detail b , av_tournee c
                                                                    where a.id_order = b.id_order
                                                                    and b.id_order_detail = c.id_order_detail 
                                                                    and c.id_truck = ? 
                                                                    and c.date_livraison = ?                                                                     
                                                                    order by a.id_order
                                                                    ", array($truck["id_truck"], $date_livraison))
                                                    ?>
                                                    <table class="table-condensed">
                                                        <tr>
                                                            <td>Qty</td>
                                                            <td>Nom</td>
                                                            <td>Dimension</td>
                                                            <td>Poids</td>
                                                        </tr>
                                                        <?
                                                        //on boucle sur les produits
                                                        foreach ($listOrderProduct as $OrderProduct) {
                                                            $p_qty = $OrderProduct["nb_product_delivered"];

                                                            $montant_produits += $OrderProduct["product_price"];
                                                            $nb_produits += $OrderProduct["nb_product_delivered"];
                                                            $poids_produits += $p_qty * $OrderProduct["product_weight"];
                                                            //$montant_transport += $OrderProduct["product_shipping"];

                                                            if ($tmpRef != $OrderProduct["reference"]) {
                                                                $montant_transport += $conf_shipping_amount;
                                                                $nb_commandes++;
                                                                ?>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <th colspan="4">
                                                                        <a href="av_orders_view.php?id_order=<?= $OrderProduct["id_order"] ?>"><?= $OrderProduct["reference"] ?></a>
                                                                    </th>
                                                                </tr>
                                                                <?
                                                                $tmpRef = $OrderProduct["reference"];
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td><?= $p_qty ?></td>
                                                                <td><?= $OrderProduct["product_name"] ?></td>
                                                                <td nowrap><?= $OrderProduct["product_width"] ?> x <?= $OrderProduct["product_height"] ?></td>
                                                                <td><?= $p_qty * $OrderProduct["product_weight"] ?> Kg</td>                                        
                                                                <td>
                                                                    <button name="delProduitTruck" value="<?= $OrderProduct["id_order_detail"] ?>" ><span class="glyphicon glyphicon-remove"></span></button>
                                                                </td>                                        
                                                            </tr>
                                                            <?
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">Nb produits</td><td colspan="3"><?= $nb_produits ?></td>
                                                        </tr>                   
                                                        <tr>
                                                            <td colspan="3">Nb Commandes</td><td colspan="3"><?= $nb_commandes ?></td>
                                                        </tr>                   
                                                        <tr>
                                                            <td colspan="3">Montant produits</td><td colspan="3"><?= $montant_produits ?> €</td>
                                                        </tr>                   
                                                        <tr>
                                                            <td colspan="3">Transport facturé</td><td colspan="3"><?= $montant_transport ?> €</td>
                                                        </tr>                   
                                                        <tr>
                                                            <td colspan="2">Poids. produits</td>
                                                            <td nowrap><?= $poids_produits ?> Kg</td>
                                                            <td colspan="2">Poids. restant</td>
                                                            <td nowrap
                                                            <?
                                                            if ($truckLoad["poids_restant"] >= 100) {
                                                                echo 'class="danger"';
                                                            }
                                                            ?>
                                                                >
                                                                <?= $truckLoad["poids_restant"] ?> Kg
                                                            </td>
                                                        </tr> 
                                                        <tr>
                                                            <td colspan="6">
                                                                <div class="progress">

                                                                    <?
                                                                    if ($truckLoad["truck_weight_load"] > TRUCK_OVER_LOAD) {
                                                                        ?>
                                                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?= $truckLoad["truck_weight_load"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $truckLoad["truck_weight_load"] ?>%">
                                                                            <span class="sr-only"><?= $truckLoad["truck_weight_load"] ?>% Complete</span>
                                                                        </div>
                                                                        <?
                                                                    } else {
                                                                        ?>
                                                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?= $truckLoad["truck_weight_load"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $truckLoad["truck_weight_load"] ?>%">
                                                                            <span class="sr-only"><?= $truckLoad["truck_weight_load"] ?>% Complete</span>
                                                                        </div>
                                                                        <?
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?
                                                        if ($truckLoad["truck_weight_load"] > 0 || $nb_produits > 0) {
                                                            ?>
                                                            <tr>
                                                                <td colspan="6">

                                                                    <a href ="av_truck_preview.php?id_truck=<?= $truck["id_truck"] ?>&planning=<?= $_GET["planning"] ?>" class="btn btn-warning btn-lg btn-block">Aperçu</a>
                                                                </td>
                                                            </tr>
                                                            <?
                                                        }
                                                        ?>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <hr>
                            <?
                        }
                    }
                    ?>

                </td>
            </tr>

        </table>
    </div>
    <?
}
?>


<script>
    $("button[name='addtruck']").click(function() {


        var id_order_detail = $(this).parent().parent().parent().parent().attr("id");

        var nb = $("#nb_" + id_order_detail).val();

        var p = $(this).val() + "|" + $("#planning").val() + "|" + nb;

        var action = "add";
        var module = "truckTournee";
        var func = action + module;
        console.log(p + " " + func + " nb " + nb);


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
            },
            error: function(xhr, textStatus, error) {
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }
        });

        location.reload();

    });
    $("button[name='delProduitTruck']").click(function() {
        var p = $(this).val();
        var action = "del";
        var module = "ProduitTournee";

        var func = action + module;

        console.log(func);

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
            },
            error: function(xhr, textStatus, error) {
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }
        });
        location.reload();
    });
    $("button[name='validTruck']").click(function() {
        var p = $(this).val();
        var action = "upd";
        var module = "ValidTruck";

        var func = action + module;

        console.log(p + " " + func);

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
            },
            error: function(xhr, textStatus, error) {
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }
        });
        location.reload();
    });


    jQuery(function($) {
        $.datepicker.regional['fr'] = {
            closeText: 'Fermer',
            prevText: '<Préc',
            nextText: 'Suiv>',
            currentText: 'Courant',
            monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthNamesShort: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun',
                'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
            dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
            weekHeader: 'Sm',
            //dateFormat: 'dd/mm/yy',
            dateFormat: 'yyyy-mm-dd',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''};

        $.datepicker.setDefaults($.datepicker.regional['fr']);
    });

    $("#datepicker2").datepicker(
            {
                //minDate: 0,
                onSelect: function() {
                    var day1 = $("#datepicker").datepicker('getDate').getDate();
                    var month1 = $("#datepicker").datepicker('getDate').getMonth() + 1;
                    var year1 = $("#datepicker").datepicker('getDate').getFullYear();
                    var fullDate = year1 + "-" + month1 + "-" + day1;
                    var str_output = "<h1><center>" + day1 + "-" + month1 + "-" + year1 + "</center></h1>";
                    //$("#planning").val(fullDate);
                    //page_output.innerHTML = str_output;
                }
            });
</script>
