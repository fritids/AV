<?php

include ("../../configs/settings.php");
include ("../../classes/MysqliDb.php");

mysql_connect($bdd_host, $bdd_user, $bdd_pwd);
mysql_select_db($bdd_name);
mysql_query("SET NAMES UTF8");

$req = "SELECT id_customer, firstname, lastname "
        . " FROM av_customer "
        . " WHERE (concat(upper(firstname), ' ' , upper(lastname),' ' , upper(email))) LIKE upper('%" . $_REQUEST['term'] . "%')";


$query = mysql_query($req);

while ($row = mysql_fetch_array($query)) {
    $results[] = array(
        'label' => utf8_encode($row['firstname']) . " " . utf8_encode($row['lastname']),
        'id_customer' => $row['id_customer']
    );
}

echo json_encode($results);
?>
