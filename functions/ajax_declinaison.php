<?php

include ("../configs/settings.php");

mysql_query("SET NAMES UTF8");

mysql_connect($bdd_host, $bdd_user, $bdd_pwd);
mysql_select_db($bdd_name);

$priceAttribut = 0;
$weightAttribut = 0;
$impact_coef = 1;

foreach ($_POST["ids"] as $combination) {
    if ($combination["value"] > 0) {
        $req = "SELECT * "
                . " FROM av_product_attribute "
                . " WHERE id_product_attribute = " . $combination["value"];

        $query = mysql_query($req);
        $rows = mysql_fetch_array($query);
        $priceAttribut += round($rows["price"] * $config["vat_rate"], 2);
        $weightAttribut += $rows["weight"];
    }
}
foreach ($_POST["subItems"] as $sub_item) {
    if ($sub_item["value"] > 0) {
        $req = "SELECT * "
                . " FROM av_attributes_items "
                . " WHERE id_attributes_items = " . $sub_item["value"];

        $query = mysql_query($req);
        $rows = mysql_fetch_array($query);

        if ($rows["price_impact_percentage"] > 0) {
            $impact_coef = $rows["price_impact_percentage"];
        }
        if ($rows["price_impact_amount"] > 0) {
            $priceAttribut += round($rows["price_impact_amount"] * $config["vat_rate"], 2);
        }
    }
}

$req = "SELECT * "
        . " FROM av_product"
        . " WHERE id_product = " . $_POST["id_product"];


$query = mysql_query($req);
$rows = mysql_fetch_array($query);
$productPrice = round($rows["price"] * $impact_coef * $config["vat_rate"], 2);
$productweight = $rows["weight"];

echo json_encode(array("price" => $priceAttribut + $productPrice, "weight" => $weightAttribut + $productweight));
//echo json_encode(array("price" => $text));
?>
