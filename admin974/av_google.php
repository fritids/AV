<?php

include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

define("TRUCK_OVER_LOAD", 95);


$stmtOrder = $db2->prepare("
                        SELECT distinct d.id_order
                        FROM  av_tournee a, av_truck b, av_order_detail c, av_orders d, av_address e, av_customer f
                        WHERE a.id_truck = b.id_truck
                        AND a.id_order_detail = c.id_order_detail
                        AND c.id_order = d.id_order
                        AND d.id_address_delivery = e.id_address
                        and a.date_livraison = ?
                        and a.id_truck = ?
                        AND d.id_customer = f.id_customer
                        ");

$stmtOrder->execute(array($_POST["date_delivery"], $_POST["id_truck"]));

$r = $stmtOrder->fetchAll(PDO::FETCH_ASSOC);

?>