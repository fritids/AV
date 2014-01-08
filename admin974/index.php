<?php

if (isset($_POST["search_id_order"])) {
    header('location: av_orders_view.php?id_order=' . $_POST["search_item"]);
}
if (isset($_POST["search_id_customer"])) {
    header('location: av_customer_view.php?id_customer=' . $_POST["search_item"]);
}
if (isset($_POST["search_customer_lastname"])) {
    header('location: av_customer.php?PME_sys_qf2=' . $_POST["search_item"]);
}
if (isset($_POST["search_id_devis"])) {
    header('location: av_devis_view.php?id_devis=' . $_POST["search_item"]);
}

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
print_r($_POST);
?>