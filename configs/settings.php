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
$config["vat_rate"] = 1.196;

$conf_shipping_amount = 25;

$paypal["email_account"] = "contact@moka-web.net";

$paypal["returnurl"] = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/index.php?order-confirmation";
$paypal["cancelurl"] = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/index.php?order-error";
$paypal["returnipn"] = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/modules/paypal/validationipn.php";



// free texte : a bigger reference, session context for the return on the merchant website
$sTexteLibre = "";


$confmail["from"] = "contact@allovitres.com";
$confmail["welcome"] = "Bienvenue";
$confmail["commande_new"] = "Nouvelle commande";
$confmail["devis_contact"] = "stephane.alamichel@gmail.com";
$confmail["devis_subject"] = "Nouvelle demande de devis";
        
        
$monitoringEmail = "stephane.alamichel@gmail.com";
$monitoringEmails = array("stephane.alamichel@gmail.com");

define("CMCIC_CLE", "182A5C7FB0AD605886A46193E5E97278D7A5DD9A");
define("CMCIC_TPE", "0354284");
define("CMCIC_VERSION", "3.0");
define("CMCIC_SERVEUR", "https://paiement.creditmutuel.fr/");
define("CMCIC_CODESOCIETE", "allovitres");
define("CMCIC_URLOK", "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/index.php?paiementok");
define("CMCIC_URLKO", "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/index.php?paiementko");
?>