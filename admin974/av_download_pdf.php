<?php

session_start();
require_once ("../configs/settings.php");
require_once ("./av_utilities.php");
require_once ("./securite.php");
require_once ("../functions/products.php");
require_once ("../functions/devis.php");
require_once ("../functions/users.php");
require_once ("../functions/tools.php");
require_once ("../functions/orders.php");
require_once ('../classes/tcpdf.php');
require_once ('../libs/Smarty.class.php');

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$smarty = new Smarty;
$smarty->setTemplateDir(array('../templates', '../templates/mails', '../templates/pdf/front'));
$smarty->setCompileDir("../templates_c");

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Allovitre');
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetFont('times', '', 10);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING);

$now = date("Y-M-d");



if (isset($_GET["devis"]) && isset($_POST["id_devis"])) {
    $did = $_POST["id_devis"];

    $devisinfo = getDevis($did);
    $smarty->assign("devisinfo", $devisinfo[0]);
    $content_body = $smarty->fetch('front_devis.tpl');

    $pdf->AddPage('P', 'A4');
    $pdf->writeHTML($content_body, true, false, true, false, '');
    $pdf->lastPage();
    $filename = "AV_DE_" . $did . "_" . $now . ".pdf";
    $pdf->Output($filename, 'D');
}
if (isset($_GET["order"]) && isset($_POST["id_order"])) {
    $oid = $_POST["id_order"];

    $orderinfo = getOrderInfos($oid);

    $smarty->assign("orderinfo", $orderinfo);
    $content_body = $smarty->fetch('front_order.tpl');
    $annexe_body = $smarty->fetch('front_annexe.tpl');

    $pdf->AddPage('P', 'A4');
    $pdf->writeHTML($content_body, true, false, true, false, '');
    $pdf->AddPage('P', 'A4');
    $pdf->writeHTML($annexe_body, true, false, true, false, '');
    $pdf->lastPage();
    $filename = "AV_FA_" . $oid . "_" . $now . ".pdf";
    $pdf->Output($filename, 'D');
    //echo $annexe_body;
}
if (isset($_GET["all_orders"]) && isset($_POST["start_date"]) && isset($_POST["end_date"])) {
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];

    $r = $db->rawQuery("select id_order from av_orders where ifnull(current_state,0) != 0 and invoice_date between ? and ?", array($start_date, $end_date));

    if ($r) {
        foreach ($r as $order) {
            $orderinfo = getOrderInfos($order["id_order"]);

            $smarty->assign("orderinfo", $orderinfo);
            $content_body = $smarty->fetch('front_order.tpl');

            $pdf->AddPage('P', 'A4');
            $pdf->writeHTML($content_body, true, false, true, false, '');
        }
        $pdf->lastPage();
        $filename = "AV_FA_" . $start_date . "_" . $end_date . "_" . $now . ".pdf";
        $pdf->Output($filename, 'D');
    }else{
        echo "pas de résultat.";
    }
}
?>