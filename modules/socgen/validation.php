<?php

require_once("../../configs/settings.php");
require('../../classes/MysqliDb.php');
require('../../libs/Smarty.class.php');
require('../../classes/class.phpmailer.php');
require('../../classes/tcpdf.php');
require('../../classes/sms.inc.php');
include ("../../functions/products.php");
include ("../../functions/orders.php");
include ("../../functions/voucher.php");


//sms
$user_login = 'pei73hyl8trvtivx8rduvg2p@sms-accounts.com';
$api_key = 'PLYvbMEbIhW5zfnQy0Xi';
$sms_type = QUALITE_PRO; // ou encore QUALITE_PRO
$sms_mode = INSTANTANE; // ou encore DIFFERE
$sms_sender = 'ALLOVITRES';

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$smarty = new Smarty;
$smarty->addTemplateDir(array('../../templates/mails', '../../templates/', '../../templates/pdf/', '../../templates/pdf/front/'));
$smarty->setCompileDir('../../templates_c/');

/* init pdf */
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

$now = date("d-m-y");

$message = "message=" . $_REQUEST["DATA"];
$pathfile = "pathfile=/trusttelecom.fr/paiement/param/pathfile";
$path_bin = "/trusttelecom.fr/paiement/bin/response";
$message = escapeshellcmd($message);
$result = exec("$path_bin $pathfile $message");

$tableau = explode("!", $result);

$code = $tableau[1];
$error = $tableau[2];
$merchant_id = $tableau[3];
$merchant_country = $tableau[4];
$amount = $tableau[5];
$transaction_id = $tableau[6];
$payment_means = $tableau[7];
$transmission_date = $tableau[8];
$payment_time = $tableau[9];
$payment_date = $tableau[10];
$response_code = $tableau[11];
$payment_certificate = $tableau[12];
$authorisation_id = $tableau[13];
$currency_code = $tableau[14];
$card_number = $tableau[15];
$cvv_flag = $tableau[16];
$cvv_response_code = $tableau[17];
$bank_response_code = $tableau[18];
$complementary_code = $tableau[19];
$complementary_info = $tableau[20];
$return_context = $tableau[21];
$caddie = $tableau[22];
$receipt_complement = $tableau[23];
$merchant_language = $tableau[24];
$language = $tableau[25];
$customer_id = $tableau[26];
$order_id = $tableau[27];
$customer_email = $tableau[28];
$customer_ip_address = $tableau[29];
$capture_day = $tableau[30];
$capture_mode = $tableau[31];
$data = $tableau[32];
$order_validity = $tableau[33];
$transaction_condition = $tableau[34];
$statement_reference = $tableau[35];
$card_validity = $tableau[36];
$score_value = $tableau[37];
$score_color = $tableau[38];
$score_info = $tableau[39];
$score_threshold = $tableau[40];
$score_profile = $tableau[41];

$logfile = "/trusttelecom.fr/paiement/logfile.txt";
// Ouverture du fichier de log en append
$fp = fopen($logfile, "a");

if (( $code == "" ) && ( $error == "" )) {
    fwrite($fp, "erreur appel response\n");
    print ("executable response non trouve $path_bin\n");
} else if ($code != 0) {
    fwrite($fp, " API call error.\n");
    fwrite($fp, "Error message :  $error\n");
} else {

    mysql_connect($bdd_host, $bdd_user, $bdd_pwd) or exit(0);
    mysql_select_db($bdd_name) or exit(0);

    // OK, Sauvegarde des champs de la réponse

    fwrite($fp, "merchant_id : $merchant_id\n");
    fwrite($fp, "merchant_country : $merchant_country\n");
    fwrite($fp, "amount : $amount\n");
    fwrite($fp, "transaction_id : $transaction_id\n");
    fwrite($fp, "transmission_date: $transmission_date\n");
    fwrite($fp, "payment_means: $payment_means\n");
    fwrite($fp, "payment_time : $payment_time\n");
    fwrite($fp, "payment_date : $payment_date\n");
    fwrite($fp, "response_code : $response_code\n");
    fwrite($fp, "payment_certificate : $payment_certificate\n");
    fwrite($fp, "authorisation_id : $authorisation_id\n");
    fwrite($fp, "currency_code : $currency_code\n");
    fwrite($fp, "card_number : $card_number\n");
    fwrite($fp, "cvv_flag: $cvv_flag\n");
    fwrite($fp, "cvv_response_code: $cvv_response_code\n");
    fwrite($fp, "bank_response_code: $bank_response_code\n");
    fwrite($fp, "complementary_code: $complementary_code\n");
    fwrite($fp, "complementary_info: $complementary_info\n");
    fwrite($fp, "return_context: $return_context\n");
    fwrite($fp, "caddie : $caddie\n");
    fwrite($fp, "receipt_complement: $receipt_complement\n");
    fwrite($fp, "merchant_language: $merchant_language\n");
    fwrite($fp, "language: $language\n");
    fwrite($fp, "customer_id: $customer_id\n");
    fwrite($fp, "order_id: $order_id\n");
    fwrite($fp, "customer_email: $customer_email\n");
    fwrite($fp, "customer_ip_address: $customer_ip_address\n");
    fwrite($fp, "capture_day: $capture_day\n");
    fwrite($fp, "capture_mode: $capture_mode\n");
    fwrite($fp, "data: $data\n");
    fwrite($fp, "order_validity: $order_validity\n");
    fwrite($fp, "transaction_condition: $transaction_condition\n");
    fwrite($fp, "statement_reference: $statement_reference\n");
    fwrite($fp, "card_validity: $card_validity\n");
    fwrite($fp, "card_validity: $score_value\n");
    fwrite($fp, "card_validity: $score_color\n");
    fwrite($fp, "card_validity: $score_info\n");
    fwrite($fp, "card_validity: $score_threshold\n");
    fwrite($fp, "card_validity: $score_profile\n");
    fwrite($fp, "-------------------------------------------\n");


    $oid = $order_id;

    if ($bank_response_code == "00") {

        $status = 2;

        $amount = $amount / 100;

        $sql = "update av_orders set current_state = " . $status . " , payment= 'Carte credit' where id_order = " . $oid;
        mysql_query($sql);

        $order_payment = array(
            "id_order" => $oid,
            "order_reference" => str_pad($oid, 9, '0', STR_PAD_LEFT),
            "id_currency" => 1,
            "amount" => $amount,
            "conversion_rate" => 1,
            "payment_method" => "Carte credit",
            "date_add" => date("Y-m-d H:i:s"),
        );

        $db->insert("av_order_payment", $order_payment);

        createInvoice($oid);
        
        $orderinfo = getOrderInfos($oid);

        updQuantity($oid);
        

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

        mail($monitoringEmail, 'monitoring - Valid CB ' . $oid);

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

        // on retire 1 à nombre coupon utilisable
        if ($orderinfo["order_voucher"] != "") {
            $code = $orderinfo["order_voucher"];
            $cid = $orderinfo["id_customer"];
            updVoucherCodeQty($code, $cid);
        }
    } else {

        $status = 8;
        $sql = "update av_orders set current_state = " . $status . " , payment= 'Carte credit' where id_order = " . $oid;
        mysql_query($sql);

        mail($monitoringEmail, 'Annulation CB ' . $oid);
    }
}
fclose($fp);
?>
