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
// nb devis total
    $r1 = $db->rawQuery("SELECT count( id_devis ) nb
FROM `av_devis`
WHERE date( invoice_date )
BETWEEN ? AND ?
AND current_state
IN ( 1, 2, 3, 4, 5 ) ", array($stda, $enda));

//nb devis validé
    $r2 = $db->rawQuery("SELECT count( id_devis ) nb
FROM `av_devis`
WHERE date( invoice_date )
BETWEEN ? AND ?
AND current_state=4 ", array($stda, $enda));

}

?>

<div class="container">
    <div class="page-header">
        <h1>Statistiques</h1>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">Statistiques Devis </div>
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
    </div>
    <hr>

    <?
    if (!empty($r1)) {
        $total_amount = 0;
        $total_produit_ht = 0;
        $total_nb_order = 0;
        ?>
        <div class="col-xs-6">
            <h2>Stats devis</h2>
            <br/><br/>
			Nombre total de devis générés sur la période : <?php echo $r1[0][nb]; ?>
			
			<br/>Nombre de devis convertis sur la période : <?php echo $r2[0][nb]; ?>
			<br/>Taux de convertion sur la période : <?php echo round(100*$r2[0][nb]/$r1[0][nb]); ?> %
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
