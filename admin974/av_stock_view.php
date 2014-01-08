<?php
require_once "../configs/settings.php";
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$r = $db->where("stock_tracking", 1)
        ->get("av_product");
?>

<div class="container">     
    <div class="page-header">
        <h1>gestion du stock <small>etat des produits</small></h1>
    </div>
    <div>

        <table class="table table-bordered table-condensed">
            <tr>
                <th>#</th>
                <th>Produit</th>
                <th>Quantité</th>
            </tr>
            <?
            foreach ($r as $product) {
                ?>
                <tr>
                    <td><a href="av_product.php?PME_sys_fl=1&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $product["id_product"]?>"><?= $product["id_product"]?></a></td>
                    <td><?= $product["name"]?></td>
                    <td><?= $product["quantity"]?></td>                   
                </tr>
                <?
            }
            ?>
        </table>
    </div>

</div>
