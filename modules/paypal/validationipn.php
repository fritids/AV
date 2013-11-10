<?php

// tell PHP to log errors to ipn_errors.log in this directory
ini_set('log_errors', true);
ini_set('error_log', dirname(__FILE__) . '/ipn_errors.log');

// intantiate the IPN listener
include('./ipnlistener.php');
require('../../configs/settings.php');
require('../../classes/MysqliDb.php');
require('../../functions/orders.php');

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
            "order_reference" =>  str_pad($oid, 9, '0', STR_PAD_LEFT),
            "id_currency" => 1,
            "amount" => $amount,
            "conversion_rate" => 1,
            "payment_method" => "Paypal",
            "date_add" => date("Y-m-d H:i:s"),
        );

        $db->insert("av_order_payment", $order_payment);

        mail($monitoringEmail, 'Valid IPN ' . $txn_id . " " . $_POST['invoice'], $listener->getTextReport());
    }
} else {
    // manually investigate the invalid IPN
    mail($monitoringEmail, 'Invalid IPN', $listener->getTextReport());
}
?>