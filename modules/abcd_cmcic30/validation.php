<?php

header("Pragma: no-cache");
header("Content-type: text/plain");
require_once("../../configs/settings.php");
require_once("../../classes/CMCIC_Tpe.inc.php");
require('../../classes/MysqliDb.php');
require('../../libs/Smarty.class.php');
require('../../classes/class.phpmailer.php');
require('../../classes/tcpdf.php');
require('../../classes/sms.inc.php');
include ("../../functions/products.php");
include ("../../functions/orders.php");
include ("../../functions/voucher.php");

$now = date("d-m-y");

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

// Begin Main : Retrieve Variables posted by CMCIC Payment Server 
$CMCIC_bruteVars = getMethode();

// TPE init variables
$oTpe = new CMCIC_Tpe();
$oHmac = new CMCIC_Hmac($oTpe);

// Message Authentication
$cgi2_fields = sprintf(CMCIC_CGI2_FIELDS, $oTpe->sNumero, $CMCIC_bruteVars["date"], $CMCIC_bruteVars['montant'], $CMCIC_bruteVars['reference'], $CMCIC_bruteVars['texte-libre'], $oTpe->sVersion, $CMCIC_bruteVars['code-retour'], $CMCIC_bruteVars['cvx'], $CMCIC_bruteVars['vld'], $CMCIC_bruteVars['brand'], $CMCIC_bruteVars['status3ds'], $CMCIC_bruteVars['numauto'], $CMCIC_bruteVars['motifrefus'], $CMCIC_bruteVars['originecb'], $CMCIC_bruteVars['bincb'], $CMCIC_bruteVars['hpancb'], $CMCIC_bruteVars['ipclient'], $CMCIC_bruteVars['originetr'], $CMCIC_bruteVars['veres'], $CMCIC_bruteVars['pares']);


if ($oHmac->computeHmac($cgi2_fields) == strtolower($CMCIC_bruteVars['MAC']) || $CMCIC_bruteVars['MAC'] == 'sandbox') {

    mysql_connect($bdd_host, $bdd_user, $bdd_pwd) or exit(0);
    mysql_select_db($bdd_name) or exit(0);

    $oid = $CMCIC_bruteVars['reference'];

    switch ($CMCIC_bruteVars['code-retour']) {
        case "Annulation" :
            $status = 8;
            $sql = "update av_orders set current_state = " . $status . " , payment= 'Carte credit' where id_order = " . $oid;
            mysql_query($sql);

            mail($monitoringEmail, 'Annulation CB ' . $oid, var_export($CMCIC_bruteVars, true));

            break;

        case "paiement":

            $status = 2;

            $amount = $CMCIC_bruteVars['montant'];

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

            $orderinfo = getOrderInfos($oid);

            updQuantity($oid);
            createInvoice($oid);

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

            mail($monitoringEmail, 'monitoring - Valid CB ' . $oid, var_export($CMCIC_bruteVars, true));

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

            break;
    }

    $receipt = CMCIC_CGI2_MACOK;
} else {
    // your code if the HMAC doesn't match
    $receipt = CMCIC_CGI2_MACNOTOK . $cgi2_fields;
}

//-----------------------------------------------------------------------------
// Send receipt to CMCIC server
//-----------------------------------------------------------------------------
printf(CMCIC_CGI2_RECEIPT, $receipt);

// Copyright (c) 2009 Euro-Information ( mailto:centrecom@e-i.com )
// All rights reserved. ---
?>
