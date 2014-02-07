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

/* if ($_POST["extract"] == 2 && isset($_POST["start_date"]) && isset($_POST["end_date"])) {
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
  } */

if ($_POST["reporting"] == "Ventes" && isset($_POST["start_date"]) && isset($_POST["end_date"])) {

    $filename = "tmp/av_orders_" . $_POST["start_date"] . "-" . $_POST["end_date"] . ".xls";

    $stmt = $db2->prepare("SELECT date(d.invoice_date) date_commande, 
                        LPAD(reference, 9, '0') ref_commande,
                        LPAD(id_order_invoice, 9, '0') ref_facture, 
                        concat(a.id_order, '-', lastname, ' ', firstname) client, 
                        frais_de_port_ht,                        
                        total_ht,
                        montant_tva_196,                        
                        montant_tva_20,                        
                        total_paid,                        
                        payment,
                        '' compte
                        FROM  mv_orders a, av_customer b, av_order_invoice d
                        WHERE a.id_customer = b.id_customer
                        and a.id_order = d.id_order
                        and date(d.invoice_date) between ? and ?
                        and current_state in (2,3,4,5,7)
                        ");

    $stmt->execute(array($_POST["start_date"], $_POST["end_date"]));

    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $excel = new ExportDataExcel('file');
    $excel->filename = $filename;
    $excel->initialize();
    $header = array_keys($r[0]);

    $excel->addRow($header);

    $now = date("Y-m-d H:i:s");
    $batch = "VENTES";
    $sth = $db->rawQuery("select max(batch_no)+1 batch_no from av_accounting_summary where batch_name = ?", array($batch));

    if (empty($sth[0]["batch_no"]))
        $sth[0]["batch_no"] = 100;

    $batch_no = $sth[0]["batch_no"];

    foreach ($r as $record) {
        $payment = $record["payment"];

        $entries = $db2->query("select * from av_accounting_entries where batch_name = '" . $batch . "' order by 1");

        foreach ($entries as $entry) {
            $debit_amount = 0;
            $credit_amount = 0;

            if (empty($entry["account"])) {
                if ($payment == 'Chèque') {
                    $payment_account = "58500000";
                } elseif ($payment == 'Virement bancaire') {
                    $payment_account = "58200000";
                } elseif ($payment == 'Credit card') {
                    $payment_account = "58300000";
                } elseif ($payment == 'Carte credit') {
                    $payment_account = "58300000";
                } elseif (strtolower($payment) == 'paypal') {
                    $payment_account = "58400000";
                } elseif ($payment == 'Manuel') {
                    $payment_account = "58300000";
                } else {
                    $payment_account = "585000xx";
                }
            } else {
                $payment_account = $entry["account"];
            }

            $amount = $record[$entry["calculation"]];

            if ($amount > 0) {
                if ($entry["sens"] == 'C')
                    $credit_amount = $amount;

                if ($entry["sens"] == 'D')
                    $debit_amount = $amount;

                $date_invoice = strftime("%Y/%m/%d", strtotime($record["date_commande"]));
                $ref_facture = $record["ref_facture"];
                $client = $record["client"];
                $compte = $payment_account;
                $debit = $debit_amount;
                $credit = $credit_amount;

                $output = array($date_invoice, $ref_facture, $client, $compte, $debit, $credit);

                $ouputParams = array(
                    "entry_name" => $entry["name"],
                    "entry_calculation" => $entry["calculation"],
                    "date_add" => $now,
                    "batch_name" => $batch,
                    "batch_no" => $batch_no,
                    "output" => implode(",", $output),
                );

                $db->insert("av_accounting_output", $ouputParams);
            }
        }
        //$excel->addRow($record);
        $summaryParams = array(
            "batch_name" => $batch,
            "batch_no" => $batch_no,
            "id_user" => $_SESSION["user_id"],
            "date_add" => $now,
        );
        $db->insert("av_accounting_summary", $summaryParams);
    }
    $data = "";
    $output = $db2->query("select * from av_accounting_output where batch_name = '" . $batch . "' and  batch_no = " . $batch_no);
    foreach ($output as $o) {
        $data .= $o["output"] . "\n";
    }
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=AV_" . $batch . "_" . $batch_no . ".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    print $data;
}

if ($_POST["reporting"] == "Remboursement" && isset($_POST["start_date"]) && isset($_POST["end_date"])) {

    $filename = "tmp/av_orders_" . $_POST["start_date"] . "-" . $_POST["end_date"] . ".xls";

    $stmt = $db2->prepare("SELECT date(date_refund) date_commande, 
                        LPAD(id_order, 9, '0') ref_commande,
                        LPAD(id_order_refund, 9, '0') ref_facture, 
                        concat(id_order, '-', lastname, ' ', firstname) client, 
                        frais_de_port_ht,                        
                        total_ht,
                        montant_tva_196,                        
                        montant_tva_20,                        
                        total_refund,                        
                        payment,
                        '' compte
                        FROM  mv_order_refund
                        WHERE date(date_refund) between ? and ?
                        ");

    $stmt->execute(array($_POST["start_date"], $_POST["end_date"]));

    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $excel = new ExportDataExcel('file');
    $excel->filename = $filename;
    $excel->initialize();
    $header = array_keys($r[0]);

    $excel->addRow($header);

    $now = date("Y-m-d H:i:s");
    $batch = "REMBOURSEMENTS";
    $sth = $db->rawQuery("select max(batch_no)+1 batch_no from av_accounting_summary where batch_name = ?", array($batch));

    if (empty($sth[0]["batch_no"]))
        $sth[0]["batch_no"] = 100;

    $batch_no = $sth[0]["batch_no"];

    foreach ($r as $record) {
        $payment = $record["payment"];

        $entries = $db2->query("select * from av_accounting_entries where batch_name = '" . $batch . "' order by 1");


        
        foreach ($entries as $entry) {
            $debit_amount = 0;
            $credit_amount = 0;                   
                    
            if (empty($entry["account"])) {
                if ($payment == 'Chèque') {
                    $payment_account = "58500000";
                } elseif ($payment == 'Virement bancaire') {
                    $payment_account = "58200000";
                } elseif ($payment == 'Credit card') {
                    $payment_account = "58300000";
                } elseif ($payment == 'Carte credit') {
                    $payment_account = "58300000";
                } elseif (strtolower($payment) == 'paypal') {
                    $payment_account = "58400000";
                } elseif ($payment == 'Manuel') {
                    $payment_account = "58300000";
                } else {
                    $payment_account = "585000xx";
                }
            } else {
                $payment_account = $entry["account"];
            }

            $amount = $record[$entry["calculation"]];

            if ($amount > 0) {
                if ($entry["sens"] == 'C')
                    $credit_amount = $amount;

                if ($entry["sens"] == 'D')
                    $debit_amount = $amount;

                $date_invoice = strftime("%Y/%m/%d", strtotime($record["date_commande"]));
                $ref_facture = $record["ref_facture"];
                $client = $record["client"];
                $compte = $payment_account;
                $debit = $debit_amount;
                $credit = $credit_amount;

                $output = array($date_invoice, $ref_facture, $client, $compte, $debit, $credit);

                $ouputParams = array(
                    "entry_name" => $entry["name"],
                    "entry_calculation" => $entry["calculation"],
                    "date_add" => $now,
                    "batch_name" => $batch,
                    "batch_no" => $batch_no,
                    "output" => implode(",", $output),
                );

                
                $db->insert("av_accounting_output", $ouputParams);
            }
        }
        //$excel->addRow($record);
        $summaryParams = array(
            "batch_name" => $batch,
            "batch_no" => $batch_no,
            "id_user" => $_SESSION["user_id"],
            "date_add" => $now,
        );
        $db->insert("av_accounting_summary", $summaryParams);
    }
    $data = "";
    $output = $db2->query("select * from av_accounting_output where batch_name = '" . $batch . "' and  batch_no = " . $batch_no);
    foreach ($output as $o) {
        $data .= $o["output"] . "\n";
    }
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=AV_" . $batch . "_" . $batch_no . ".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    print $data;
}
?>