<?php
$bdd_host = "localhost";
$bdd_user = "allovitres";
$bdd_pwd = "wxcvbn";
$bdd_name = "allovitres";

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
$config["payment"]["virement_infos"] = "Banque Societe Générale<br>code banque : 30003<br>Code guichet : 00030<br>N°compte : 00027000532<br>Clé rib : 81<br>";
$config["vat_rate"] = 1.196;

//$conf_shipping_amount= 25;
$conf_shipping_amount= 25;


$paypal["email_account"] = "alamichel.s@free.fr";
define("returnurl", "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/index.php?paiementok");
define("cancelurl", "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/index.php?paiementko");
$paypal["returnipn"] = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "/modules/paypal/validationipn.php";


// free texte : a bigger reference, session context for the return on the merchant website
$sTexteLibre = "Texte Libre";
// customer email
$sEmail = "test@test.zz";

$monitoringEmail = "stephane.alamichel@gmail.com, benoit@trusttelecom.fr";


$confmail["from"] = "contact@allovitres.com";
$confmail["welcome"] = "Bienvenue";
$confmail["commande_new"] = "Nouvelle commande";
$confmail["devis_contact"] = "stephane.alamichel@gmail.com";
$confmail["devis_subject"] = "Nouvelle demande de devis";
        
        
$monitoringEmail = "stephane.alamichel@gmail.com, benoit@trusttelecom.fr";
$monitoringEmails = array("stephane.alamichel@gmail.com", "benoit@trusttelecom.fr");


define ("CMCIC_CLE", "182A5C7FB0AD605886A46193E5E97278D7A5DD9A");
define ("CMCIC_TPE", "0354284");
define ("CMCIC_VERSION", "3.0");
//define ("CMCIC_SERVEUR", "https://ssl.paiement.cic-banques.fr/test/");
define ("CMCIC_SERVEUR", "https://paiement.creditmutuel.fr/");
define ("CMCIC_CODESOCIETE", "allovitres");
define ("CMCIC_URLOK", "http://".$_SERVER["SERVER_NAME"].  dirname($_SERVER["REQUEST_URI"])."/index.php?paiementok");
define ("CMCIC_URLKO", "http://".$_SERVER["SERVER_NAME"].  dirname($_SERVER["REQUEST_URI"])."/index.php?paiementko");


?>
