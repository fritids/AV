<?php
//connexion à la base de données 
define('DB_NAME', 'allovitre');
define('DB_USER', 'allovitre');
define('DB_PASSWORD', 'T1pFLinOEs!');
define('DB_HOST', 'localhost');
$link   =   mysql_connect( DB_HOST , DB_USER , DB_PASSWORD );
mysql_select_db( DB_NAME , $link );
 
// changement de l'ordre des photos dans la base de données, photo par photo
foreach( $_POST['cmd'] as $order => $id_cmd )
{
    mysql_query( "UPDATE av_tournee SET `position` = $order WHERE id_order = $id_cmd", $link ) or die( mysql_error() );
}
 
/*****
fonctions
*****/
function safe($var)
{
	$var = mysql_real_escape_string($var);
	$var = addcslashes($var, '%_');
	$var = trim($var);
	$var = htmlspecialchars($var);
	return $var;
}
?>