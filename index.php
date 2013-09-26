<?php

include ('configs/settings.php');
require('libs/Smarty.class.php');
require('classes/MysqliDb.php');



$sub_menu = array(
    array("title" => "Simple Vitrage", "url" => "?cat=1"),
    array("title" => "Double Vitrage", "url" => "?cat=2"),
    array("title" => "Verre Spécifiques", "url" => "?cat=3"),
    array("title" => "Accessoires", "url" => "?cat=4")
);

$smarty = new Smarty;

//connexion base de données
$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);




$product = $db->where('id_product', 1)
        ->get('av_product');

$products = $db->where('id_category', 1)
        ->get('av_product');

$product_caract = $db->where('id_product', 1)
        ->get('av_product_caract');



/* switch ($page) {
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
  } */

$page = "home";

if (isset($_GET["cat"]))
    $page = "category";
if (isset($_GET["p"]))
    $page = "product";


$smarty->assign('sub_menu', $sub_menu);

$smarty->assign('page', $page);

$smarty->assign('product', $product[0]);
$smarty->assign('products', $products);
$smarty->assign('product_caract', $product_caract);

$smarty->display('index.tpl');
?>