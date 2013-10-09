<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$id_planning = 1;

$nb_produits = 0;
$poids_produits = 0;
$montant_produits = 0;

$camions = $db->get("av_camion");

$orders = $db->where("current_state", 1)
        ->get("av_orders");
?>


<table border="1">
    <?
    foreach ($orders as $order) {
        ?>
        <tr>
            <td><?= $order["id_order"] ?></td>
            <td><?= $order["reference"] ?></td>        
        </tr>
        <tr>
            <td colspan="2">
                <table>


                    <?
                    $listOrderProduct = $db->rawQuery("select a.id_order, b.*
                from av_orders a, av_order_detail b 
                where a.id_order = b.id_order
                and a.id_order = ? ", array($order["id_order"]));


                    foreach ($listOrderProduct as $OrderProduct) {
                        ?>
                        <tr>
                            <td><?= $OrderProduct["product_name"] ?></td>
                            <td><?= $OrderProduct["product_name"] ?></td>
                            <td><?= $OrderProduct["product_name"] ?></td>
                            <td>
                                <button id="add" class="btn btn-primary" value="<?=$OrderProduct["product_id"]?>"> Ajouter </button>
                                <button id="del" class="btn btn-primary" value="<?=$OrderProduct["product_id"]?>"> delete </button>
                            </td>
                        </tr>



                        <?
                    }
                    ?>

                </table>
            </td>
        </tr>
        <?
    }
    ?>


</table>
<br />
<br />
<br />
<br />



<?
foreach ($camions as $camion) {

    $nb_produits = 0;
    $montant_produits = 0;
    $poids_produits = 0;
    $montant_transport = 0;
    $volume_produit = 0;
    ?>
    <table border="1">
        <tr>
            <th><?= $camion["id_camion"] ?></th>                
            <th><?= $camion["imma"] ?></th>
            <th><?= $camion["name"] ?></th>
        </tr>
        <tr>
            <td colspan="4">
                <?
                $listOrderProduct = $db->rawQuery("select a.id_order, b.*
                    from av_orders a, av_order_detail b , av_camion_product c
                    where a.id_order = b.id_order
                    and b.id_order_detail = c.id_order_detail 
                    and c.id_camion = ? ", array($camion["id_camion"]))
                ?>
                <table>
                    <?
                    foreach ($listOrderProduct as $OrderProduct) {
                        $p = getProductInfos($OrderProduct["product_id"]);

                        $p_qty = $OrderProduct["product_quantity"];

                        $montant_produits += $OrderProduct["product_price"];
                        $nb_produits += $OrderProduct["product_quantity"];
                        $poids_produits += $p_qty * $p["weight"];
                        $montant_transport += $OrderProduct["product_shipping"];
                        $volume_produit += $p_qty * $p["width"] * $p["height"] * $p["depth"];
                        ?>
                        <tr>
                            <td><?= $p["id_product"] ?></td>
                            <td><?= $OrderProduct["product_quantity"] ?></td>
                            <td><?= $p["name"] ?></td>
                            <td><?= $p["width"] ?> x <?= $p["height"] ?> x <?= $p["depth"] ?></td>
                            <td><?= $p["weight"] ?></td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr>
                        <td>Nb produits</td><td><?= $nb_produits ?></td>
                    </tr>                   
                    <tr>
                        <td>Volume produits</td><td><?= $volume_produit ?> m3</td>
                    </tr>                   
                    <tr>
                        <td>Poids produits</td><td><?= $poids_produits ?> Kg</td>
                    </tr>                   
                    <tr>
                        <td>Montant produits</td><td><?= $montant_produits ?> €</td>
                    </tr>                   
                    <tr>
                        <td>Transport facturé</td><td><?= $montant_transport ?> €</td>
                    </tr>                   

                </table>
            </td>
        </tr>
    </table>
    <?
}
?>
<script>
    $("#add").click(function() {
        var p = 3;
        var action = "add";
        var module = "CamionProduct";

        var func = action + module;

        console.log(func);

        $.ajax({
            url: "functions/ajax_camions.php",
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

    });
    $("#del").click(function() {
        var p = 5;
        var action = "del";
        var module = "CamionProduct";

        var func = action + module;

        console.log(func);

        $.ajax({
            url: "functions/ajax_camions.php",
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

    });
</script>