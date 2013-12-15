<?php
require_once "../configs/settings.php";
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");
require('../libs/Smarty.class.php');

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);
$r = $db->rawQuery("select a.* , b.lastname, b.firstname
            from mv_orders a, av_customer b
            where a.id_customer = b.id_customer
            and (ARC_INFO > 0 or RECU_INFO > 0 or COMMANDE_INFO > 0 )
            and id_order not in (select id_order from mv_orders where (ARC_INFO = 5 and RECU_INFO = 5 and COMMANDE_INFO = 5))");
?>
<div class="container">
    <div class="page-header">
        <h1>Tableau de bord des ventes</h1>
    </div>

    <table>
        <tr>
            <td>Nb commandes</td><td><?= count($r) ?></td>
        </tr>
    </table>

    <table class="table table-bordered table-condensed">
        <tr>
            <th class="text-center">Commande</th>
            <th class="text-center">Client</th>            
            <th class="text-center">Montant</th>
            <th class="text-center">Nb jour depuis la vente</th>
            <th class="text-center">ARC</th>
            <th class="text-center">COMM</th>
            <th class="text-center">RECU</th>        
        </tr>
        <?
        foreach ($r as $row) {
            ?>
            <tr>
                <td class="text-center"><a href="av_orders_view.php?id_order=<?= $row["id_order"] ?>"><?= $row["reference"] ?></a></td>
                <td class="text-center"><?= $row["lastname"] . " " . $row["firstname"] ?></td>
                <td class="text-center"><?= number_format($row["total_paid"], 2, ".", " ") ?> €</td>
                <td class="text-center"><?= round((time() - strtotime($row["date_add"])) / 86400) ?> jours</td>
                <td class="text-center alert-<?= $row["ARC_INFO"] ?>">
                    <?
                    switch ($row["ARC_INFO"]) {
                        case 5:
                            echo '<span class="glyphicon glyphicon-ok"></span>';
                            break;
                        case 6:
                            echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                            break;
                        case 8:
                            echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                            break;
                        default :
                    }
                    ?>                        
                </td>
                <td class="text-center alert-<?= $row["COMMANDE_INFO"] ?>">
                    <?
                    switch ($row["COMMANDE_INFO"]) {
                        case 5:
                            echo '<span class="glyphicon glyphicon-ok"></span>';
                            break;
                        case 6:
                            echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                            break;
                        case 8:
                            echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                            break;
                        default :
                    }
                    ?>                        
                </td>
                <td class="text-center alert-<?= $row["RECU_INFO"] ?>">
                    <?
                    switch ($row["RECU_INFO"]) {
                        case 5:
                            echo '<span class="glyphicon glyphicon-ok"></span>';
                            break;
                        case 6:
                            echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                            break;
                        case 8:
                            echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                            break;
                        default :
                    }
                    ?>                        
                </td>

            </tr>
            <?
        }
        ?>

        <tr></tr>
    </table>

</div>