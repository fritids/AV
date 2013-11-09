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

$conf_shipping_amount = 25;

$paypal["email_account"] = "alamichel.s@free.fr";
define("returnurl", "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/index.php?paiementok");
define("cancelurl", "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/index.php?paiementko");


// free texte : a bigger reference, session context for the return on the merchant website
$sTexteLibre = "Texte Libre";
// customer email
$sEmail = "test@test.zz";


define("CMCIC_CLE", "182A5C7FB0AD605886A46193E5E97278D7A5DD9A");
define("CMCIC_TPE", "0354284");
define("CMCIC_VERSION", "3.0");
define("CMCIC_SERVEUR", "https://paiement.creditmutuel.fr/");
define("CMCIC_CODESOCIETE", "allovitres");
define("CMCIC_URLOK", "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/index.php?paiementok");
define("CMCIC_URLKO", "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/index.php?paiementko");
?>