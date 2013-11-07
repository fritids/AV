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
require('functions/cms.php');


// classes declaration

$smarty = new Smarty;
//$smarty->caching = 0;
$smarty->error_reporting = E_ALL & ~E_NOTICE;
//connexion base de données
$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

/* vars */
$nb_produits = 0;
$page_type = "";
$mydevis = array();
$error = array();
$breadcrumb = array("parent" => NULL, "fils" => null);
$sub_menu = getCategories();


/* Cms */
$AllCMS = getAllCmsInfo();
$cms = array();
/* fin */


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
        //$shipping_amount = $shipping_ratio * $pweight;
        $shipping_amount = $conf_shipping_amount;


        if (isset($_POST["add"])) {
            $surface = ($_POST["width"] * $_POST["height"]) / 1000000;
            $dimension = array(
                "width" => $_POST["width"],
                "height" => $_POST["height"],
                "depth" => $productInfos["depth"]
            );

            $cart->addItem($pid, $pqte, $productInfos["price"], $productInfos["name"], $shipping_amount, $surface, $dimension);

            //Si option
            if (isset($_POST["options"])) {
                $id_option = $_POST["options"];
                $option_price = $productInfos["attributes"][$id_option]["price"];
                $option_name = $productInfos["attributes"][$id_option]["name"];
                $option_weight = $productInfos["attributes"][$id_option]["weight"];
                $shipping_ratio = getDeliveryRatio($option_weight);
                //$shipping_amount = $shipping_ratio * $option_weight;
                $shipping_amount = 0;

                $cart->addItemOption($pid, $id_option, $pqte, $option_price, $option_name, $shipping_amount, $surface, $dimension);
            }
        }

        if (isset($_POST["del"])) {
            $surface = $_SESSION["cart"][$pid]["surface"];

            $cart->removeItem($pid, $pqte, $productInfos["price"], $shipping_amount, $surface);
            //$cart->removeCartItem($_POST["id_cart_item"]);
        }
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
    $nb_produits = count($products);
    $breadcrumb = array("parent" => "Accueil", "fils" => $categorie["name"]);

    $smarty->assign('products', $products);
    $smarty->assign('categorie', $categorie);
}

if (isset($_GET["p"])) {
    $page = "product";
    $product = getProductInfos($_GET["id"]);
    $smarty->assign('product', $product);
    $breadcrumb = array("parent" => "Accueil", "fils" => $product["category"]["name"]);
}
if (isset($_GET["register"])) {
    $page = "register";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Inscription");
}

if (isset($_GET["cart"])) {
    $page = "cart";
    $page_type = "full";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Panier");
}
if (isset($_GET["my-account"])) {
    $page = "my-account";
    $page_type = "full";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Mon compte");
}
if (isset($_GET["identification"])) {
    $page = "identification";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Connexion");
}


if (isset($_GET["cms"])) {
    $page = "cms";
    $cms = getCmsInfo($_GET["id"]);
    $page_type = "full";
    $breadcrumb = array("parent" => "Accueil", "fils" => null);
}
if (isset($_GET["delivery"])) {
    $page = "delivery";
    $page_type = "full";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Livraison");
}
if (isset($_GET["devis"])) {
    $page = "devis";
    $page_type = "full";
}

if (isset($_GET["orders-list"])) {
    $page = "orders-list";
    $orders = getUserOrders($_SESSION["user"]["id_customer"]);
    $breadcrumb = array("parent" => "Accueil", "fils" => "Historique");
    $smarty->assign('orders', $orders);
}



$smarty->assign('PAYPAL_CHECKOUT_FORM', '');

if (isset($_GET["order-resume"])) {
    $page = "order-resume";
    $page_type = "full";

    if (isset($_POST["order_comment"])) {
        $_SESSION["cart_summary"]["order_comment"] = $_POST["order_comment"];
    }
}

if (isset($_GET["order-payment"])) {
    $page = "order-payment";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Paiement");
    $page_type = "full";

    $settings = array(
        'business' => $paypal["email_account"], //paypal email address
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


    $smarty->assign('PAYPAL_CHECKOUT_FORM', $PaypalCheckoutForm);
    ;
}

/* new user */
if (isset($_GET["action"]) && $_GET["action"] == "new_user") {

    $userinfos = array(
        "firstname" => $_POST["firstname"],
        "lastname" => $_POST["lastname"],
        "email" => $_POST["email"],
        "passwd" => md5(_COOKIE_KEY_ . $_POST["passwd"]),
        "active" => 1,
        "date_add" => date("Y-m-d"),
        "date_upd" => date("Y-m-d"));

    if (isset($_SESSION["user"]["id_customer"]) && $_SESSION["user"]["id_customer"] != "") {

        $uid = $_SESSION["user"]["id_customer"];
        $userinfos = array(
            "firstname" => $_POST["firstname"],
            "lastname" => $_POST["lastname"],
            "email" => $_POST["email"],
            "passwd" => md5(_COOKIE_KEY_ . $_POST["passwd"]),
        );

        $invoice_adresse = array(
            "id_customer" => $uid,
            "address1" => @$_POST["invoice_address1"],
            "postcode" => @$_POST["invoice_postcode"],
            "country" => "France",
            "city" => @$_POST["invoice_city"],
            "phone" => $_POST["invoice_phone"],
            "phone_mobile" => $_POST["invoice_phone_mobile"]
        );

        $delivery_adresse = array(
            "id_customer" => $uid,
            "address1" => @$_POST["delivery_address1"],
            "postcode" => @$_POST["delivery_postcode"],
            "country" => "France",
            "city" => @$_POST["delivery_city"],
            "phone" => $_POST["delivery_phone"],
            "phone_mobile" => $_POST["delivery_phone_mobile"]
        );

        updateUserAddress($invoice_adresse, "invoice", $_SESSION["user"]["invoice"]["id_address"]);
        updateUserAddress($delivery_adresse, "delivery", $_SESSION["user"]["delivery"]["id_address"]);

        updateUserAccount($userinfos, $_SESSION["user"]["id_customer"]);
    } else {
        $uid = createNewAccount($userinfos);

        if ($uid) {
            $invoice_adresse = array(
                "alias" => 'invoice',
                "id_customer" => $uid,
                "address1" => @$_POST["invoice_address1"],
                "postcode" => @$_POST["invoice_postcode"],
                "city" => @$_POST["invoice_city"],
                "phone" => $_POST["invoice_phone"],
                "phone_mobile" => $_POST["invoice_phone_mobile"],
                "country" => 'France',
                "active" => 1,
                "date_add" => date("Y-m-d"),
                "date_upd" => date("Y-m-d"));

            if (isset($_POST["liv"])) {
                $delivery_adresse = array(
                    "alias" => 'delivery',
                    "id_customer" => $uid,
                    "address1" => @$_POST["delivery_address1"],
                    "postcode" => @$_POST["delivery_postcode"],
                    "city" => @$_POST["delivery_city"],
                    "phone" => $_POST["delivery_phone"],
                    "phone_mobile" => $_POST["delivery_phone_mobile"],
                    "country" => 'France',
                    "active" => 1,
                    "date_add" => date("Y-m-d"),
                    "date_upd" => date("Y-m-d"));
            } else {
                $delivery_adresse = $invoice_adresse;
                $delivery_adresse["alias"] = 'delivery';
            }

            createNewAdresse($invoice_adresse);
            createNewAdresse($delivery_adresse);

            //auto login
            checkUserLogin($_POST["email"], $_POST["passwd"]);
        } else { //error creation
            $error = array("txt" => "Le compte existe déjà");
            $page = "register";
        }
    }
}

/* Login */
if (isset($_GET["action"]) && $_GET["action"] == "login") {
    $res = checkUserLogin($_POST["email"], $_POST["passwd"]);
    if (!$res) {
        $error = array("txt" => "La connexion a échoué");
        $page = "identification";
    }
}

/* logout */
if (isset($_GET["action"]) && $_GET["action"] == "logout") {
    session_destroy();
    unset($_SESSION);
}


/* Order */
if (isset($_GET["action"]) && $_GET["action"] == "order_validate") {
    $status = 0;
    $page_type = "full";

    if ($_POST["payment"])
        $payment = $_POST["payment"];

    if ($payment == "Chèque") {
        $status = 1;
    } elseif ($payment == "Virement bancaire") {
        $status = 10;
    } elseif ($payment == "Paypal") {
        $status = 2;
    }

    $order_summary = array(
        "id_customer" => $_SESSION["user"]["id_customer"],
        "reference" => RandomString(),
        "id_address_delivery" => $_SESSION["user"]["delivery"]["id_address"],
        "id_address_invoice" => $_SESSION["user"]["invoice"]["id_address"],
        "payment" => $payment,
        "current_state" => $status,
        "total_paid" => $_SESSION["cart_summary"]["total_amount"],
        "invoice_date" => date("Y-m-d h:i:s"),
        "delivery_date" => date("Y-m-d h:i:s"),
        "date_add" => date("Y-m-d h:i:s"),
        "date_upd" => date("Y-m-d h:i:s"),
        "order_comment" => $_SESSION["cart_summary"]["order_comment"],
    );




    $oid = $db->insert("av_orders", $order_summary);

    $order_payment = array(
        "id_order" => $oid,
        "id_currency" => 1,
        "amount" => $_SESSION["cart_summary"]["total_amount"],
        "payment_method" => $payment,
        "conversion_rate" => 1,
        "date_add" => date("Y-m-d h:i:s"),
    );

    $db->insert("av_order_payment", $order_payment);

    foreach ($cartItems as $item) {

        $p = getProductInfos($item["id"]);

        $order_detail = array(
            "id_order" => $oid,
            "id_product" => $item["id"],
            "product_name" => $item["name"],
            "product_quantity" => $item["quantity"],
            "product_price" => $item["price"],
            "product_shipping" => $item["shipping"],
            "product_width" => $item["dimension"]["width"],
            "product_height" => $item["dimension"]["height"],
            "product_depth" => $item["dimension"]["depth"],
            "product_weight" => $item["quantity"] * $p["weight"] * $item["surface"],
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
    $page = "order-confirmation";
    //$orders = getUserOrders($_SESSION["user"]["id_customer"]);
    $smarty->assign('order', $order_summary);
    $smarty->assign('payment', $payment);
}

/**/
if (isset($_SESSION["user"])) {
    $mydevis = $db->rawQuery("select a.*, b.nom, b.prenom 
         from av_devis a , admin_user b 
         where a.id_user = b.id_admin 
         and current_state = 1
         and id_customer = ?", array($_SESSION["user"]["id_customer"]));

    foreach ($mydevis as $k => $devis) {

        $mydevisdetail = $mydevisdetail = $db->where("id_devis", $devis["id_devis"])
                ->get("av_devis_detail");

        $mydevis[$k]["detail"] = $mydevisdetail;
    }
}
/**/

if (isset($_GET["devis"])) {

    if (isset($_GET["action"]) && $_GET["action"] == "view") {
        $devis_id = $_GET["id"];

        $mydevis = $db->rawQuery("select a.*, b.nom, b.prenom 
         from av_devis a , admin_user b 
         where a.id_user = b.id_admin 
         and current_state = 1
         and id_devis = ?
         and id_customer = ?", array($devis_id, $_SESSION["user"]["id_customer"]));


        if ($mydevis) {
            $mydevisdetail = $db->where("id_devis", $devis_id)
                    ->get("av_devis_detail");

            $smarty->assign('mydevisdetail', $mydevisdetail);
        }
    }
    if (isset($_GET["action"]) && $_GET["action"] == "del") {
        $devis_id = $_GET["id"];

        $r = $db->where("id_devis", $devis_id)
                ->where("id_customer", $_SESSION["user"]["id_customer"])
                ->update("av_devis", array("current_state" => 2));
    }


    $smarty->assign('mydevis', $mydevis);
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
$smarty->assign('cms', $cms);
$smarty->assign('AllCMS', $AllCMS);
$smarty->assign('page_info', $page);
$smarty->assign('page_type', $page_type);
$smarty->assign('breadcrumb', $breadcrumb);
$smarty->assign('mydevis', $mydevis);
$smarty->assign('error', $error);
$smarty->assign('config', $config);

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