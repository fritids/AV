<?php
$bdd_host = "localhost";
$bdd_user = "allovitre";
$bdd_pwd = "T1pFLinOEs!";
$bdd_name = "allovitre";

mysql_connect($bdd_host,$bdd_user,$bdd_pwd);
mysql_select_db($bdd_name);


for($i=1;$i<=95;$i++)
{
/*
mysql_query("INSERT INTO `prix_artiste` (`id_visuel` ,`id_client` ,`S` ,`M` ,`L` ,`XL` ,`XXL`) 
VALUES ('".mysql_real_escape_string($id_visuel)."', '".mysql_real_escape_string($id_client)."', '15', '25', '45', '80', '125')");
*/
}

?>
