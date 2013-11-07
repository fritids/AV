<?php
$bdd_host = "localhost";
$bdd_user = "root";
$bdd_pwd = "";
$bdd_name = "allo-vitres";

$bdname = $bdd_name;
$bdserv = $bdd_host;
$bduser = $bdd_user;
$bdpass = $bdd_pwd;


$opts['hn'] = $bdd_host;
$opts['un'] = $bdd_user;
$opts['db'] = $bdd_name;
$opts['pw'] = $bdd_pwd;

@define('_COOKIE_KEY_', 'v91VNEVAhNhXtSMBDq7pZfzYgAWtohfhUonBn9KtdV5AnBnNguduBnFR');


$config["payment"]["cheque_infos"] = "ALLOVITRE <br> adresse 1 <br> cp ville";
$config["payment"]["virement_infos"] = "RIB ###### <br> IBAN ############";

$conf_shipping_amount= 25;

$paypal["email_account"] = "alamichel.s@free.fr";
$paypal["returnurl"] = "http://trusttelecom.fr/allovitres/?action=order_validate";
$paypal["cancelurl"] = "http://trusttelecom.fr/allovitres/";

?>