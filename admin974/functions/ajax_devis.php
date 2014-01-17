<?php

include ("../../configs/settings.php");
include ("../../classes/MysqliDb.php");
include ("../../functions/products.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);


mysql_connect($bdd_host, $bdd_user, $bdd_pwd);
mysql_select_db($bdd_name);

$req = "SELECT name, id_product, weight, price, min_width , min_height , max_width ,max_height, min_area_invoiced, max_area_invoiced "
        . "FROM av_product "
        . "WHERE id_product = " . $_POST['id_product'];

$query = mysql_query($req);

while ($row = mysql_fetch_array($query)) {
    
    $p = getProductInfos($row['id_product']);
    
    $results = array(
        'label' => utf8_encode($row['name']),
        'id_product' => $row['id_product'],
        'price' => $row['price'] * $config["vat_rate"],
        'weight' => $row['weight'],
        'min_width' => $row['min_width'],
        'min_height' => $row['min_height'],
        'max_width' => $row['max_width'],
        'max_height' => $row['max_height'],
        'min_area_invoiced' => $row['min_area_invoiced'],
        'max_area_invoiced' => $row['max_area_invoiced'],
        'info' => $p,
    );
}

echo json_encode($results);
?>
