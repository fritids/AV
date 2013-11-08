<?php

include ("../configs/settings.php");

mysql_query("SET NAMES UTF8");

mysql_connect($bdd_host, $bdd_user, $bdd_pwd);
mysql_select_db($bdd_name);

/*
  //foreach (explode("&", $_POST["ids"]) as $val) {
  foreach (explode("&", $_POST["ids"]) as $val) {

  $opt[] = str_replace("options=", "", $val);
  }


 */

$priceAttribut = 0;
$weightAttribut = 0;

foreach ($_POST["ids"] as $combination) {
    if ($combination["value"] > 0) {
        $req = "SELECT * "
                . " FROM av_product_attribute "
                . " WHERE id_product_attribute = " . $combination["value"];

        $query = mysql_query($req);
        $rows = mysql_fetch_array($query);
        $priceAttribut += $rows["price"];
        $weightAttribut += $rows["weight"];
    }
}


$req = "SELECT * "
        . " FROM av_product"
        . " WHERE id_product = " . $_POST["id_product"];


$query = mysql_query($req);
$rows = mysql_fetch_array($query);
$productPrice = $rows["price"];
$productweight = $rows["weight"];

echo json_encode(array("price" => $priceAttribut + $productPrice, "weight" => $weightAttribut + $productweight));
//echo json_encode(array("price" => $text));
?>
