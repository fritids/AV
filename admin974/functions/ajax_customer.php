<?php

include ("../../configs/settings.php");
include ("../../classes/MysqliDb.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);
mysql_query("SET NAMES UTF8");

mysql_connect($bdd_host, $bdd_user, $bdd_pwd);
mysql_select_db($bdd_name);

$req = "SELECT id_customer, firstname, lastname "
        . " FROM av_customer "
        . " WHERE (upper(firstname) LIKE upper('%" . $_REQUEST['term'] . "%') or upper(lastname) LIKE upper('%" . $_REQUEST['term'] . "%') )";


$query = mysql_query($req);

while ($row = mysql_fetch_array($query)) {
    $results[] = array(
        'label' => utf8_encode($row['firstname']) . " " . utf8_encode($row['lastname']),
        'id_customer' => $row['id_customer']
    );
}

echo json_encode($results);
?>
