<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

define("TRUCK_OVER_LOAD", 95);

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
?>
<?
if (isset($_GET["planning"])) {

    $date_livraison = $_GET["planning"];


    // on recupère les camions disponible sur la date choisi
    $trucks = $db->rawQuery("select * from av_truck 
                          where id_truck = ?
                          and id_truck not in (select id_truck from av_truck_planning where date_delivery = ?)", array($_GET["id_truck"], $date_livraison));
    ?>

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
                    $listOrderProduct = $db->rawQuery("select a.id_order, a.reference, d.postcode, b.*, c.*
                        from av_orders a, av_order_detail b , av_tournee c, av_address d
                        where a.id_order = b.id_order
                        and b.id_order_detail = c.id_order_detail 
                        and a.id_address_delivery = d.id_address
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
                            <td>commentaire 1</td>
                            <td>commentaire 2</td>
                            <td>commentaire 3</td>
                        </tr>
                        <?
                        //on boucle sur les produits
                        foreach ($listOrderProduct as $OrderProduct) {
                            $p = getProductInfos($OrderProduct["id_product"]);

                            $p_qty = $OrderProduct["nb_product_delivered"];

                            $montant_produits += $OrderProduct["product_price"];
                            $nb_produits += $OrderProduct["nb_product_delivered"];
                            $poids_produits += $p_qty * $OrderProduct["product_weight"];
                            $montant_transport += $OrderProduct["product_shipping"];
                            
                            if ($tmpRef != $OrderProduct["reference"]) {
                                ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <th colspan="2"><?= $OrderProduct["reference"] ?></th>
                                    <th colspan="2"><?= getDeliveryZone($OrderProduct["postcode"]) ?></th>
                                </tr>
                                <?
                                $tmpRef = $OrderProduct["reference"];
                            }
                            ?>
                            <tr>
                                <td><?= $p_qty ?></td>
                                <td nowrap><?= $p["name"] ?></td>
                                <td nowrap><?= $OrderProduct["product_width"] ?> x <?= $OrderProduct["product_height"] ?> x <?= $OrderProduct["product_depth"] ?></td>
                                <td><?= $p_qty * $OrderProduct["product_weight"] ?> Kg</td>                                                                                                                          
                                <td><input type="text" value="" name="comment1"> </td>                                                                                                              
                                <td><input type="text" value="" name="comment2"> </td>                                                                                                              
                                <td><input type="text" value="" name="comment3"> </td>                                                                                                              
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
                        <tr>
                            <td colspan="6">
                                <button name="validTruck" value="<?= $truck["id_truck"] ?>|<?= $_GET["planning"] ?>" type="button" class="btn btn-warning btn-lg btn-block">Valider</button>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?
    }
    ?>
    <?
}
?>


<script>
     
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

    $("#datepicker").datepicker(
            {
                //minDate: 0,
                onSelect: function() {
                    var day1 = $("#datepicker").datepicker('getDate').getDate();
                    var month1 = $("#datepicker").datepicker('getDate').getMonth() + 1;
                    var year1 = $("#datepicker").datepicker('getDate').getFullYear();
                    var fullDate = year1 + "-" + month1 + "-" + day1;
                    var str_output = "<h1><center>" + day1 + "-" + month1 + "-" + year1 + "</center></h1>";
                    $("#planning").val(fullDate);
                    page_output.innerHTML = str_output;
                }
            });
</script>
