<?php
include ('configs/settings.php');
require('libs/Smarty.class.php');
require('classes/MysqliDb.php');
require('classes/panier.php');
require('classes/paypal.php');
require('classes/class.phpmailer.php');
require('functions/users.php');
require('functions/products.php');
require('functions/categories.php');
require('functions/orders.php');
require('functions/tools.php');
require('functions/cms.php');
require('classes/CMCIC_Tpe.inc.php');

// classes declaration

$smarty = new Smarty;
//$smarty->caching = 0;
//$smarty->error_reporting = E_ALL & ~E_NOTICE;
$smarty->setTemplateDir(array('templates', 'templates/mails'));


//connexion base de données
$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

//Create a new PHPMailer instance
$mail = new PHPMailer();
//Set who the message is to be sent from
$mail->SetFrom($confmail["from"]);


$promo_id = array(79, 65, 123, 124);
foreach ($promo_id as $id) {
    $promos[] = getProductInfos($id);
}

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

//commande déjà payé on flush
checkOrderPaid();

/* action Caddie */
if (isset($_GET["cart"])) {

    if (isset($_POST["id_product"]) and $_POST["id_product"] != "" && $_POST["quantity"] != "") {
        $pid = $_POST["id_product"];
        $pqte = $_POST["quantity"];
        $prixttc = $_POST["prixttc"];

        $productInfos = getProductInfos($_POST["id_product"]);

        $pweight = $productInfos["weight"];
        $shipping_ratio = getDeliveryRatio($pweight);
        //$shipping_amount = $shipping_ratio * $pweight;
        //$shipping_amount = $conf_shipping_amount;
        $shipping_amount = 0;

        if (isset($_POST["add"])) {
            $dimension = array();

            if (isset($_POST["width"]) && !empty($_POST["width"]) && isset($_POST["height"]) && !empty($_POST["height"])) {
                $surface = ($_POST["width"] * $_POST["height"]) / 1000000;
                $dimension = array(
                    "width" => $_POST["width"],
                    "height" => $_POST["height"],
                    "depth" => $productInfos["depth"]
                );
            }

            $cart->addItem($pid, $pqte, round($productInfos["price"], 2), $productInfos["name"], $shipping_amount, $surface, $dimension, $productInfos);

//Si option
            if (isset($_POST["options"])) {
                if (is_array($_POST["options"])) {
                    foreach ($_POST["options"] as $id_combination => $id_option) {
                        $option_price = $productInfos["combinations"][$id_combination]["attributes"][$id_option]["price"];
                        $option_name = $productInfos["combinations"][$id_combination]["attributes"][$id_option]["name"];
                        $option_weight = $productInfos["combinations"][$id_combination]["attributes"][$id_option]["weight"];
                        $shipping_ratio = getDeliveryRatio($option_weight);
//$shipping_amount = $shipping_ratio * $option_weight;
                        $shipping_amount = 0;

                        $cart->addItemOption($pid, $id_option, $pqte, $option_price, $option_name, $shipping_amount, $surface, $dimension);
                    }
                }
            }


            $_SESSION["cart_summary"]['total_shipping'] = $conf_shipping_amount;
        }

        if (isset($_POST["del"])) {
            $surface = $_SESSION["cart"][$pid]["surface"];
            $pqte = $_SESSION["cart"][$pid]["quantity"];
            $price = $_SESSION["cart"][$pid]["price"];

            $cart->removeItem($pid, $pqte, $price, $shipping_amount, $surface);
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
    $page_type = "full";
    $page = "identification";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Connexion");
}
if (isset($_GET["order-identification"])) {
    $page = "order-identification";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Connexion");
    $page_type = "full";
}

if (isset($_GET["contact-devis"])) {
    $page = "contact-devis";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Demande de devis");
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
if (isset($_GET["contactez-nous"])) {
    $page = "contact";
}

if (isset($_GET["orders-list"])) {
    $page = "orders-list";
    $orders = getUserOrders($_SESSION["user"]["id_customer"]);
    $breadcrumb = array("parent" => "Accueil", "fils" => "Historique");
    $smarty->assign('orders', $orders);
}



$smarty->assign('PAYPAL_CHECKOUT_FORM', '');
$smarty->assign('CMCIC_CHECKOUT_FORM', '');

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

    saveorder();

    // PAYPAL
    $settings = array(
        'business' => $paypal["email_account"], //paypal email address
        'currency' => 'EUR', //paypal currency
        'cursymbol' => '&euro;', //currency symbol
        'location' => 'FR', //location code  (ex GB)
        'returnurl' => $paypal["returnurl"], //where to go back when the transaction is done.
        'returntxt' => 'Retour au site', //What is written on the return button in paypal
        'cancelurl' => $paypal["cancelurl"], //Where to go if the user cancels.
        'returnipn' => $paypal["returnipn"], //Where to go if the user cancels.
        'shipping' => $conf_shipping_amount, //Shipping Cost
        'invoice' => $_SESSION["id_order"], // order ref
        'custom' => ''                           //Custom attribute
    );

    $pp = new paypalcheckout($settings); //Create an instance of the class
    $pp->addMultipleItems($cartItems); //Add all the items to the cart in one go
    //$cartHTML = $pp->getCartContentAsHtml();
    $PaypalCheckoutForm = $pp->getCheckoutForm();
    $smarty->assign('PAYPAL_CHECKOUT_FORM', $PaypalCheckoutForm);
    // fin paypal
    // CMCIC
    $sOptions = "";
    $sReference = $_SESSION["id_order"];
    $sMontant = $_SESSION["cart_summary"]["total_amount"] + $_SESSION["cart_summary"]["total_shipping"];
    $sDevise = "EUR";
    $sDate = date("d/m/Y:H:i:s");
    $sLangue = "FR";
    $sTexteLibre = "Texte";
    $sEmail = $_SESSION["user"]["email"];

    $sNbrEch = "";
    $sDateEcheance1 = "";
    $sMontantEcheance1 = "";
    $sDateEcheance2 = "";
    $sMontantEcheance2 = "";
    $sDateEcheance3 = "";
    $sMontantEcheance3 = "";
    $sDateEcheance4 = "";
    $sMontantEcheance4 = "";

    $oTpe = new CMCIC_Tpe($sLangue);
    $oHmac = new CMCIC_Hmac($oTpe);

    // Data to certify
    $PHP1_FIELDS = sprintf(CMCIC_CGI1_FIELDS, $oTpe->sNumero, $sDate, $sMontant, $sDevise, $sReference, $sTexteLibre, $oTpe->sVersion, $oTpe->sLangue, $oTpe->sCodeSociete, $sEmail, $sNbrEch, $sDateEcheance1, $sMontantEcheance1, $sDateEcheance2, $sMontantEcheance2, $sDateEcheance3, $sMontantEcheance3, $sDateEcheance4, $sMontantEcheance4, $sOptions);

    // MAC computation
    $sMAC = $oHmac->computeHmac($PHP1_FIELDS);

    $CMCICCheckoutForm = '
    <form action="' . $oTpe->sUrlPaiement . '" method="post" id="PaymentRequest">
        <p>
            <input type="hidden" name="version"             id="version"        value="' . $oTpe->sVersion . '" />
            <input type="hidden" name="TPE"                 id="TPE"            value="' . $oTpe->sNumero . '" />
            <input type="hidden" name="date"                id="date"           value="' . $sDate . '" />
            <input type="hidden" name="montant"             id="montant"        value="' . $sMontant . $sDevise . '" />
            <input type="hidden" name="reference"           id="reference"      value="' . $sReference . '" />
            <input type="hidden" name="MAC"                 id="MAC"            value="' . $sMAC . '" />
            <input type="hidden" name="url_retour"          id="url_retour"     value="' . $oTpe->sUrlKO . '" />
            <input type="hidden" name="url_retour_ok"       id="url_retour_ok"  value="' . $oTpe->sUrlOK . '" />
            <input type="hidden" name="url_retour_err"      id="url_retour_err" value="' . $oTpe->sUrlKO . '" />
            <input type="hidden" name="lgue"                id="lgue"           value="' . $oTpe->sLangue . '" />
            <input type="hidden" name="societe"             id="societe"        value="' . $oTpe->sCodeSociete . '" />
            <input type="hidden" name="texte-libre"         id="texte-libre"    value="' . HtmlEncode($sTexteLibre) . '" />
            <input type="hidden" name="mail"                id="mail"           value="' . $sEmail . '" />
            <!-- -->
            <input type="hidden" name="nbrech"              id="nbrech"         value="" />
            <input type="hidden" name="dateech1"            id="dateech1"       value="" />
            <input type="hidden" name="montantech1"         id="montantech1"    value="" />
            <input type="hidden" name="dateech2"            id="dateech2"       value="" />
            <input type="hidden" name="montantech2"         id="montantech2"    value="" />
            <input type="hidden" name="dateech3"            id="dateech3"       value="" />
            <input type="hidden" name="montantech3"         id="montantech3"    value="" />
            <input type="hidden" name="dateech4"            id="dateech4"       value="" />
            <input type="hidden" name="montantech4"         id="montantech4"    value="" />
            
            <input type="submit" name="bouton"              id="bouton"         class ="pay_cb" value="Payer par carte bancaire" />
        </p>
    </form>';

    $smarty->assign('CMCIC_CHECKOUT_FORM', $CMCICCheckoutForm);
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

    //compte existe déjà on update
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

            //envoie mail
            $mail->AddAddress($_POST["email"]);
            $mail->Subject = $confmail["welcome"];
            $smarty->assign("email", $_POST["email"]);
            $smarty->assign("mdp", $_POST["passwd"]);

            $user_mail_body = $smarty->fetch('notif_new_acccount.tpl');
            $mail->MsgHTML($user_mail_body);
            $mail->Send();
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
        $page_type = "full";
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

    validateOrder($_SESSION["id_order"], array("current_state" => $status, "payment" => $payment));

    //on redirige sur la listes des commandes
    $page = "order-confirmation";

    $smarty->assign('reference', $_SESSION["reference"]);
    $smarty->assign('payment', $payment);

    //on flush le caddie
    unset($_SESSION["cart"]);
    unset($_SESSION["cart_summary"]);
    unset($_SESSION["id_order"]);
    unset($_SESSION["reference"]);
    $cartItems = array();
}

/* paiement CB */

if (isset($_GET["paiementok"])) {
    $status = 0;
    $page_type = "full";

    $smarty->assign('reference', $_SESSION["reference"]);

    $page = "order-confirmation";

    //on flush le caddie
    unset($_SESSION["cart"]);
    unset($_SESSION["cart_summary"]);
    unset($_SESSION["id_order"]);
    unset($_SESSION["reference"]);
    $cartItems = array();
}

/* devis */
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

        $mydevis = $db->rawQuery("select a.*, b.nom, b.prenom 
                                from av_devis a , admin_user b 
                                where a.id_user = b.id_admin 
                                and current_state = 1
                                and id_devis = ?
                                and id_customer = ?", array($devis_id, $_SESSION["user"]["id_customer"]));
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
//$smarty->assign('meta', $meta);
$smarty->assign('promos', $promos);

$smarty->display('index.tpl');
?>
<?
if ($_SESSION["user"]["email"] == "stephane.alamichel@gmail.com") {
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
    <br>
    <?
}
?>