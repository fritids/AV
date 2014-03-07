<?php

include ("../configs/settings.php");

mysql_query("SET NAMES UTF8");

mysql_connect($bdd_host, $bdd_user, $bdd_pwd);
mysql_select_db($bdd_name);

$priceAttribut = 0;
$priceOption = 0;
$priceAnswer = 0;
$weightAttribut = 0;
$impact_coef = 1;

if (isset($_POST["ids"])) {
    foreach ($_POST["ids"] as $combination) {
        if ($combination["value"] > 0) {
            $req = "SELECT * "
                    . " FROM av_product_attribute "
                    . " WHERE id_product_attribute = " . $combination["value"];

            $query = mysql_query($req);
            $rows = mysql_fetch_array($query);

            if ($_POST["posable"])
                $rows["price"] = $rows["price_pose"];

            $priceAttribut += $rows["price"] * $config["vat_rate"];
            $weightAttribut += $rows["weight"];
        }
    }
}

if (isset($_POST["main_item_ids"])) {
    foreach ($_POST["main_item_ids"] as $sub_item) {
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
                $priceOption += $rows["price_impact_amount"] * $config["vat_rate"];
            }
        }
    }
}
if (isset($_POST["questions"]) && $_POST["posable"] == 1) {
    foreach ($_POST["questions"] as $question) {
        if ($question["value"] > 0) {
            $req = "SELECT * "
                    . " FROM av_pose_form "
                    . " WHERE id_pose_form = " . $question["value"];

            $query = mysql_query($req);
            $rows = mysql_fetch_array($query);

            if ($rows["price"] > 0) {
                $priceAnswer += $rows["price"] * $config["vat_rate"];
            }
        }
    }
}

$req = "SELECT * "
        . " FROM av_product"
        . " WHERE id_product = " . $_POST["id_product"];


$query = mysql_query($req);
$rows = mysql_fetch_array($query);

if ($_POST["posable"])
    $rows["price"] = $rows["price_pose"];


$productPrice = $rows["price"] * $impact_coef * $config["vat_rate"];
$productweight = $rows["weight"];

echo json_encode(
        array(
            "price" => $priceAttribut * $impact_coef + $productPrice,
            "price_option" => $priceOption,
            "price_answer" => $priceAnswer,
            "weight" => $weightAttribut + $productweight,
            "post" => $_POST,
            "impact_coef" => $impact_coef,
            "priceAttribut" => $priceAttribut
        )
);
//echo json_encode(array("price" => $text));
?>
