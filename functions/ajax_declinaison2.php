<?php

include ("../configs/settings.php");

mysql_query("SET NAMES UTF8");

mysql_connect($bdd_host, $bdd_user, $bdd_pwd);
mysql_select_db($bdd_name);

$priceAttribut = 0;
$weightAttribut = 0;

$arr = explode("|", $_POST["value"]);

//print_r($arr);

$req = "SELECT * "
        . " FROM av_product_attribute "
        . " WHERE id_product_attribute = " . $arr[1];

$query = mysql_query($req);
$rows = mysql_fetch_array($query);
$priceAttribut = round($rows["price"] * $config["vat_rate"], 2);
$weightAttribut = $rows["weight"];


$req = "SELECT * "
        . " FROM av_product"
        . " WHERE id_product = " . $arr[0];


$query = mysql_query($req);
$rows = mysql_fetch_array($query);

$productPrice = round($rows["price"] * $config["vat_rate"], 2);
$productweight = $rows["weight"];

echo json_encode(array("priceAttribut" => $priceAttribut,
    "productPrice" => $productPrice
));
?>
