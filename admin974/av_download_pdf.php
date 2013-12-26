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
require "../classes/php-export-data.class.php";

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
);

$dns = 'mysql:host=' . $bdd_host . ';dbname=' . $bdd_name;
$db2 = new PDO($dns, $bdd_user, $bdd_pwd, $options);


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


    $pdf->AddPage('P', 'A4');
    $pdf->writeHTML($content_body, true, false, true, false, '');

    if ($orderinfo["nb_custom_product"] > 0) {
        $annexe_body = $smarty->fetch('front_annexe.tpl');
        $pdf->AddPage('P', 'A4');
        $pdf->writeHTML($annexe_body, true, false, true, false, '');
    }
    $pdf->lastPage();
    $filename = "AV_FA_" . $oid . "_" . $now . ".pdf";
    $pdf->Output($filename, 'D');
    //echo $annexe_body;
}
if ($_POST["extract"] == 2 && isset($_POST["start_date"]) && isset($_POST["end_date"])) {
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];

    $r = $db->rawQuery("select id_order from av_orders where ifnull(current_state,0) != 0 and date(invoice_date) between ? and ?", array($start_date, $end_date));

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
    } else {
        echo "pas de résultat.";
    }
}

if ($_POST["extract"] == 1 && isset($_POST["start_date"]) && isset($_POST["end_date"])) {

    $filename = "tmp/av_orders_" . $_POST["start_date"] . "-" . $_POST["end_date"] . ".xls";

    $stmt = $db2->prepare("SELECT d.invoice_date date_commande, LPAD(reference, 9, '0') reference, lastname nom, firstname prenom, LPAD(d.id_order_invoice, 9, '0') facture, email, address1, city ville,   
                        round(25/(1+vat_rate/100),2) frais_de_port_HT,                        
                        round(total_paid/(1+vat_rate/100), 2) Total_HT,
                        total_paid Total_TTT,
                        vat_rate Tva,
                        payment,
                        '' compte
                        FROM  mv_orders a, av_customer b, av_address c, av_order_invoice d
                        WHERE a.id_customer = b.id_customer
                        and a.id_address_invoice = c.id_address
                        and a.id_order = d.id_order
                        and date(d.invoice_date) between ? and ?
                        and current_state not in (1,6,7,8)
                        ");

    $stmt->execute(array($_POST["start_date"], $_POST["end_date"]));

    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $excel = new ExportDataExcel('file');
    $excel->filename = $filename;
    $excel->initialize();
    $header = array_keys($r[0]);

    $excel->addRow($header);

    foreach ($r as $record) {
        $payment = $record["payment"];
        
        if($payment == 'Chèque'){
            $payment_account = "58500000";
        }elseif ($payment == 'Virement Bancaire'){
            $payment_account = "58200000";
        }elseif ($payment == 'Credit card'){
            $payment_account = "58300000";
        }elseif ($payment == 'Carte credit'){
            $payment_account = "58300000";
        }elseif (strtolower ($payment) == 'paypal'){
            $payment_account = "58400000";
        }elseif ($payment == 'Manuel'){
            $payment_account = "58300000";
        }else{
            $payment_account = "585000xx";
        }  
        
        $record["compte"] = $payment_account;
        $excel->addRow($record);
    }

    $excel->finalize();

    header("Location: " . $filename);
}
?>