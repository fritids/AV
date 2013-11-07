<?php

include ("../../configs/settings.php");
include ("../../classes/MysqliDb.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);


mysql_connect($bdd_host, $bdd_user, $bdd_pwd);
mysql_select_db($bdd_name);

$req = "SELECT name, id_product, weight, price, min_width , min_height ,max_surface ,max_width ,max_height "
        . "FROM av_product "
        . "WHERE name LIKE '%" . $_REQUEST['term'] . "%' ";

$query = mysql_query($req);

while ($row = mysql_fetch_array($query)) {
    $results[] = array(
        'label' => utf8_encode($row['name']),
        'id_product' => $row['id_product'],
        'price' => $row['price'],
        'weight' => $row['weight'],
        'min_width' => $row['min_width'],
        'min_height' => $row['min_height'],
        'max_surface' => $row['max_surface'],
        'max_width' => $row['max_width'],
        'max_height' => $row['max_height'],
    );
}

echo json_encode($results);
?>
