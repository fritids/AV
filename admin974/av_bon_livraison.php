<?php
//error_reporting(0);
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
require('../libs/Smarty.class.php');
require('../classes/tcpdf.php');


$smarty = new Smarty;
$smarty->caching = 0;
//$smarty->error_reporting = E_ALL & ~E_NOTICE;
$smarty->setTemplateDir(array('../templates', '../templates/mails/', '../templates/mails/admin', '../templates/pdf', '../templates/pdf/admin'));
$bl_pdf_body = "";
$order_path = "";
$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);


$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
);

$dns = 'mysql:host=' . $bdd_host . ';dbname=' . $bdd_name;
$db2 = new PDO($dns, $bdd_user, $bdd_pwd, $options);


$d = $db->rawQuery("select distinct date_livraison from av_tournee order by 1 desc");
$t = $db->rawQuery("select distinct date_livraison, a.id_truck, b.name from av_tournee a, av_truck b where a.id_truck = b.id_truck order by 1,2");
$date_delivery = "";
$id_truck = "";
$bl_commande_filename = "";
if (isset($_POST) && !(empty($_POST))) {
    $date_delivery = $_POST["date_delivery"];
    $id_truck = $_POST["id_truck"];
}
if (isset($_GET) && !(empty($_GET))) {
    $date_delivery = $_GET["date_livraison"];
    $id_truck = $_GET["id_truck"];
}
?>

<div class="container">
    <div class="page-header">
        <h1>Bon de livraison</h1>

    </div>
    <div class="text-center">

        <?
        if (!empty($date_delivery) && !empty($id_truck)) {
            $stmtOrder = $db2->prepare("
                        SELECT distinct a.id_order
                        FROM  av_tournee a
                        WHERE a.date_livraison = ?
                        and a.id_truck = ?
                        order by a.position
                        ");

            $stmtOrderDetail = $db2->prepare("select c.id_order, c.id_order_detail
                        FROM  av_tournee a, av_order_detail c
                        WHERE a.id_order_detail = c.id_order_detail
                        and a.date_livraison = ?
                        and a.id_truck = ?
                        and a.id_order = ?                        
                        ");

            $stmtOrder->execute(array($date_delivery, $id_truck));

            $r = $stmtOrder->fetchAll(PDO::FETCH_ASSOC);

            $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Allovitre');
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            $pdf->SetFont('times', '', 11);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING);

            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            $bl_commande_filename = md5(rand());

            foreach ($r as $order) {
                $OrderDetails = array();
                $pdf->AddPage();

                $orderinfo = getOrderInfos($order["id_order"]);
                $oid = $order["id_order"];
                // print_r($orderinfo);                

                $stmtOrderDetail->execute(array($date_delivery, $id_truck, $oid));
                $tmpOds = $stmtOrderDetail->fetchAll(PDO::FETCH_ASSOC);

                foreach ($tmpOds as $ods) {
                    $OrderDetails[] = getOrdersDetail($ods["id_order_detail"]);
                }

                //print_r($OrderDetails);


                $smarty->assign("orderinfo", $orderinfo);
                $smarty->assign("orderdetails", $OrderDetails);

                $bl_pdf_body = $smarty->fetch('admin_bon_livraison.tpl');


                $pdf->writeHTML($bl_pdf_body, true, false, true, false, '');

                foreach ($tmpOds as $k => $ods) {
                    $param = array(
                        "id_order" => $oid,
                        "id_user" => $_SESSION["user_id"],
                        "id_order_detail" => $ods["id_order_detail"],
                        "category" => "bl",
                        "bdc_filename" => $bl_commande_filename
                    );
                    $r = $db->insert("av_order_bdc", $param);
                }
            }

            $pdf->lastPage();

            $path = "./ressources/bon_de_livraison";
            $order_path = $path;
//$bdc_commande_filename = "BDC_" . $orderSupplier["id_supplier"] . "_" . $orderinfo["id_order"] . "_" . date("dMy") ;

            @mkdir($order_path);
            $pdf->Output($order_path . "/" . $bl_commande_filename . ".pdf", 'F');
            ?>

            <a href = "<?= $order_path ?>/<?= $bl_commande_filename ?>.pdf" class="btn btn-lg btn-primary" target = "_blank"><span class="glyphicon glyphicon-download"></span> Télécharger</a>
            <?
        } else {
            ?>


            <form action="" method="post">
                <div class="col-md-12"> 
                    <div class="col-md-3">
                        <label for="date_delivery" > Date livraison :
                            <select id="date_delivery" class="pme-input-0" name="date_delivery">
                                <option value="--"  >--</option>
                                <?
                                foreach ($d as $rec) {
                                    ?>
                                    <option value="<?= $rec["date_livraison"] ?>"  ><?= $rec["date_livraison"] ?></option>
                                    <?
                                }
                                ?>
                            </select>
                    </div>

                    <div class="col-md-3">
                        <label for="truck" > Camion :
                            <select id="truck" class="pme-input-0" name="id_truck">
                                <option value="--"  >--</option>
                                <?
                                foreach ($t as $rec) {
                                    ?>
                                    <option value="<?= $rec["id_truck"] ?>" class="<?= $rec["date_livraison"] ?>" ><?= $rec["name"] ?></option>
                                    <?
                                }
                                ?>
                            </select>
                    </div>

                    <div class="col-md-2">
                        <input type="submit" class="btn btn-primary" value="Télécharger"  />

                    </div>
                </div>
            </form>

            <script>
                $("#truck").chained("#date_delivery");
            </script>
            <?
        }
        ?>
    </div>
</div>