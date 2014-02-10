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


if ($stda != "" && $enda != '') {
    $r1 = $db->rawQuery("SELECT date(b.invoice_date) invoice_date, payment, count(1) nb_orders, sum((total_paid-25)/(1+vat_rate/100)) total_produit_ht, sum(total_paid)  total_paid
    FROM `av_orders` a, av_order_invoice b
    where a.id_order = b.id_order 
    and ifnull(current_state,0) > 0 
    and date(b.invoice_date) between ? and ?
    and current_state in (2,3,4,5)
    group by date(invoice_date), payment  ", array($stda, $enda));

    $r2 = $db->rawQuery("SELECT date(b.invoice_date) invoice_date, count(1) nb_orders, sum((total_paid-25)/(1+vat_rate/100)) total_produit_ht, sum(total_paid)  total_paid
    FROM `av_orders` a, av_order_invoice b
    where a.id_order = b.id_order 
    and ifnull(current_state,0) > 0 
    and date(b.invoice_date) between ? and ?
    and current_state in (2,3,4,5)
    group by date(invoice_date) ", array($stda, $enda));
}
?>

<div class="container">
    <div class="page-header">
        <h1>Statistiques & reporting</h1>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">Statistiques des ventes </div>
                <div class="panel-body">
                    <form action="?stats" method="post" class="form-horizontal">
                        <div class="form-group">
                            <div class="col-xs-6">
                                Début : <input type="text" class="datepicker" value="<?= $stda ?>" name="stda"> 
                            </div>                       
                            <div class="col-xs-6">
                                Fin : <input type="text" class="datepicker" value="<?= $enda ?>" name="enda"> 
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <input type="submit" class="btn btn-block btn-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">Reporting comptable</div>
                <div class="panel-body">                
                    <form action="av_download_pdf.php" method="post"  target="blank" class="form-horizontal">
                        <div class="form-group">
                            <div class="col-xs-6">
                                Début : <input type="text" class="datepicker" value="" name="start_date"> 
                            </div> 
                            <div class="col-xs-6">
                                Fin : <input type="text" class="datepicker" value="" name="end_date"> 
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-6">
                                <input type="submit" name="reporting" value="Ventes" class="btn btn-block btn-primary">                                
                            </div>
                            <div  class="col-xs-6">
                                <input type="submit" name="reporting" value="Remboursement" class="btn btn-block btn-primary">                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <hr>

    <?
    if (!empty($r1)) {
        $total_amount = 0;
        $total_produit_ht = 0;
        $total_nb_order = 0;
        ?>
        <div class="col-xs-6">
            <h2>Agrégé par date et mode de paiement</h2>
            <table class="table table-bordered table-condensed table-striped">
                <tr>
                    <th>Date</th>
                    <th>Paiement</th>
                    <th>Nb comm.</th>
                    <th>Mnt produit HT (hors fdp)</th>
                    <th>Mnt TTC</th>
                </tr>
                <?
                foreach ($r1 as $row) {
                    $total_amount += $row["total_paid"];
                    $total_produit_ht += $row["total_produit_ht"];
                    $total_nb_order +=$row["nb_orders"];
                    ?>
                    <tr>
                        <td><?= $row["invoice_date"] ?></td>
                        <td><?= $row["payment"] ?></td>
                        <td><?= $row["nb_orders"] ?></td>
                        <td><?= number_format($row["total_produit_ht"], 2, '.', ' ') ?> €</td>
                        <td><?= number_format($row["total_paid"], 2, '.', ' ') ?> €</td>
                    </tr>
                    <?
                }
                ?>
                <tr class="alert-info">
                    <td><b>Total sur la période</b></td>
                    <td></td>
                    <td><b><?= $total_nb_order ?> </b></td>
                    <td><b><?= number_format($total_produit_ht, 2, '.', ' ') ?> €</b></td>
                    <td><b><?= number_format($total_amount, 2, '.', ' ') ?> €</b></td>
                </tr>
            </table>
        </div>
        <?
    }
    ?>

    <?
    if (!empty($r2)) {
        $total_amount = 0;
        $total_produit_ht = 0;
        $total_nb_order = 0;
        ?>  

        <div class="col-xs-6">
            <h2>Agrégé par date</h2>
            <table class="table table-bordered table-condensed table-striped">
                <tr>
                    <th>Date</th>
                    <th>Nb comm.</th>
                    <th>Montant produit HT (hors fdp)</th>
                    <th>Montant TTC</th>
                </tr>
                <?
                foreach ($r2 as $row) {
                    $total_amount += $row["total_paid"];
                    $total_produit_ht += $row["total_produit_ht"];
                    $total_nb_order +=$row["nb_orders"];
                    ?>
                    <tr>
                        <td><?= $row["invoice_date"] ?></td>
                        <td><?= $row["nb_orders"] ?></td>
                        <td><?= number_format($row["total_produit_ht"], 2, '.', ' ') ?> €</td>
                        <td><?= number_format($row["total_paid"], 2, '.', ' ') ?> €</td>
                    </tr>
                    <?
                }
                ?>
                <tr class="alert-info">
                    <td><b>Total sur la période</b></td>                    
                    <td><b><?= $total_nb_order ?> </b></td>
                    <td><b><?= number_format($total_produit_ht, 2, '.', ' ') ?> €</b></td>
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
