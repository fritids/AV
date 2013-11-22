<?php

include ("../configs/settings.php");
include ("header.php");
require('../libs/Smarty.class.php');
require('../classes/class.phpmailer.php');


$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);


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
    $mail->SetFrom($confmail["from"]);

    $mail->AddAddress($contact["email"]);

    foreach ($monitoringEmails as $bccer) {
        $mail->AddBCC($bccer);
    }

    $mail->Subject = "Allovitres - Envoi de votre commande #" . $contact["id_order"];
    $smarty->assign("tournee_livraison", $contact["horaire"]);
    $mail_body = $smarty->fetch('notif_order_delivery.tpl');

    $mail->MsgHTML($mail_body);

    if ($mail->Send()) {

        echo "envoi mail order#" . $contact["id_order"] . " " . $contact["firstname"] . " " . $contact["lastname"] . " " . $contact["email"] . "<br>";

        $r = $db->where("id_order", $contact["id_order"])
                ->update("av_tournee", array("mail_send" => 1));

        $param = array(
            "id_order" => $contact["id_order"],
            "id_user" => $_SESSION["user_id"],
            "category" => "mail_livraison",
        );

        /* on change le status des ligne de detail en livraison fixé*/
        $orderDetails = $db->rawQuery("select id_order_detail 
            from av_tournee 
            where id_truck = ?
            and date_livraison = ? 
            and id_order = ?
            and status = 2", array($contact["id_truck"], $contact["date_livraison"], $contact["id_order"]));

        foreach ($orderDetails as $orderDetail) {
            $r = $db->where("id_order_detail", $orderDetail["id_order_detail"])
                    ->update("av_order_detail", array("product_current_state" => 19, "date_upd" => date("Y-m-d H:i:s")));
        }


        $r = $db->insert("av_order_bdc", $param);
    }
}
?>
