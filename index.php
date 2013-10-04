<?php
include ('configs/settings.php');
require('libs/Smarty.class.php');
require('classes/MysqliDb.php');
require('classes/panier.php');
require('classes/paypal.php');
require('functions/users.php');
require('functions/products.php');
require('functions/categories.php');
require('functions/orders.php');
require('functions/tools.php');


// classes declaration

$smarty = new Smarty;
//$smarty->caching = 0;
//$smarty->error_reporting = E_ALL & ~E_NOTICE;
//connexion base de données
$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$nb_produits = 0;

$sub_menu = getCategories();


//Caddie
$cart = new Panier();

/* action Caddie */
if (isset($_GET["cart"])) {
    if (isset($_POST["id_product"]) and $_POST["id_product"] != "" && $_POST["quantity"] != "") {

        $pid = $_POST["id_product"];
        $pqte = $_POST["quantity"];

        $productInfos = getProductInfos($_POST["id_product"]);

        $pweight = $productInfos["weight"];
        $shipping_ratio = getDeliveryRatio($pweight);
        $shipping_amount = $shipping_ratio * $pweight;

        if (isset($_POST["add"])) {
            $cart->addItem($pid, $pqte, $productInfos["price"], $productInfos["name"], $shipping_amount);

            //Si option
            if (isset($_POST["options"])) {
                $id_option = $_POST["options"];
                $option_price = $productInfos["attributes"][$id_option]["price"];
                $option_name = $productInfos["attributes"][$id_option]["name"];
                $option_weight = $productInfos["attributes"][$id_option]["weight"];
                $shipping_ratio = getDeliveryRatio($option_weight);
                $shipping_amount = $shipping_ratio * $option_weight;

                $cart->addItemOption($pid, $id_option, $pqte, $option_price, $option_name, $shipping_amount);
            }
        }

        if (isset($_POST["del"]))
            $cart->removeItem($pid, $pqte, $productInfos["price"], $shipping_amount);

        // on empecher de faire un F5
        header("Location: index.php?cart");
    }
}

$cartItems = $cart->showCart();
$cart_nb_items = count($cartItems);



/* dispatcher */
$page = "home";

if (isset($_GET["c"])) {
    $page = "category";
    
    $categorie = getCategorieInfo($_GET["id"]);
    
    $products = getProductByCategorie($_GET["id"]);
    $nb_produits= count($products);
    
    $smarty->assign('products', $products);
    $smarty->assign('categorie', $categorie);
}

if (isset($_GET["p"])) {
    $page = "product";
    $product = getProductInfos($_GET["id"]);
    $smarty->assign('product', $product);
}
if (isset($_GET["register"]))
    $page = "register";

if (isset($_GET["cart"]))
    $page = "cart";

if (isset($_GET["my-account"]))
    $page = "my-account";

if (isset($_GET["identification"]))
    $page = "identification";

if (isset($_GET["orders-list"])) {
    $page = "orders-list";
    $orders = getUserOrders($_SESSION["user"]["id_customer"]);
    $smarty->assign('orders', $orders);
}


$smarty->assign('PAYPAL_CHECKOUT_FORM', '');

if (isset($_GET["order-resume"])) {
    $page = "order-resume";
    
    $settings = array(
            'business' => $paypal["email_account"] , //paypal email address
            'currency' => 'EUR', //paypal currency
            'cursymbol' => '&euro;', //currency symbol
            'location' => 'FR', //location code  (ex GB)
            'returnurl' => $paypal["returnurl"], //where to go back when the transaction is done.
            'returntxt' => 'Retour au site', //What is written on the return button in paypal
            'cancelurl' => $paypal["cancelurl"], //Where to go if the user cancels.
            'shipping' => 0, //Shipping Cost
            'custom' => ''                           //Custom attribute
        );
    
    $pp = new paypalcheckout($settings); //Create an instance of the class
    $pp->addMultipleItems($cartItems); //Add all the items to the cart in one go
    //$cartHTML = $pp->getCartContentAsHtml();
    $PaypalCheckoutForm = $pp->getCheckoutForm();

    $CheckoutFormTest = "<form action='?action=order_validate' method='post'>
        <input type='submit' value='Panier validé' />
        </form>";

    $smarty->assign('PAYPAL_CHECKOUT_FORM', $PaypalCheckoutForm);
    $smarty->assign('PAYPAL_CHECKOUT_FORM_TEST', $CheckoutFormTest);
}


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
            "id_customer" => $uid,
            "address1" => @$_POST["invoice_address1"],
            "postcode" => @$_POST["invoice_postcode"],
            "country" => "France",
            "city" => @$_POST["invoice_city"]
        );

        $delivery_adresse = array(
            "id_customer" => $uid,
            "address1" => @$_POST["delivery_address1"],
            "postcode" => @$_POST["delivery_postcode"],
            "country" => "France",
            "city" => @$_POST["delivery_city"]
        );

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
            "city" => @$_POST["invoice_city"],
            "country" => 'France',
            "active" => 1,
            "date_add" => date("Y-m-d"),
            "date_upd" => date("Y-m-d"));

        $delivery_adresse = array(
            "alias" => 'delivery',
            "id_customer" => $uid,
            "address1" => @$_POST["delivery_address1"],
            "postcode" => @$_POST["delivery_postcode"],
            "city" => @$_POST["delivery_city"],
            "country" => 'France',
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


/* Order */
if (isset($_GET["action"]) && $_GET["action"] == "order_validate") {
    $order_summary = array(
        "id_customer" => $_SESSION["user"]["id_customer"],
        "reference" => RandomString(),
        "id_address_delivery" => $_SESSION["user"]["delivery"]["id_address"],
        "id_address_invoice" => $_SESSION["user"]["invoice"]["id_address"],
        "current_state" => 1,
        "total_paid" => $_SESSION["cart_summary"]["total_amount"],
        "invoice_date" => date("Y-m-d h:i:s"),
        "delivery_date" => date("Y-m-d h:i:s"),
        "date_add" => date("Y-m-d h:i:s"),
        "date_upd" => date("Y-m-d h:i:s"),
    );

    $oid = $db->insert("av_orders", $order_summary);

    foreach ($cartItems as $item) {

        $order_detail = array(
            "id_order" => $oid,
            "product_id" => $item["id"],
            "product_name" => $item["name"],
            "product_quantity" => $item["quantity"],
            "product_price" => $item["price"],
            "product_shipping" => $item["shipping"],
            "total_price_tax_incl" => $item["quantity"] * $item["price"] + $item["shipping"],
            "total_price_tax_excl" => $item["quantity"] * $item["price"] + $item["shipping"]
        );

        // les options
        if (isset($item["options"])) {
            foreach ($item["options"] as $k => $option) {
                $order_detail["product_attribute_id"] = $option["o_id"];
                $order_detail["attribute_name"] = $option["o_name"];
                $order_detail["attribute_quantity"] = $option["o_quantity"];
                $order_detail["attribute_price"] = $option["o_price"];
                $order_detail["attribute_shipping"] = $option["o_shipping"];
                $order_detail["total_price_tax_incl"] += $option["o_quantity"] * $option["o_price"] + $option["o_shipping"];
                $order_detail["total_price_tax_excl"] += $option["o_quantity"] * $option["o_price"] + $option["o_shipping"];

                $db->insert("av_order_detail", $order_detail);
            }
        } else { // pas d'option
            $db->insert("av_order_detail", $order_detail);
        }
    }

    //on flush le caddie
    unset($_SESSION["cart"]);
    unset($_SESSION["cart_summary"]);
    $cartItems = array();

//on redirige sur la listes des commandes
    $page = "orders-list";
    $orders = getUserOrders($_SESSION["user"]["id_customer"]);
    $smarty->assign('orders', $orders);
}



/* session */
$smarty->assign('user', null);
if (@$_SESSION["is_logged"]) {
    $smarty->assign('user', $_SESSION["user"]);
}



/* Smarty */
$smarty->assign('sub_menu', $sub_menu);
$smarty->assign('page', $page);
$smarty->assign('cart', $cartItems);
$smarty->assign('cart_nb_items', $cart_nb_items);
$smarty->assign('nb_produits', $nb_produits);

$smarty->display('index.tpl');
?>

<h1>Session</h1>
<?= print_r($_SESSION) ?>
<h1>Cart item</h1>
<?= print_r($cartItems); ?>
<h1>Orders</h1>
<?= print_r($orders); ?>
<h1>Product</h1>
<?= print_r($product); ?>
<h1>Post</h1>
<?= print_r($_POST); ?>