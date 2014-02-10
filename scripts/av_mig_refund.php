<?php

require_once "../configs/settings.php";
include ("../classes/MysqliDb.php");
include ("../functions/orders.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);


$r = $db->rawQuery("select id_order, payment,date_upd from av_orders where current_state = 7");

foreach ($r as $record)
    refundOrder($record["id_order"], $record["payment"]);
?>