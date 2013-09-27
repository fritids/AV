<?php

include ('configs/settings.php');
require('libs/Smarty.class.php');
require('classes/MysqliDb.php');
require('classes/panier.php');
require('functions/users.php');
require('functions/products.php');



$sub_menu = array(
    array("title" => "Simple Vitrage", "url" => "?c&id=1"),
    array("title" => "Double Vitrage", "url" => "?c&id=2"),
    array("title" => "Verre Spécifiques", "url" => "?c&id=3"),
    array("title" => "Accessoires", "url" => "?c&id=4")
);

// classes declaration

$smarty = new Smarty;
//$smarty->error_reporting = E_ALL & ~E_NOTICE;
//connexion base de données
$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

//Caddie
$cart = new Panier();

$product_caract = $db->where('id_product', 1)
        ->get('av_product_caract');

/* dispatcher */
$page = "home";

if (isset($_GET["c"])) {
    $page = "category";
    $products = $db->where('id_category', $_GET["id"])
            ->get('av_product');
    $smarty->assign('products', $products);
}

if (isset($_GET["p"])) {
    $page = "product";
    $product = getProductInfos($_GET["id"]);
    $smarty->assign('product', $product);
}

if (isset($_GET["n"]))
    $page = "form_user_account";

if (isset($_GET["cart"]))
    $page = "cart";

if (isset($_GET["my-account"]))
    $page = "my-account";

/* action Caddie */

if (isset($_GET["cart"])) {
    if (isset($_POST["id_product"]) and $_POST["id_product"] != "") {

        $pid = $_POST["id_product"];
        $pqte = @$_POST["qte"];
        
        $productInfos = getProductInfos($_POST["id_product"]);

        if (isset($_POST["add"]))
            $cart->addItem($pid, 1, $productInfos["price"], $productInfos["name"]);
        if (isset($_POST["del"]))
            $cart->removeItem($pid, $pqte);
    }
}

$items = $cart->showCart();
$cart_nb_items = count($items);


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


/* new user */
if (isset($_GET["action"]) && $_GET["action"] == "new_user") {

    $userinfos = array(
        "firstname" => $_POST["firstname"],
        "lastname" => $_POST["lastname"],
        "email" => $_POST["email"],
        "passwd" => md5($_POST["passwd"]),
        "phone" => $_POST["phone"],
        "phone_mobile" => $_POST["phone_mobile"],
        "active" => 1,
        "date_add" => date("Y-m-d"),
        "date_upd" => date("Y-m-d"));

    if (isset($_SESSION["user"]["id_customer"]) && $_SESSION["user"]["id_customer"] != "") {

        $uid = $_SESSION["user"]["id_customer"];
        $userinfos = array(
            "firstname" => $_POST["firstname"],
            "lastname" => $_POST["lastname"],
            "email" => $_POST["email"],
            "passwd" => md5($_POST["passwd"]),
            "phone" => $_POST["phone"],
            "phone_mobile" => $_POST["phone_mobile"]);

        $invoice_adresse = array(
            "address1" => @$_POST["invoice_address1"],
            "postcode" => @$_POST["invoice_postcode"],
            "country" => @$_POST["invoice_country"]);

        $delivery_adresse = array(
            "address1" => @$_POST["delivery_address1"],
            "postcode" => @$_POST["delivery_postcode"],
            "country" => @$_POST["delivery_country"]);


        updateUserAddress($invoice_adresse, "invoice", $_SESSION["user"]["invoice"]["id_address"]);
        updateUserAddress($delivery_adresse, "delivery", $_SESSION["user"]["delivery"]["id_address"]);

        updateUserAccount($userinfos, $_SESSION["user"]["id_customer"]);
    } else {
        $uid = createNewAccount($userinfos);

        $invoice_adresse = array(
            "alias" => 'invoice',
            "id_customer" => $uid,
            "address1" => @$_POST["invoice_address1"],
            "postcode" => @$_POST["invoice_postcode"],
            "country" => @$_POST["invoice_country"],
            "active" => 1,
            "date_add" => date("Y-m-d"),
            "date_upd" => date("Y-m-d"));

        $delivery_adresse = array(
            "alias" => 'delivery',
            "id_customer" => $uid,
            "address1" => @$_POST["delivery_address1"],
            "postcode" => @$_POST["delivery_postcode"],
            "country" => @$_POST["delivery_country"],
            "active" => 1,
            "date_add" => date("Y-m-d"),
            "date_upd" => date("Y-m-d"));


        createNewAdresse($invoice_adresse);
        createNewAdresse($delivery_adresse);
        
        //auto login
        checkUserLogin($_POST["email"], $_POST["passwd"]);
    }
}


/* Login */
if (isset($_GET["action"]) && $_GET["action"] == "login") {
    $res = checkUserLogin($_POST["email"], $_POST["passwd"]);

    /* if ($res) {
      echo "ok";
      } else {
      echo "ko";
      }
     */
}

/* logout */
if (isset($_GET["action"]) && $_GET["action"] == "logout") {
    session_destroy();
    unset($_SESSION);
}


/* session */
$smarty->assign('is_logged', false);
$smarty->assign('user', null);

if (@$_SESSION["is_logged"]) {
    $smarty->assign('is_logged', true);
    $smarty->assign('user', $_SESSION["user"]);
}



/* Smarty */



$smarty->assign('sub_menu', $sub_menu);
$smarty->assign('page', $page);

$smarty->assign('product_caract', $product_caract);

$smarty->assign('cart', $items);
$smarty->assign('cart_nb_items', $cart_nb_items);


$smarty->display('index.tpl');

print_r($_SESSION);
?>
