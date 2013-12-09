<?php

include ("../configs/settings.php");
include ("../classes/MysqliDb.php");
include ("../functions/products.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$product = getProductInfos($_POST["id_product"]);
$id_item= $_POST["id_item"];
$id_sub_item= $_POST["id_sub_item"];

echo json_encode($product["specific_combinations"][$id_item]["items"][$id_sub_item]);
?>
