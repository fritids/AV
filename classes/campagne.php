<?php
// envoi instantanté de SMS

require('sms.inc.php');

$user_login = 'pei73hyl8trvtivx8rduvg2p@sms-accounts.com';
$api_key = 'PLYvbMEbIhW5zfnQy0Xi';

$sms_recipients = array('+33634202920');
$sms_text = 'Bonjour, nous vous remercions pour votre commande, votre facture vous etes été transmise par mail. L\'équipe Allovitres.';
$sms_type = QUALITE_PRO; // ou encore QUALITE_PRO
$sms_mode = INSTANTANE; // ou encore DIFFERE
$sms_sender = 'ALLOVITRES';

$sms = new SMS();

$sms->set_user_login($user_login);
$sms->set_api_key($api_key);
$sms->set_sms_mode($sms_mode);
$sms->set_sms_text($sms_text);
$sms->set_sms_recipients($sms_recipients);
$sms->set_sms_type($sms_type);
$sms->set_sms_sender($sms_sender);
// $sms->set_date(2013, 4, 25, 15, 12); // En cas d'envoi diffŽrŽ.

$xml = $sms->send();
echo '<textarea style="width:200px;height:200px;">' . $xml . '</textarea>';
?>