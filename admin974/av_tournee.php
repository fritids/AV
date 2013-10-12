<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");

define("TRUCK_OVER_LOAD", 95);

function getProductTruck($id_order_detail, $date_delivery) {
    global $db;

    $p = $db->where("id_order_detail", $id_order_detail)
            ->where("date_livraison", $date_delivery)
            ->get("av_tournee");

    if ($p)
        return ($p[0]);
}

function getTruckLoad($id) {
    global $db;

    $r = $db->rawQuery("select a.id_truck, capacity ,
                    if ((sum(product_quantity * ( product_width * product_height * product_depth) / 1000000000) / capacity ) * 100 >100 , 100 , (sum(product_quantity * ( product_width * product_height * product_depth) / 1000000000) / capacity ) * 100 ) truck_load, 
                    sum(product_quantity * ( product_width * product_height * product_depth) / 1000000000) tot_vol, 
                    capacity - sum(product_quantity * ( product_width * product_height * product_depth) / 1000000000) volume_restant, 
                    sum(product_quantity * product_weight) tot_weight,
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

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$id_planning = 1;

$nb_produits = 0;
$poids_produits = 0;
$montant_produits = 0;
?>
<div id="datepicker"></div> 

<form method="get">
    <input type="text" value="<?= @$_GET["planning"] ?>" name="planning" id="planning">

    <input type="submit" >
</form>

<?
if (isset($_GET["planning"])) {

    $date_livraison = $_GET["planning"];

    // on recupère les camions disponible sur la date choisi
    $trucks = $db->rawQuery("select * from av_truck 
                          where id_truck not in (select id_truck from av_truck_planning where date_delivery = ?)", array($date_livraison));

    $orders = $db->rawQuery("select distinct a.*
                        from av_orders a, av_order_detail b 
                        where a.id_order = b.id_order
                        and b.id_order_detail not in (select id_order_detail from av_tournee where status = 2 )
                        ");
    ?>

    <table >
        <tr>
            <td valign="top">
                <table border="1" class="table-bordered">  
                    <?
                    foreach ($orders as $order) {
                        $customer = getOrderUserDetail($order["id_customer"]);
                        ?>
                        <tr>
                            <th><?= $order["id_order"] ?></th>
                            <th><?= $order["reference"] ?></th>        
                            <th><?= $customer["firstname"] . " " . $customer["lastname"] ?></th>        
                            <th><?= $order["total_paid"] ?> €</th>        
                            <th colspan="2">Actions</th>        
                        </tr>
                        <?
                        $listOrderProduct = $db->rawQuery("select a.id_order, b.*
                                                            from av_orders a, av_order_detail b 
                                                            where a.id_order = b.id_order
                                                            and b.id_order_detail not in (select id_order_detail from av_tournee where status = 2 )
                                                            and a.id_order = ? 
                                                            ", array($order["id_order"]));

                        foreach ($listOrderProduct as $OrderProduct) {
                            $prodVolume = $OrderProduct["product_quantity"] * round(($OrderProduct["product_width"] * $OrderProduct["product_height"] * $OrderProduct["product_depth"]) / 1000000000, 2);
                            ?>
                            <tr>
                                <td><?= $OrderProduct["id_order_detail"] ?></td>
                                <td><?= $OrderProduct["product_quantity"] ?> x <?= $OrderProduct["product_name"] ?></td>
                                <td><?= $OrderProduct["product_width"] ?> x <?= $OrderProduct["product_height"] ?> x <?= $OrderProduct["product_depth"] ?></td>
                                <td><?= $prodVolume ?> m3</td>
                                <td>
                                    <ul class = "list-inline">
                                        <?
                                        //on bloucle sur les camions
                                        foreach ($trucks as $truck) {
                                            $truckLoad = getTruckLoad(array($truck["id_truck"], $date_livraison));
                                            ?>
                                            <li>
                                                <?
                                                $mytruck = getProductTruck($OrderProduct["id_order_detail"], $date_livraison);
                                                if (isset($mytruck) && $truck ["id_truck"] != $mytruck["id_truck"]) {
                                                    ?>
                                                    <button name="addtruck"  class="btn btn-sm btn btn-default" disabled="disabled"> <?= $truck["name"] ?> </button>
                                                    <?
                                                } else {
                                                    if (!isset($mytruck) && (!empty($truckLoad) && $prodVolume > $truckLoad["volume_restant"])) {
                                                        ?>
                                                        <button name="addtruck"  class="btn btn-sm btn btn-default" disabled="disabled"> <?= $truck["name"] ?> </button>
                                                        <?
                                                    } else {
                                                        ?>
                                                        <button name="addtruck"  class="btn btn-primary btn-sm btn " value="add|<?= $truck["id_truck"] ?>|<?= $OrderProduct["id_order_detail"] ?>"> <?= $truck["name"] ?> </button>
                                                        <?
                                                    }
                                                    if ($truckLoad["truck_load"] >= TRUCK_OVER_LOAD) {
                                                        ?>
                                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?= $truckLoad["truck_load"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $truckLoad["truck_load"] ?>%; height: 2px; margin-top: 2px; float: none;">
                                                            <span class="sr-only"><?= $truckLoad["truck_load"] ?>% Complete</span>
                                                        </div>
                                                        <?
                                                    } else {
                                                        ?>
                                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?= $truckLoad["truck_load"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $truckLoad["truck_load"] ?>%; height: 2px; margin-top: 2px; float: none;">
                                                            <span class="sr-only"><?= $truckLoad["truck_load"] ?>% Complete</span>
                                                        </div>
                                                        <?
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
                    }
                    ?>
                </table>
                <hr>
            </td>
            <td valign="top">

                <?
                // on recupère les camions
                foreach ($trucks as $truck) {

                    $nb_produits = 0;
                    $montant_produits = 0;
                    $poids_produits = 0;
                    $montant_transport = 0;
                    $volume_produit = 0;
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
                                            $listOrderProduct = $db->rawQuery("select a.id_order, a.reference, b.*
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
                                                    <td>Volume</td>
                                                </tr>
                                                <?
                                                //on boucle sur les produits
                                                foreach ($listOrderProduct as $OrderProduct) {

                                                    $p = getProductInfos($OrderProduct["product_id"]);

                                                    $p_qty = $OrderProduct["product_quantity"];

                                                    $montant_produits += $OrderProduct["product_price"];
                                                    $nb_produits += $OrderProduct["product_quantity"];
                                                    $poids_produits += $p_qty * $OrderProduct["product_weight"];
                                                    $montant_transport += $OrderProduct["product_shipping"];
                                                    $volume_produit += $p_qty * $OrderProduct["product_width"] * $OrderProduct["product_height"] * $OrderProduct["product_depth"];

                                                    if ($tmpRef != $OrderProduct["reference"]) {
                                                        ?>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <th colspan="4"><?= $OrderProduct["reference"] ?></th>
                                                        </tr>
                                                        <?
                                                        $tmpRef = $OrderProduct["reference"];
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?= $OrderProduct["product_quantity"] ?></td>
                                                        <td nowrap><?= $p["name"] ?></td>
                                                        <td nowrap><?= $OrderProduct["product_width"] ?> x <?= $OrderProduct["product_height"] ?> x <?= $OrderProduct["product_depth"] ?></td>
                                                        <td><?= $p_qty * $OrderProduct["product_weight"] ?> Kg</td>                                        
                                                        <td><?= $p_qty * round(($OrderProduct["product_width"] * $OrderProduct["product_height"] * $OrderProduct["product_depth"]) / 1000000000, 2) ?> m3</td>                                        
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
                                                    <td colspan="3">Poids produits</td><td colspan="3"><?= $poids_produits ?> Kg</td>
                                                </tr>                   
                                                <tr>
                                                    <td colspan="3">Montant produits</td><td colspan="3"><?= $montant_produits ?> €</td>
                                                </tr>                   
                                                <tr>
                                                    <td colspan="3">Transport facturé</td><td colspan="3"><?= $montant_transport ?> €</td>
                                                </tr>                   
                                                <tr>
                                                    <td colspan="2">Vol. produits</td>
                                                    <td nowrap><?= $truckLoad["tot_vol"] ?> m3</td>
                                                    <td colspan="2">Vol. restant</td>
                                                    <td nowrap
                                                    <?
                                                    if ($truckLoad["volume_restant"] >= 100) {
                                                        echo 'class="danger"';
                                                    }
                                                    ?>
                                                        >
                                                        <?= $truckLoad["volume_restant"] ?> m3
                                                    </td>
                                                </tr> 
                                                <tr>
                                                    <td colspan="6">
                                                        <div class="progress">

                                                            <?
                                                            if ($truckLoad["truck_load"] > TRUCK_OVER_LOAD) {
                                                                ?>
                                                                <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?= $truckLoad["truck_load"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $truckLoad["truck_load"] ?>%">
                                                                    <span class="sr-only"><?= $truckLoad["truck_load"] ?>% Complete</span>
                                                                </div>
                                                                <?
                                                            } else {
                                                                ?>
                                                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?= $truckLoad["truck_load"] ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $truckLoad["truck_load"] ?>%">
                                                                    <span class="sr-only"><?= $truckLoad["truck_load"] ?>% Complete</span>
                                                                </div>
                                                                <?
                                                            }
                                                            ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">
                                                        <button name="validTruck" value="<?= $truck["id_truck"] ?>|<?= $_GET["planning"] ?>" type="button" class="btn btn-warning btn-lg btn-block">Valider</button>
                                                    </td>
                                                </tr>
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
                ?>

            </td>
        </tr>

    </table>

    <?
}
?>


<script>
    $("button[name='addtruck']").click(function() {
        var p = $(this).val() + "|" + $("#planning").val();
        var action = "add";
        var module = "truckTournee";
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
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''};

        $.datepicker.setDefaults($.datepicker.regional['fr']);
    });

    $("#datepicker").datepicker(
            {
                //minDate: 0,
                onSelect: function() {
                    var day1 = $("#datepicker").datepicker('getDate').getDate();
                    var month1 = $("#datepicker").datepicker('getDate').getMonth() + 1;
                    var year1 = $("#datepicker").datepicker('getDate').getFullYear();
                    var fullDate = year1 + "-" + month1 + "-" + day1;
                    var str_output = "<h1><center>" + fullDate + "</center></h1><br/><br>";
                    $("#planning").val(fullDate);
                    //page_output.innerHTML = str_output;
                }
            });
</script>
