<?php

include ("../configs/settings.php");

mysql_query("SET NAMES UTF8");

mysql_connect($bdd_host, $bdd_user, $bdd_pwd);
mysql_select_db($bdd_name);


$req = "SELECT * "
        . " FROM av_product_attribute "
        . " WHERE id_product_attribute = " . $_POST["id"];


$query = mysql_query($req);

$rows = mysql_fetch_array($query);

$priceAttribut= $rows["price"];

$req = "SELECT * "
        . " FROM av_product"
        . " WHERE id_product = " . $rows["id_product"];


$query = mysql_query($req);

$rows = mysql_fetch_array($query);

$productPrice = $rows["price"];

echo json_encode(array("price" => $priceAttribut+$productPrice));
?>
