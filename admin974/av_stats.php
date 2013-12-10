<?php
require_once "../configs/settings.php";
include ("header.php");
include ("../functions/users.php");
include ("../functions/tools.php");



$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);
$stda = '';
$enda = '';

if (isset($_POST["stda"]))
    $stda = $_POST["stda"];
if (isset($_POST["enda"]))
    $enda = $_POST["enda"];


if ($stda != "" && $enda) {
    $r = $db->rawQuery("SELECT date(invoice_date) invoice_date, payment, count(1) nb_orders, sum(total_paid)  total_paid
    FROM `av_orders` 
    where ifnull(current_state,0) > 0 
    and date(invoice_date) between ? and ?
    and current_state not in (1,6,7,8)
    group by date(invoice_date), payment  ", array($stda, $enda));
}
?>

<div class="container">
    <div class="row">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">Statistiques</div>
                <div class="panel-body">
                    <form action="?stats" method="post">
                        Début : <input type="text" class="datepicker" value="<?= $stda ?>" name="stda"> 
                        Fin : <input type="text" class="datepicker" value="<?= $enda ?>" name="enda"> 
                        <input type="submit">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">Reporting</div>
                <div class="panel-body">
                    
                    <form action="av_download_pdf.php" method="post"  target="blank">
                        <div>
                            <input type="radio"  name="extract" value="1"> Excel
                            <input type="radio"  name="extract" value="2"> PDF
                        </div>
                        Début : <input type="text" class="datepicker" value="" name="start_date"> 
                        Fin : <input type="text" class="datepicker" value="" name="end_date"> 
                        <input type="submit">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?
    if (!empty($r)) {
        $total_amount = 0;
        $total_nb_order = 0;
        ?>
        <div class="row">
            <table class="table table-condensed">
                <tr>
                    <th>Date</th>
                    <th>Mode de paiement</th>
                    <th>Nb commandes</th>
                    <th>Montant TTC</th>
                </tr>
                <?
                foreach ($r as $row) {
                    $total_amount += $row["total_paid"];
                    $total_nb_order +=$row["nb_orders"];
                    ?>
                    <tr>
                        <td><?= $row["invoice_date"] ?></td>
                        <td><?= $row["payment"] ?></td>
                        <td><?= $row["nb_orders"] ?></td>
                        <td><?= number_format($row["total_paid"], 2, '.', ' ') ?> €</td>
                    </tr>
                    <?
                }
                ?>
                <tr class="alert-info">
                    <td><b>Total sur la période</b></td>
                    <td></td>
                    <td><b><?= $total_nb_order ?> </b></td>
                    <td><b><?= number_format($total_amount, 2, '.', ' ') ?> €</b></td>
                </tr>
            </table>
        </div>
        <?
    }
    ?>

</div>

<script>
    $(function() {
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd"
        });
    });

</script>
