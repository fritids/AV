<?php

include ("../configs/settings.php");
include ("header.php");
require('../libs/Smarty.class.php');
require('../classes/class.phpmailer.php');
require('../classes/sms.inc.php');
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/tools.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

define("EN_COURS_LIVRAISON", 4);
define("LIVRAISON_PROGRAMMEE", 19);

//SMS

$user_login = 'pei73hyl8trvtivx8rduvg2p@sms-accounts.com';
$api_key = 'PLYvbMEbIhW5zfnQy0Xi';
$sms_type = QUALITE_PRO; // ou encore QUALITE_PRO
$sms_mode = INSTANTANE; // ou encore DIFFERE
$sms_sender = 'ALLOVITRES';

$smarty = new Smarty;
$smarty->caching = 0;
//$smarty->error_reporting = E_ALL & ~E_NOTICE;
$smarty->setTemplateDir(array('../templates', '../templates/mails/', '../templates/mails/admin', '../templates/pdf', '../templates/pdf/admin'));

$date_livraison = $_POST["delivery_date"];
$id_truck = $_POST["id_truck"];

$r = $db->rawQuery("select distinct a.id_order, a.id_truck, a.date_livraison, horaire, comment1, comment2, comment3, firstname, lastname, email
from av_tournee a, av_order_detail b, av_orders c, av_customer d
where a.id_order_detail = b.id_order_detail
and a.id_order = c.id_order
and c.id_customer = d.id_customer
and a.mail_send = 0
and date(a.date_livraison) = ?
and a.id_truck = ?
", array($date_livraison, $id_truck));

//Create a new PHPMailer instance
$mail = new PHPMailer();
//Set who the message is to be sent from
$mail->CharSet = 'UTF-8';

foreach ($r as $k => $contact) {
    $mail->ClearAllRecipients();
    $mail->SetFrom("livraison@allovitres.com");

    $mail->AddAddress($contact["email"]);

    foreach ($monitoringEmails as $bccer) {
        $mail->AddBCC($bccer);
    }

    // liste des produits à livrer
    $orderDetails = $db->rawQuery("select b.*
            from av_tournee a, av_order_detail b
            where a.id_order_detail = b.id_order_detail
            and a.id_truck = ?
            and a.date_livraison = ? 
            and a.id_order = ? ", array($contact["id_truck"], $contact["date_livraison"], $contact["id_order"]));

    $mail->Subject = "Allovitres - Envoi de votre commande #" . $contact["id_order"];
    $smarty->assign("tournee_livraison", $contact["horaire"]);
    $smarty->assign("orderdetails", $orderDetails);
    
    $mail_body = $smarty->fetch('notif_order_delivery.tpl');

    $mail->MsgHTML($mail_body);

    if ($mail->Send()) {

        $orderinfo = getOrderInfos($contact["id_order"]);

        echo "envoi mail order#" . $contact["id_order"] . " " . $contact["firstname"] . " " . $contact["lastname"] . " " . $contact["email"] . "<br>";

        $r = $db->where("id_order", $contact["id_order"])
                ->update("av_tournee", array("mail_send" => 1));


        /* on change le statut global a préparation en cours */
        $s = $db->where("id_order", $contact["id_order"])
                ->update("av_orders", array("current_state" => EN_COURS_LIVRAISON, "date_upd" => date("Y-m-d H:i:s")));

        addLog(array("tabs" => "mv_orders",
            "rowkey" => $orderinfo["id_order"],
            "col" => "current_state",
            "operation" => "update",
            "oldval" => $orderinfo["current_state"],
            "newval" => EN_COURS_LIVRAISON
        ));

        /* on change le status des ligne de detail en livraison fixé */

        foreach ($orderDetails as $orderDetail) {
            $r = $db->where("id_order_detail", $orderDetail["id_order_detail"])
                    ->update("av_order_detail", array("product_current_state" => LIVRAISON_PROGRAMMEE, "date_upd" => date("Y-m-d H:i:s")));

            addLog(array("tabs" => "mv_orders",
                "rowkey" => $orderDetail["id_order_detail"],
                "col" => "product_current_state",
                "operation" => "update",
                "oldval" => $orderDetail["product_current_state"],
                "newval" => LIVRAISON_PROGRAMMEE
            ));
        }

        $param = array(
            "id_order" => $contact["id_order"],
            "id_user" => $_SESSION["user_id"],
            "category" => "mail_livraison",
        );
        $r = $db->insert("av_order_bdc", $param);

        //sms
        if ($orderinfo["alert_sms"] == 1) {
            $order_sms_text = "Bonjour, Votre livraison est prévue pour le " . $contact["horaire"] . " . L'équipe Allovitres.";
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
                "id_order" => $contact["id_order"],
                "id_user" => $_SESSION["user_id"],
                "category" => "sms_mail_livraison",
            );
            $r = $db->insert("av_order_bdc", $param);
        }
    }
}
?>
