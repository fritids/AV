<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);
$devis = $db->get("av_devis");
$did ="";

if (isset($_POST["id_devis"])) {
    $did = $_POST["id_devis"];
    $r = $db->rawQuery("select * from av_devis_detail where id_devis = ?", array($did));
}
?>

<div class="container">
    <div class="row">
        <form method="post"> 
            <select name="id_devis" class="pme-input-0">
                <?
                foreach ($devis as $dev) {
                    ?>
                    <option value="<?= $dev["id_devis"] ?>"
                    <? if ($dev["id_devis"] == $did && !empty($did)) echo "selected" ?>
                            ><?= $dev["id_devis"] ?> <?= $dev["date_add"] ?></option>
                            <?
                        }
                        ?>
                <input type="submit">
            </select>
        </form>
    </div>
    <?
    if (!empty($did)) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <h2>Liste des produits</h2>
                <table class="table table-bordered table-condensed col-md-12" id="tab_devis">
                    <tr>
                        <th>Produit</th>
                        <th>Option</th>
                        <th>Largeur (mm)</th>
                        <th>Hauteur (mm)</th>
                        <th>Profondeur (mm)</th>
                        <th>Prix Unit.</th>
                        <th>Poids Unit.</th>
                        <th>Quantity</th>
                        <th>Fdp</th>                        
                        <th>Prix ttc</th>                        
                    </tr>
                    <?
                    foreach ($r as $line) {
                        ?>

                        <tr id="id0">
                            <td><?= $line["product_name"] ?></td>
                            <td>&nbsp;</td>
                            <td><?= $line["product_width"] ?></td>
                            <td><?= $line["product_height"] ?></td>
                            <td><?= $line["product_depth"] ?></td>
                            <td><?= $line["product_price"] ?></td>
                            <td><?= $line["product_weight"] ?></td>
                            <td><?= $line["product_quantity"] ?></td>                                                             
                            <td><?= $line["product_shipping"] ?></td>                    
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

