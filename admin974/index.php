<?php
ini_set('session.gc_maxlifetime', 14400);
session_start();
include ("../configs/settings.php");
mysql_connect($bdserv, $bduser, $bdpass);
mysql_select_db($bdname);
include('securite.php');
include ("header.php");
?>

<?php

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'accueil';
}

switch ($page) {
    case "accueil":
        $titre = 'Allovitre';
        $meta_description = 'description meta';
        $fil_ariane = '<a href="/">ACCUEIL</a> >';
        break;
    case "creer_compte":
        $titre = 'Allovitre';
        $meta_description = 'description meta';
        $fil_ariane = '<a href="/">ACCUEIL</a> >';
        break;
}

include( $page . '.php');
include( 'footer.php');
?>