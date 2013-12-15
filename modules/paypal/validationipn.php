<?php

// tell PHP to log errors to ipn_errors.log in this directory
ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__) . '/ipn_errors.log');

// intantiate the IPN listener
include('./ipnlistener.php');
require('../../configs/settings.php');
require('../../classes/MysqliDb.php');
require('../../libs/Smarty.class.php');
require('../../classes/class.phpmailer.php');
require('../../classes/tcpdf.php');
require('../../classes/sms.inc.php');
include("../../functions/products.php");
include("../../functions/orders.php");


$now = date("d-m-y");

//sms
$user_login = 'pei73hyl8trvtivx8rduvg2p@sms-accounts.com';
$api_key = 'PLYvbMEbIhW5zfnQy0Xi';
$sms_type = QUALITE_PRO; // ou encore QUALITE_PRO
$sms_mode = INSTANTANE; // ou encore DIFFERE
$sms_sender = 'ALLOVITRES';


$smarty = new Smarty;
$smarty->addTemplateDir(array('../../templates/mails', '../../templates/', '../../templates/pdf/', '../../templates/pdf/front/'));
$smarty->setCompileDir('../../templates_c/');

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


//Create a new PHPMailer instance
$mail = new PHPMailer();
//Set who the message is to be sent from
$mail->SetFrom($confmail["from"]);
$mail->CharSet = 'UTF-8';

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$listener = new IpnListener();

// tell the IPN listener to use the PayPal test sandbox
$listener->use_sandbox = false;

// try to process the IPN POST
try {
    $listener->requirePostMethod();
    $verified = $listener->processIpn();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit(0);
}

if ($verified) {

    $errmsg = '';   // stores errors from fraud checks
    // 1. Make sure the payment status is "Completed" 
    if ($_POST['payment_status'] != 'Completed') {
        // simply ignore any IPN that is not completed
        exit(0);
    }

    // 2. Make sure seller email matches your primary account email.
    /*
      if ($_POST['receiver_email'] != 'seller@paypalsandbox.com') {
      $errmsg .= "'receiver_email' does not match: ";
      $errmsg .= $_POST['receiver_email'] . "\n";
      }
     */
    /*
      // 3. Make sure the amount(s) paid match
      if ($_POST['mc_gross'] != '9.99') {
      $errmsg .= "'mc_gross' does not match: ";
      $errmsg .= $_POST['mc_gross'] . "\n";
      }

      // 4. Make sure the currency code matches
      if ($_POST['mc_currency'] != 'USD') {
      $errmsg .= "'mc_currency' does not match: ";
      $errmsg .= $_POST['mc_currency'] . "\n";
      } */

    // 5. Ensure the transaction is not a duplicate.
    mysql_connect($bdd_host, $bdd_user, $bdd_pwd) or exit(0);
    mysql_select_db($bdd_name) or exit(0);

    $txn_id = mysql_real_escape_string($_POST['txn_id']);
    $sql = "SELECT COUNT(*) FROM av_orders WHERE txn_id = '$txn_id'";
    $r = mysql_query($sql);

    if (!$r) {
        error_log(mysql_error());
        exit(0);
    }

    $exists = mysql_result($r, 0);
    mysql_free_result($r);

    if ($exists) {
        $errmsg .= "'txn_id' has already been processed: " . $_POST['txn_id'] . "\n";
    }

    if (!empty($errmsg)) {

        // manually investigate errors from the fraud checking
        $body = "IPN failed fraud checks: \n$errmsg\n\n";
        $body .= $listener->getTextReport();
        mail($monitoringEmail, 'IPN Fraud Warning', $body);
    } else {
        $sql = "update av_orders set current_state = 2 , payment= 'Paypal', txn_id = '" . $txn_id . "' 
            where id_order = " . $_POST['invoice'];

        mysql_query($sql);

        $oid = $_POST['invoice'];
        $amount = $_POST['mc_gross'];
        $shipping = $_POST['mc_shipping'];
        $pd = $_POST['payment_date'];

        "insert into av_paypal_order ('id_order','id_transaction',currency','total_paid','shipping','capture','payment_date','payment_method','payment_status')
                values ('" . $oid . " ','" . $txn_id . "','EUR','" . $amount . "','" . $shipping . "','0','" . $pd . "','1','Completed')";


        $order_payment = array(
            "id_order" => $oid,
            "order_reference" => str_pad($oid, 9, '0', STR_PAD_LEFT),
            "id_currency" => 1,
            "amount" => $amount,
            "conversion_rate" => 1,
            "payment_method" => "Paypal",
            "date_add" => date("Y-m-d H:i:s"),
        );

        $db->insert("av_order_payment", $order_payment);

        mail($monitoringEmail, 'Valid IPN ' . $txn_id . " " . $_POST['invoice'], $listener->getTextReport());

        $orderinfo = getOrderInfos($oid);
        
        $mail->AddAddress($orderinfo["customer"]["email"]);
        $mail->Subject = $confmail["commande_new"] . " " . $oid;

        $smarty->assign("orderinfo", $orderinfo);

        $mail_content = $smarty->fetch('notif_order_payment.tpl');
        $invoice = $smarty->fetch('front_order.tpl');

        $pdf_file = "AV_FA_" . $oid . "_" . $now . ".pdf";

        $pdf->AddPage('P', 'A4');
        $pdf->writeHTML($invoice, true, false, true, false, '');
        if ($orderinfo["nb_custom_product"] > 0) {
            $annexe_body = $smarty->fetch('front_annexe.tpl');
            $pdf->AddPage('P', 'A4');
            $pdf->writeHTML($annexe_body, true, false, true, false, '');
        }
        $pdf->lastPage();
        $pdf->Output("../../tmp/" . $pdf_file, 'F');
        $mail->MsgHTML($mail_content);
        $mail->AddAttachment("../../tmp/" . $pdf_file);
        foreach ($monitoringEmails as $bccer) {
            $mail->AddBCC($bccer);
        }
        if ($mail->Send()) {
            $param = array(
                "id_order" => $oid,
                "id_user" => 0,
                "category" => "mail_commande",
            );
            $r = $db->insert("av_order_bdc", $param);
        }

        unlink("../../tmp/" . $pdf_file);

        //sms
        if ($orderinfo["alert_sms"] == 1) {
            $order_sms_text = "Bonjour, nous vous remercions pour votre commande, votre facture vous a été transmise par mail. L'équipe Allovitres.";
            $sms = new SMS();
            $sms->set_user_login($user_login);
            $sms->set_api_key($api_key);
            $sms->set_sms_mode($sms_mode);
            $sms->set_sms_text($order_sms_text);
            $sms->set_sms_recipients(array($orderinfo["alert_sms_phone"]));
            $sms->set_sms_type($sms_type);
            $sms->set_sms_sender($sms_sender);
            $sms->send();

            $param = array(
                "id_order" => $oid,
                "id_user" => 0,
                "category" => "sms_mail_commande",
            );
            $r = $db->insert("av_order_bdc", $param);
        }
    }
} else {
    // manually investigate the invalid IPN
    mail($monitoringEmail, 'Invalid IPN', $listener->getTextReport());
}
?>