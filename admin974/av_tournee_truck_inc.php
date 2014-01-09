
<?
require_once ("../configs/settings.php");
require_once ("../functions/products.php");
require_once ("../functions/orders.php");
require_once ("../functions/users.php");
require_once ("functions/truck.php");

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
                            <th><button class="emptytruck" value="<?= $truck["id_truck"] ?>" data-toggle="tooltip" title="Vider le camion"><span class="glyphicon glyphicon-trash"></span></button></th>                
                            <th colspan="2"><?= $truck["name"] ?></th>
                            <th>

                                <a href ="av_truck_preview.php?id_truck=<?= $truck["id_truck"] ?>&planning=<?= $_GET["planning"] ?>" data-toggle="tooltip" title="apercu du camion"><button><span class="glyphicon glyphicon-eye-open"></span></button></a>
                                <a href ="#" onclick="javascript:location.reload()" data-toggle="tooltip" title="Rafraichir le camion"><button><span class="glyphicon glyphicon-refresh"></span></button></a>
                            </th>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <?
                                // on recupère les produits affectés au camion
                                $listOrderProduct = $db->rawQuery("select a.date_add, a.id_customer, a.id_order, a.reference, id_product, b.*, c.*
                                            from av_orders a, av_order_detail b, av_tournee c
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
                                        <td>&nbsp;</td>

                                    </tr>
                                    <?
                                    //on boucle sur les produits
                                    foreach ($listOrderProduct as $OrderProduct) {
                                        $p_qty = $OrderProduct["nb_product_delivered"];
                                        //$o_truck = getOrderInfos($OrderProduct["id_order"]);
                                        $o_customer = getUserOrdersCustomer($OrderProduct["id_customer"]);

                                        $montant_produits += $OrderProduct["product_price"];
                                        $nb_produits += $OrderProduct["nb_product_delivered"];
                                        $poids_produits += $p_qty * $OrderProduct["product_weight"];
                                        //$montant_transport += $OrderProduct["product_shipping"];

                                        if ($tmpRef != $OrderProduct["reference"]) {
                                            $montant_transport += $conf_shipping_amount;
                                            $nb_commandes++;
                                            ?>
                                            <tr>
                                                <th colspan="5">
                                                    <a target="_blank" href="av_orders_view.php?id_order=<?= $OrderProduct["id_order"] ?>">
                                                        <?= $OrderProduct["reference"] ?>
                                                    </a>
                                                    <?= date("d/m", strtotime($OrderProduct["date_add"])) ?> 
                                                    <?= $o_customer["firstname"] ?> <?= $o_customer["lastname"] ?>

                                                </th>
                                            </tr>
                                            <?
                                            $tmpRef = $OrderProduct["reference"];
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $p_qty ?></td>
                                            <td ><?= $OrderProduct["product_name"] ?></td>
                                            <td nowrap><?= $OrderProduct["product_width"] ?> x <?= $OrderProduct["product_height"] ?></td>
                                            <td><?= $p_qty * $OrderProduct["product_weight"] ?> Kg</td>                                        
                                            <td>
                                                <button name="delProduitTruck" class="del_<?= $OrderProduct["id_truck"] ?>" value="<?= $OrderProduct["id_order_detail"] ?>" ><span class="glyphicon glyphicon-remove"></span></button>
                                            </td>                                        
                                        </tr>
                                        <?
                                    }
                                    ?>
                                    <tr>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Nb produits</td><td colspan="2"><?= $nb_produits ?></td>
                                    </tr>                   
                                    <tr>
                                        <td colspan="3">Nb Commandes</td><td colspan="2"><?= $nb_commandes ?></td>
                                    </tr>                   
                                    <tr>
                                        <td colspan="3">Montant produits</td><td colspan="2"><?= $montant_produits ?> €</td>
                                    </tr>                   
                                    <tr>
                                        <td colspan="3">Transport facturé</td><td colspan="2"><?= $montant_transport ?> €</td>
                                    </tr>                   
                                    <tr>
                                        <td colspan="2">Poids. produits</td>
                                        <td nowrap><?= $poids_produits ?> Kg</td>
                                        <td>Poids. restant</td>
                                        <td 
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
