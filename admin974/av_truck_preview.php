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

function updValidTruck($id_truck, $date_livraison, $updinfos) {
    global $db;

    foreach ($updinfos["comment1"] as $id => $comment) {
        $r = $db->where("id_truck", $id_truck)
                ->where("date_livraison", $date_livraison)
                ->where("id_order", $id)
                ->update("av_tournee", array("comment1" => $comment));
    }
    foreach ($updinfos["comment2"] as $id => $comment) {
        $r = $db->where("id_truck", $id_truck)
                ->where("date_livraison", $date_livraison)
                ->where("id_order", $id)
                ->update("av_tournee", array("comment2" => $comment));
    }
    foreach ($updinfos["comment3"] as $id => $comment) {
        $r = $db->where("id_truck", $id_truck)
                ->where("date_livraison", $date_livraison)
                ->where("id_order", $id)
                ->update("av_tournee", array("comment3" => $comment));
    }
    foreach ($updinfos["horaire"] as $id => $comment) {
        $r = $db->where("id_truck", $id_truck)
                ->where("date_livraison", $date_livraison)
                ->where("id_order", $id)
                ->update("av_tournee", array("horaire" => $comment));
    }

    $r = $db->where("id_truck", $id_truck)
            ->where("date_livraison", $date_livraison)
            ->where("id_order", $id)
            ->update("av_tournee", array("status" => $updinfos["status"]));



    /* on bloque le camion pour la date livraison */
    $infoTruckPlanning = array(
        "id_truck" => $id_truck,
        "date_delivery" => $date_livraison,
        "status" => 1
    );

    $r = $db->insert("av_truck_planning", $infoTruckPlanning);

    /* on passe les produits en livraison prévu */
    $orderDetails = $db->rawQuery("select id_order_detail from av_tournee where id_truck = ? and date_livraison = ? and status = 2", array($id_truck, $date_livraison));

    foreach ($orderDetails as $orderDetail) {
        $r = $db->where("id_order_detail", $orderDetail["id_order_detail"])
                ->update("av_order_detail", array("product_current_state" => 14));
    }
    if ($r)
        return true;
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
$upd = false;
?>

<?
if (isset($_POST) && !empty($_POST)) {

    $updinfos = array(
        "status" => 2,
        "comment1" => $_POST["comment1"],
        "comment2" => $_POST["comment2"],
        "comment3" => $_POST["comment3"],
        "horaire" => $_POST["horaire"],
    );
    $r = updValidTruck($_POST["id_truck"], $_POST["date_livraison"], $updinfos);

    if ($r)
        echo "<div class='alert alert-success text-center' > Camion validé </div>";

    $upd = true;
}

if (isset($_GET["planning"]) && !$upd) {

    $date_livraison = $_GET["planning"];

    // on recupère les camions disponible sur la date choisi
    $trucks = $db->rawQuery("select * from av_truck 
                          where id_truck = ?
                          and id_truck not in (select id_truck from av_truck_planning where date_delivery = ?)", array($_GET["id_truck"], $date_livraison));
    $truck = $trucks[0];

    // on recupère les camions

    $nb_produits = 0;
    $montant_produits = 0;
    $poids_produits = 0;
    $montant_transport = 0;
    $tmpRef = "";
    $truckLoad = getTruckLoad(array($truck["id_truck"], $date_livraison));
    ?>

    <div class="container">
        <form action="" method="post">
            <input type="hidden" name="id_truck" value="<?= $truck["id_truck"] ?>"/>
            <input type="hidden" name="date_livraison" value="<?= $_GET["planning"] ?>"/>     
            <input type="hidden" name="status" value="2"/> 

            <?
            // on recupère les produits affectés au camion
            $listOrderProduct = $db->rawQuery("select a.id_order, a.reference, d.postcode, a.id_customer, b.*, c.*
                        from av_orders a, av_order_detail b , av_tournee c, av_address d
                        where a.id_order = b.id_order
                        and b.id_order_detail = c.id_order_detail 
                        and a.id_address_delivery = d.id_address
                        and c.id_truck = ? 
                        and c.date_livraison = ?                                                                     
                        order by a.id_order
                        ", array($truck["id_truck"], $date_livraison))
            ?>


            <table class="col-md-12 table-condensed">    
                <tr>
                    <td>
                        <ul id="sortlist" class="list-unstyled">
                            <?
                            //on boucle sur les produits
                            foreach ($listOrderProduct as $OrderProduct) {
                                ?>
                                <li id="votre_id_1" class="sortable_item">
                                    <table >
                                        <?
                                        $p = getProductInfos($OrderProduct["id_product"]);
                                        $customer = getOrderUserDetail($OrderProduct["id_customer"]);
                                        $adresse = getAdresse($OrderProduct["id_customer"], 'delivery');

                                        $p_qty = $OrderProduct["nb_product_delivered"];

                                        $montant_produits += $OrderProduct["product_price"];
                                        $nb_produits += $OrderProduct["nb_product_delivered"];
                                        $poids_produits += $p_qty * $OrderProduct["product_weight"];
                                        $montant_transport += $OrderProduct["product_shipping"];


                                        $addrs = $adresse["address1"] . "<br>";
                                        if ($adresse["address2"])
                                            $addrs .= $adresse["address2"] . "<br>";
                                        $addrs .= $adresse["postcode"] . " " . $adresse["city"];
                                        $addrs_link = str_replace(' ', '+', $addrs);
                                        $addrs_link = str_replace('<br>', '+', $addrs);


                                        if ($tmpRef != $OrderProduct["reference"]) {
                                            ?>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <th colspan="2"><?= $OrderProduct["reference"] ?></th>
                                                <th colspan="2">
                                                    <?= $customer["firstname"] . " " . $customer["lastname"] ?><br>
                                                    <a href="https://maps.google.fr/maps?q=<?= $addrs_link ?>" target="_blank" ><?= $addrs ?></a>
                                                </th>
                                                <th>temps de trajet <br><input type="text" value="" name="comment1[<?= $OrderProduct["id_order"] ?>]"> </th>                                                                                                              
                                                <th>Heure de livraison<br><input type="text" value="" name="comment2[<?= $OrderProduct["id_order"] ?>]"> </th>                                                                                                              
                                                <th>Informations <br><input type="text" value="" name="comment3[<?= $OrderProduct["id_order"] ?>]"> </th>                                                                                                              
                                                <th>Horaire indiqué au client<br><input type="text" value="" name="horaire[<?= $OrderProduct["id_order"] ?>]"> </th>    
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

                                        </tr>
                                    </table>
                                </li>
                                <?
                            }
                            ?>
                            <table class="col-md-6">
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
                                        echo 'class = "danger" ';
                                    }
                                    ?>
                                        >
                                        <?= $truckLoad["poids_restant"] ?> Kg
                                    </td>
                                </tr> 
                                <tr>
                                    <td colspan="9">
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
                                    <td colspan="9">
                                        <button type="submit" name="validate"  class="btn btn-warning btn-lg btn-block">Valider</button>
                                    </td>
                                </tr>
                            </table>
                    </td>
                </tr>
            </table>

        </form>
    </div>
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
                    var year1 = $("#datepicker").datepicker('getDate ').getFullYear();
                    var fullDate = year1 + "-" + month1 + "-" + day1;
                    var str_output = "<h1><center>" + day1 + "-" + month1 + "-" + year1 + "</center></h1>";
                    $("#planning").val(fullDate);
                    page_output.innerHTML = str_output;
                }
            });
</script>
