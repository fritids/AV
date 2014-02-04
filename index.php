<?php
if ($_SERVER['REQUEST_URI'] == '/index.php') {
    header("Status: 301 Moved Permanently", false, 301);
    header("Location: /");
}
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
require('functions/devis.php');
require('functions/voucher.php');
//require('classes/CMCIC_Tpe.inc.php');
require('classes/tcpdf.php');

// classes declaration

$smarty = new Smarty;
//$smarty->caching = 0;
//$smarty->error_reporting = E_ALL & ~E_NOTICE;
$smarty->setTemplateDir(array('templates', 'templates/mails', 'templates/pdf/front'));

/* init pdf */
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Allovitre');
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetFont('times', '', 10);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING);

//connexion base de données
$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

//Create a new PHPMailer instance
$mail = new PHPMailer();
//Set who the message is to be sent from
$mail->SetFrom($confmail["from"]);
$mail->CharSet = 'UTF-8';

// récupère les produits en promo

$r = $db->rawQuery("select * from av_product where is_promo = 1 and id_product in (56, 65, 49, 124)");
foreach ($r as $p) {
    $promoshome[] = getProductInfos($p["id_product"]);
}
$r = $db->rawQuery("select * from av_product where is_promo = 1");
foreach ($r as $p) {
    $promos[] = getProductInfos($p["id_product"]);
}

/* vars */
$nb_produits = 0;
$page_type = "";
$mydevis = array();
$ko_msg = array();
$breadcrumb = array("parent" => NULL, "fils" => null);
$sub_menu = getCategories();
$secured_pages = array("my-account", "devis", "orders-list", "order-resume", "delivery", "order-payment");
$search = getSearchCriterias();
$search_result = array();

/* Cms */
$AllCMS = getAllCmsInfo();
$cms = array();
/* fin */

/* Meta */
$meta = array(
    "title" => "ALLOVITRES : verre pas cher, tout type de vitrage sur mesure. - Allovitres.com",
    "description" => "Allovitres propose la vente en ligne de vitres pas chers. Simple vitrage, double vitrage, verre décoratifs ou autres verres spécifiques, toutes nos offres de vitres sont à tarif discount.",
    "keywords" => "simple vitrage, double vitrage, verre décoratifs, verre spécifique, verre feuilleté, miroir, verre surmesure, allovitres"
);
/**/

//Caddie
$cart = new Panier();

//commande déjà payé on flush
checkOrderPaid();



/* action Caddie */
if (isset($_GET["cart"])) {

    if (isset($_POST["id_product"]) and $_POST["id_product"] != "" && $_POST["quantity"] != "") {
        $pid = $_POST["id_product"];
        $pqte = $_POST["quantity"];
        $pcustom = @$_POST["custom"];

        $productInfos = getProductInfos($_POST["id_product"]);

        if ($pcustom) {
            $mapCustomAttribute = mapCustomAttribute($pcustom);
            $productInfos["custom_label"] = $mapCustomAttribute;
            $productInfos["custom"] = $pcustom;
        }
        $pweight = $productInfos["weight"];
        $shipping_ratio = getDeliveryRatio($pweight);
        //$shipping_amount = $shipping_ratio * $pweight;
        //$shipping_amount = $conf_shipping_amount;
        $shipping_amount = 0;
        $shape_impact_coef = 1;
        $impact_coef = 1;

        if (isset($_POST["add"])) {
            $dimension = array();

            if ($_POST["width"] < $productInfos["min_width"] && $productInfos["min_width"] > 0)
                $tmperr.= "Largeur minimum requise " . $productInfos["min_width"] . " mm <br>";
            if ($_POST["height"] < $productInfos["min_height"] && $productInfos["min_height"] > 0)
                $tmperr.= "Longueur minimum requise " . $productInfos["min_height"] . " mm<br>";
            if ($_POST["width"] > $productInfos["max_width"] && $productInfos["max_width"] > 0)
                $tmperr.= "Largeur maximum autorisé " . $productInfos["max_width"] . " mm<br>";
            if ($_POST["height"] > $productInfos["max_height"] && $productInfos["max_height"] > 0)
                $tmperr.= "Taille maximum autorisé " . $productInfos["max_height"] . " mm<br>";

            if ($tmperr)
                $ko_msg = array("txt" => $tmperr);

            if (isset($_POST["width"]) && !empty($_POST["width"]) && isset($_POST["height"]) && !empty($_POST["height"])) {
                $surface = ($_POST["width"] * $_POST["height"]) / 1000000;

                if ($surface < $productInfos["min_area_invoiced"])
                    $surface = $productInfos["min_area_invoiced"];
                if ($surface >= $productInfos["max_area_invoiced"])
                    $impact_coef = 1.5;

                $dimension = array(
                    "width" => $_POST["width"],
                    "height" => $_POST["height"],
                    "depth" => $productInfos["depth"]
                );
            }

            //Si option
            if (isset($_POST["custom"])) {
                if (is_array($_POST["custom"])) {
                    foreach ($mapCustomAttribute as $custom_item) {
                        if (is_array($custom_item)) {
                            foreach ($custom_item as $k => $sub_items) {
                                foreach ($sub_items as $l => $sub_item) {
                                    if ($sub_item["price_impact_percentage"] > 0) {
                                        $productInfos["price"] *= $sub_item["price_impact_percentage"];
                                        $shape_impact_coef = $sub_item["price_impact_percentage"];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $nbItem = $cart->getNbItems() + 1;

            if (empty($ko_msg))
                $cart->addItem($pid, $pqte, $productInfos["price"] * $impact_coef * $shape_impact_coef, $productInfos["name"], $shipping_amount, $surface, $dimension, $productInfos, $nbItem);

            //Si option
            if (empty($ko_msg) && isset($_POST["options"])) {
                if (is_array($_POST["options"])) {
                    foreach ($_POST["options"] as $id_combination => $id_option) {
                        $option_price = $productInfos["combinations"][$id_combination]["attributes"][$id_option]["price"];
                        $option_name = $productInfos["combinations"][$id_combination]["attributes"][$id_option]["name"];
                        $option_weight = $productInfos["combinations"][$id_combination]["attributes"][$id_option]["weight"];
                        $shipping_ratio = getDeliveryRatio($option_weight);
                        //$shipping_amount = $shipping_ratio * $option_weight;
                        $shipping_amount = 0;

                        $cart->addItemOption($pid, $id_option, $pqte, $option_price * $impact_coef * $shape_impact_coef, $option_name, $shipping_amount, $surface, $dimension, $nbItem);
                    }
                }
            }

            $_SESSION["cart_summary"]['total_shipping'] = $conf_shipping_amount;
        }

        if (isset($_POST["del"])) {
            $nitem = $_POST["id_cart_item"];

            $surface = $_SESSION["cart"][$nitem][$pid]["surface"];
            $pqte = $_SESSION["cart"][$nitem][$pid]["quantity"];
            $price = $_SESSION["cart"][$nitem][$pid]["prixttc"];

            $cart->removeItem($pid, $pqte, $price, $shipping_amount, $surface, $nitem);
            //$cart->removeCartItem($_POST["id_cart_item"]);
        }

        // on empecher de faire un F5
        if (empty($ko_msg))
            header("Location: index.php?cart");
    }
}

$cartItems = $cart->showCart();
$cart_nb_items = count($cartItems);

if ($cart_nb_items > 0)
    if ($_SESSION["user"]["customer_group"] == 1) {
        $cart->applyProDiscount();
    }

if (isset($_GET["p"])) {
    //array_splice($_SESSION["cart"],$_GET["p"]);
    unset($_SESSION["cart"][$_GET["p"]]);
}



/* dispatcher */
$page = "home";

if (isset($_GET["c"])) {
    $page = "category";
    $categorie = getCategorieInfo($_GET["id"]);
    $products = getProductByCategorie($_GET["id"]);
    $nb_produits = count($products);
    $breadcrumb = array("parent" => "Accueil", "fils" => $categorie["name"]);

    $meta["title"] = $categorie["meta_title"];
    $meta["description"] = $categorie["meta_description"];
    $meta["keywords"] = $categorie["meta_keywords"];

    $smarty->assign('products', $products);
    $smarty->assign('categorie', $categorie);
}

if (isset($_GET["p"])) {
    $page = "product";
    $product = getProductInfos($_GET["id"]);

    if (isset($product["name"])) {
        if (empty($product["meta_title"])) {
            $meta["title"] = $product["name"];
        } else {
            $meta["title"] = $product["meta_title"];
        }
        $meta["description"] = $product["meta_description"];
        $meta["keywords"] = $product["meta_keywords"];

        $smarty->assign('product', $product);
        $breadcrumb = array("parent" => "Accueil", "fils" => $product["category"]["name"]);
    } else {
        $ko_msg = array("txt" => "Le produit demandé n'existe plus.");
        $page = "generic_page";
    }
}
if (isset($_GET["register"])) {
    $page = "register";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Inscription");
}
if (isset($_GET["registerpro"])) {
    $page = "register-pro";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Inscription Pro");
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
    $page_type = "full";

    $cms = getCmsInfo($_GET["id"]);

    $meta["title"] = $cms["meta_title"];
    $meta["description"] = $cms["meta_description"];
    $meta["keywords"] = $cms["meta_keywords"];
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
if (isset($_GET["sitemap"])) {
    $page = "plan-site";
    $page_type = "full";
}
if (isset($_GET["search"])) {
    $page = "search";

    if (isset($_POST["search_query"]) && !empty($_POST["search_query"])) {
        $search_result = getSearchResultsByName($_POST["search_query"]);
    } else {
        $param1 = $_POST["search_lvl_1"];
        $param2 = $_POST["search_lvl_2"];
        $search_result = getSearchResults($param1, $param2);
    }
}
if (isset($_GET["product_custom"])) {
    $page = "product_custom";
    $product = getProductInfos($_GET["id"]);

    if (empty($product["meta_title"])) {
        $meta["title"] = $product["name"];
    } else {
        $meta["title"] = $product["meta_title"];
    }

    $smarty->assign('product', $product);
    $breadcrumb = array("parent" => "Accueil", "fils" => $product["category"]["name"]);
}

if (isset($_GET["orders-list"])) {
    $page = "orders-list";
    $page_type = "full";
    $orders = getUserOrders($_SESSION["user"]["id_customer"]);
    $breadcrumb = array("parent" => "Accueil", "fils" => "Historique");
    $smarty->assign('orders', $orders);
}
if (isset($_GET["order-error"])) {
    $page = "order-error";
    $page_type = "full";
}
if (isset($_GET["order-confirmation"])) {
    $page = "order-confirmation";
    $page_type = "full";
}
if (isset($_GET["promotions"])) {
    $page = "promotions";
}

$smarty->assign('PAYPAL_CHECKOUT_FORM', '');
$smarty->assign('CMCIC_CHECKOUT_FORM', '');

if (isset($_GET["order-resume"])) {
    $page = "order-resume";
    $page_type = "full";

    if (isset($_POST["alert_sms"]) && $_POST["alert_sms"] == 1 && !isset($_SESSION["cart_summary"]["order_option"])) {
        $_SESSION["cart_summary"]["total_amount"] += 1;
        $_SESSION["cart_summary"]["order_option"] = "SMS";
    }

    if (isset($_POST["alert_sms"]) && $_POST["alert_sms"] == 1) {
        $_SESSION["cart_summary"]["alert_sms_phone"] = $_POST["alert_sms_phone"];
    }

    // option déja souscrite on retire l'option
    if (!isset($_POST["alert_sms"]) && isset($_SESSION["cart_summary"]["order_option"])) {
        $_SESSION["cart_summary"]["total_amount"] -= 1;
        unset($_SESSION["cart_summary"]["order_option"]);
    }

    if (isset($_POST["order_comment"])) {
        $_SESSION["cart_summary"]["order_comment"] = $_POST["order_comment"];
    }
}

if (isset($_GET["order-payment"])) {
    $page = "order-payment";
    $breadcrumb = array("parent" => "Accueil", "fils" => "Paiement");
    $page_type = "full";
    $extra_options = 0;

    if ($_SESSION["cart_summary"]["order_option"] == "SMS") {
        $extra_options = 1;
    }

    if ($_SESSION["cart_summary"]["order_option"] == "SMS") {
        $extra_options = 1;
    }
    /* if (isset($_SESSION["cart_summary"]["total_discount"]) && $_SESSION["cart_summary"]["total_discount"] > 0) {
      $extra_options -= $_SESSION["cart_summary"]["total_discount"];
      } */
    $total_paid = round($_SESSION["cart_summary"]["total_amount"] + $_SESSION["cart_summary"]["total_shipping"] - $_SESSION["cart_summary"]["total_discount"], 2);

    saveorder();

    if (empty($_SESSION["id_order"])) {
        $ko_msg = array("txt" => "Une erreur est survenue lors de la finalisation de votre commande, veuillez retourner dans votre caddie et recommencer la procédure");
        $page = "generic_page";
    } else {
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
            'shipping' => 0, //Shipping Cost        
            'invoice' => $_SESSION["id_order"], // order ref
            'custom' => ''                           //Custom attribute
        );


        $pp = new paypalcheckout($settings); //Create an instance of the class
        //$pp->addMultipleItems($cartItems); //Add all the items to the cart in one go
        $pp->addSimpleItem(array(
            "name" => "Commande Allovitres#" . $_SESSION["reference"],
            "quantity" => 1,
            "prixttc" => $total_paid,
            "discount" => 0
        )); //Add all the items to the cart in one go
        //$cartHTML = $pp->getCartContentAsHtml();
        $PaypalCheckoutForm = $pp->getCheckoutForm();

        $smarty->assign('PAYPAL_CHECKOUT_FORM', $PaypalCheckoutForm);

        //Socgen
        $parm = "merchant_id=075218256800011";
        $parm = "$parm merchant_country=fr";
        $parm = "$parm amount=" . $total_paid * 100;
        $parm = "$parm transaction_id=" . $_SESSION["id_order"];
        $parm = "$parm order_id=" . $_SESSION["id_order"];
        $parm = "$parm currency_code=978";
        $parm = "$parm pathfile=/trusttelecom.fr/paiement/param/pathfile";
        $path_bin = "/trusttelecom.fr/paiement/bin/request";
        $parm = escapeshellcmd($parm);
        $result = exec("$path_bin $parm");
        $tableau = explode("!", "$result");
        $code = $tableau[1];
        $error = $tableau[2];
        $message = $tableau[3];


        if (( $code == "" ) && ( $error == "" )) {
            $ko_msg = array("txt" => "erreur appel request<BR> executable request non trouve $path_bin");
        }
        //	Erreur, affiche le message d'erreur
        else if ($code != 0) {
            //$ko_msg = array("txt" => "<h2>Erreur appel API de paiement.</h2><br><br><br> message erreur : $error <br>");
            if ($_SESSION["user"]["email"] == "stephane.alamichel@gmail.com") {
                echo $error . "<br>" . $parm;
            }
        }
        //	OK, affiche le formulaire HTML
        else {
            # OK, affichage du mode DEBUG si activé
            //print (" $error <br>");
            $smarty->assign('SOCGEN_CHECKOUT_FORM', $message);
        }
    }
}

/* new user */
if (isset($_GET["action"]) && $_GET["action"] == "new_user") {
    $group = 0;
    if (isset($_POST["is_pro"])) {
        $group = 1;
    }

    $userinfos = array(
        "firstname" => $_POST["firstname"],
        "lastname" => $_POST["lastname"],
        "email" => $_POST["email"],
        "passwd" => md5(_COOKIE_KEY_ . $_POST["passwd"]),
        "active" => 0,
        "secure_key" => genSecureKey(),
        "date_add" => date("Y-m-d"),
        "date_upd" => date("Y-m-d"),
        "customer_group" => $group
    );

    //compte existe déjà on update
    if (isset($_SESSION["user"]["id_customer"]) && $_SESSION["user"]["id_customer"] != "") {

        $uid = $_SESSION["user"]["id_customer"];
        $userinfos = array(
            "firstname" => $_POST["firstname"],
            "lastname" => $_POST["lastname"],
            "email" => $_POST["email"],
            "passwd" => md5(_COOKIE_KEY_ . $_POST["passwd"])
        );

        $invoice_adresse = array(
            "id_customer" => $uid,
            "address1" => @$_POST["invoice_address1"],
            "postcode" => @$_POST["invoice_postcode"],
            "country" => "France",
            "city" => @$_POST["invoice_city"],
            "phone" => $_POST["invoice_phone"],
            "phone_mobile" => $_POST["invoice_phone_mobile"],
            "date_add" => date("Y-m-d"),
            "date_upd" => date("Y-m-d"),
        );

        $delivery_adresse = array(
            "id_customer" => $uid,
            "address1" => @$_POST["delivery_address1"],
            "postcode" => @$_POST["delivery_postcode"],
            "country" => "France",
            "city" => @$_POST["delivery_city"],
            "phone" => $_POST["delivery_phone"],
            "phone_mobile" => $_POST["delivery_phone_mobile"],
            "date_add" => date("Y-m-d"),
            "date_upd" => date("Y-m-d"),
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
            //checkUserLogin($_POST["email"], $_POST["passwd"]);

            $secure_key = getSecureKey($_POST["email"]);
            $account_conf_link = $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "index.php?action=conf_mail&email=" . $_POST["email"] . "&secure_key=" . $secure_key;

            //envoi mail
            $mail->AddAddress($_POST["email"]);
            $mail->Subject = "Allovitres - " . $confmail["welcome"];
            $smarty->assign("account_conf_link", $account_conf_link);
            $smarty->assign("email", $_POST["email"]);
            $smarty->assign("mdp", $_POST["passwd"]);

            $user_mail_body = $smarty->fetch('notif_new_account.tpl');
            $mail->MsgHTML($user_mail_body);
            if ($mail->Send()) {
                $page = "generic_page";
                $page_type = "full";
                $ok_msg = array("txt" => "Merci de confirmer votre compte en suivant les instructions envoyés par email");
            } else {
                $page = "generic_page";
                $page_type = "full";
                $ko_msg = array("txt" => "Une erreur est survenue lors de la création de votre compte.");
            }
        } else { //error creation
            $ko_msg = array("txt" => "Le compte existe déjà, merci de faire une demande d'un nouveau mot de passe.");
            $page = "register";
            if ($group == 1)
                $page = "register-pro";
        }
    }
}

/* Login */
if (isset($_GET["action"]) && $_GET["action"] == "login") {
    $res = checkUserLogin($_POST["email"], $_POST["passwd"]);
    if ($res > 0) {
        switch ($res) {
            case 1:
                $ko_msg = array("txt" => "Mot de passe incorrect, demander un nouveau mot de passe");
                $page = "identification";
                $page_type = "full";
                break;
            case 2:
                $ko_msg = array("txt" => "Votre compte est inactif, un mail d'activation vous a été envoyé sur " . $_POST["email"]);
                $page = "generic_page";
                $page_type = "full";

                $mail->AddAddress($_POST["email"]);
                $mail->Subject = "Allovitre - confirmation de votre compte";
                foreach ($monitoringEmails as $bccer) {
                    $mail->AddBCC($bccer);
                }
                $secure_key = getSecureKey($_POST["email"]);
                $account_conf_link = $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]) . "index.php?action=conf_mail&email=" . $_POST["email"] . "&secure_key=" . $secure_key;

                $smarty->assign("account_conf_link", $account_conf_link);
                $mail_body = $smarty->fetch("mail_account_confirm.tpl");
                $mail->MsgHTML($mail_body);
                $mail->Send();
                break;
        }
    } else {
        if (isset($_POST["referer"]) && !empty($_POST["referer"]))
            header("Location: " . $_POST["referer"]);
        if (empty($_POST["referer"]))
            header("Location: index.php?my-account");
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
if (isset($_GET["devis"])) {
    if (isset($_GET["action"]) && $_GET["action"] == "del") {
        $devis_id = $_GET["id"];

        $r = $db->where("id_devis", $devis_id)
                ->where("id_customer", $_SESSION["user"]["id_customer"])
                ->update("av_devis ", array("current_state" => 2));
    }
    $smarty->assign('mydevis', $mydevis);
}

if (isset($_GET["action"]) && $_GET["action"] == "send_devis") {
    $page = "contact-devis";
    $mail->ClearAllRecipients();

    $contact_infos = array("lastname" => $_POST["lastname"],
        "firstname" => $_POST["firstname"],
        "tel" => $_POST["tel"],
        "phone" => $_POST["tel"],
        "email" => $_POST["email"],
        "demande" => $_POST["demande"],
        //"request" => $_POST["demande"],
        "id_customer" => @$_SESSION["user"]["id_customer"]
    );

    if ($_FILES['pj']['tmp_name'] != '') {
        $tmp_name = $_FILES['pj']['tmp_name'];
        $type = $_FILES['pj']['type'];
        $file_name = $_FILES['pj']['name'];
        $size = $_FILES['pj']['size'];
        $uploaddir = './upload/';
        $uploadfile = $uploaddir . basename($file_name);
    }

    // if the upload succeded, the file will exist
    if ($tmp_name != '') {
        if (move_uploaded_file($tmp_name, $uploadfile)) {
            $mail->AddAttachment($uploadfile);
        } /* else {
          echo "Attaque potentielle par téléchargement de fichiers.
          Voici plus d'informations :\n";
          } */
    }
    //envoie mail
    $mail->AddAddress($confmail["devis_contact"]);
    foreach ($monitoringEmails as $bccer) {
        $mail->AddBCC($bccer);
    }
    $mail->SetFrom($_POST["email"]);

    $mail->Subject = $confmail["devis_subject"];
    $smarty->assign("contact", $contact_infos);
    $mail_body = $smarty->fetch('notif_demande_devis.tpl');

    $mail->MsgHTML($mail_body);
    if ($mail->Send()) {
        $ok_msg = array("txt" => "Votre demande de devis a été envoyé");
    } else {
        $ko_msg = array("txt" => "Une erreur s'est produite pendant l'envoi de votre demande.");
    }
}

if (isset($_GET["action"]) && $_GET["action"] == "order_devis") {
    $orderDevis = getDevis($_POST["id_devis"]);



    foreach ($orderDevis[0]["details"] as $k => $odd) {
        $dimension = array();
        $productInfos = array();
        $shipping_amount = 0;
        $surface = 0;
        $impact_coef = 1;
        $shape_impact_coef = 1;
        $pcustom = array();

        $nbItem = $cart->getNbItems() + 1;
        $pqte = $odd["product_quantity"];


        //produit standard
        if ($odd["id_product"] > 0) {
            $pid = $odd["id_product"];
            $productInfos = getProductInfos($pid);
            $surface = ($odd["product_width"] * $odd["product_height"]) / 1000000;

            if ($surface < $productInfos["min_area_invoiced"])
                $surface = $productInfos["min_area_invoiced"];
            if ($surface >= $productInfos["max_area_invoiced"])
                $impact_coef = 1.5;

            if ($odd["product_width"] > 0 && $odd["product_height"]) {
                $dimension = array(
                    "width" => $odd["product_width"],
                    "height" => $odd["product_height"]
                );
            }

            if (isset($odd["custom"])) {
                foreach ($odd["custom"] as $custom) {
                    foreach ($custom["sub_item"] as $sub_item) {
                        foreach ($sub_item["item_values"] as $k => $item_value) {
                            $pcustom[$item_value["id_attribute"]][$item_value["id_attributes_items"]][$k][$item_value["id_attributes_items_values"]] = $item_value["custom_value"];
                        }
                    }
                }
            }
            if ($pcustom) {
                $mapCustomAttribute = mapCustomAttribute($pcustom);
                $productInfos["custom_label"] = $mapCustomAttribute;
                $productInfos["custom"] = $pcustom;
            }

            $cart->addItem($pid, $pqte, $odd["product_price"] * $impact_coef, "DEVIS#" . $odd["id_devis"] . "-" . $odd["product_name"], $shipping_amount, $surface, $dimension, $productInfos, $nbItem);
            foreach ($odd["combinations"] as $i => $attribute) {
                $option_price = $attribute["prixttc"];
                $option_name = $attribute["name"];
                $option_weight = $attribute["weight"];
                $id_option = $attribute["id_attribute"];
                //$shipping_amount = $shipping_ratio * $option_weight;
                $shipping_amount = 0;
                $cart->addItemOption($pid, $id_option, $pqte, $option_price * $impact_coef, $option_name, $shipping_amount, $surface, $dimension, $nbItem);
            }

            // TOTO
        } else {
            $surface = 0;
            $dimension = array();
            $productInfos = array();

            $cart->addItem($odd["id_devis_detail"], $pqte, $odd["product_price"], "DEVIS#" . $odd["id_devis"] . "-" . $odd["product_name"], $shipping_amount, $surface, $dimension, $productInfos, $nbItem);
        }
    }

    $_SESSION["cart_summary"]['total_shipping'] = $conf_shipping_amount;

    header("Location: index.php?cart");
}

if (isset($_GET["action"]) && $_GET["action"] == "add_voucher") {
    $code = $_POST["voucher_code"];
    $ko_msg = array("txt" => "Ce bon de réduction est erroné");

    if (isset($_POST["voucher_code"]))
        $voucherInfo = getVoucherCode($code, $_SESSION["user"]["id_customer"]);

    if (!empty($voucherInfo) && $voucherInfo["code"] == $code) {
        $cart->addOrderVoucher($voucherInfo);
        $ok_msg = array("txt" => "Bon de réduction a été ajouté");
        $ko_msg = array();
    }

    /*if ($code == "VICTOIREPAUC") {
        $cart->addVoucher(array(
            "code" => "VICTOIREPAUC",
            "title" => "VICTOIREPAUC",
            "group" => "category",
            "value" => 12,
            "reduction" => 10)
        );
        $ok_msg = array("txt" => "Bon de réduction a été ajouté");
        $ko_msg = array();
    }

    /* if ($code == "NOEL2013DV") {
      $cart->addVoucher(array(
      "code" => "NOEL2013DV",
      "title" => "NOEL2013DV",
      "group" => "category",
      "value" => 12,
      "reduction" => 10)
      );
      $ok_msg = array("txt" => "Bon de réduction a été ajouté");
      $ko_msg = array();
      } */
}

// mot de passe oublié
if (isset($_GET["action"]) && $_GET["action"] == "lost_pwd") {
    $email = $_POST["email"];
    $passwd = RandomString();

    $params = array(
        "passwd" => md5(_COOKIE_KEY_ . $passwd),
        "date_upd" => date("y-m-d H:i:s")
    );
    $r = $db->where("email", $email)
            ->update(("av_customer"), $params);
    if ($r) {
        $page = "identification";
        $page_type = "full";
        //envoie mail
        $mail->AddAddress($_POST["email"]);
        $mail->Subject = "Allovitres - nouveau mot de passe";
        $smarty->assign("email", $email);
        $smarty->assign("mdp", $passwd);
        foreach ($monitoringEmails as $bccer) {
            $mail->AddBCC($bccer);
        }
        $user_mail_body = $smarty->fetch('notif_send_pwd.tpl');
        $mail->MsgHTML($user_mail_body);
        if ($mail->Send()) {
            $ok_msg = array("txt" => "Un nouveau mot de passe vous a été envoyé");
        }
    }
}

/* Fichier pdf */
if (isset($_GET["action"]) && $_GET["action"] == "dl_facture") {

    if ($_POST["id_order"]) {

        $mail->ClearAllRecipients();

        $oid = $_POST["id_order"];
        $now = date("d-m-y");
        $orderinfo = getOrderInfos($oid);
        if (!empty($orderinfo) && $orderinfo["id_customer"] == $_SESSION["user"]["id_customer"]) {
            $smarty->assign("orderinfo", $orderinfo);
            $content_body = $smarty->fetch('front_order.tpl');

            $pdf->AddPage('P', 'A4');
            $pdf->writeHTML($content_body, true, false, true, false, '');

            if ($orderinfo["nb_custom_product"] > 0) {
                $annexe_body = $smarty->fetch('front_annexe.tpl');
                $pdf->AddPage('P', 'A4');
                $pdf->writeHTML($annexe_body, true, false, true, false, '');
            }
            $pdf->lastPage();
            $pdf->Output("AV_FA_" . $oid . "_" . $now . ".pdf", 'D');

            foreach ($monitoringEmails as $bccer) {
                $mail->AddBCC($bccer);
            }
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "monitoring - demande download facture #" . $oid;
            $mail->MsgHTML($content_body);
            $mail->Send();
        }
    }
}
if (isset($_GET["action"]) && $_GET["action"] == "dl_devis") {

    if ($_POST["id_devis"]) {

        $mail->ClearAllRecipients();

        $did = $_POST["id_devis"];
        $now = date("d-m-y");
        $devisinfo = getDevis($did);
        if (!empty($devisinfo[0]) && $devisinfo[0]["id_customer"] == $_SESSION["user"]["id_customer"]) {
            $smarty->assign("devisinfo", $devisinfo[0]);
            $content_body = $smarty->fetch('front_devis.tpl');

            $pdf->AddPage('P', 'A4');
            $pdf->writeHTML($content_body, true, false, true, false, '');
            $pdf->lastPage();
            $pdf->Output("AV_DE_" . $did . "_" . $now . ".pdf", 'D');
            foreach ($monitoringEmails as $bccer) {
                $mail->AddBCC($bccer);
            }
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "monitoring - demande download devis #" . $did;
            $mail->MsgHTML($content_body);
            $mail->Send();
        }
    }
}
if (isset($_GET["action"]) && $_GET["action"] == "conf_mail") {

    $r = validAccount($_GET["email"], $_GET["secure_key"]);
    if ($r) {
        $page = "generic_page";
        $ok_msg = array("txt" => "Votre compte a bien été activé");
    }
}

/* Fichier pdf */

if (isset($_GET["newsletter"]) && isset($_POST["email"])) {

    $newsletter = array(
        "date_add" => date("Y-m-d H:i:s"),
        "email" => $_POST["email"]
    );
    $r = $db->insert("av_newsletter", $newsletter);
    if ($r) {
        $page = "generic_page";
        $ok_msg = array("txt" => "Bravo vous êtes bien enregistré au programme de Newsletter d'allovitres.com . Vous recevrez très prochainement toutes nos promotions et nos bons plans");
    }
}


/* session */
$smarty->assign('user', null);
if (@$_SESSION["is_logged"]) {
    $smarty->assign('user', $_SESSION["user"]);
}

//secured page
if (in_array($page, $secured_pages) && !isset($_SESSION["is_logged"])) {
    $page = "identification";
}

$mydevis = getUserDevis($_SESSION["user"]["id_customer"]);

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
$smarty->assign('error', $ko_msg);
$smarty->assign('okmsg', $ok_msg);
$smarty->assign('config', $config);
$smarty->assign('meta', $meta);
$smarty->assign('promos', $promos);
$smarty->assign('promoshome', $promoshome);
$smarty->assign('searchs', $search);
$smarty->assign('search_result', $search_result);

$smarty->display('index.tpl');
?>
<?
if ($_SESSION["user"]["email"] == "stephane.alamichel@gmail.com" || $_SESSION["user"]["email"] == "alamichel.s@free.fr") {
    ?>

    <h1>Session</h1>
    <?= @print_r($_SESSION) ?>
    <h1>Cart item</h1>
    <?= @ print_r($cartItems); ?>
    <h1>Orders</h1>
    <?= @print_r($orders); ?>
    <h1>Product</h1>
    <?= @print_r($product); ?>
    <h1>meta</h1>
    <?= @print_r($meta); ?>
    <h1>devisinfo</h1>
    <?= @print_r($devisinfo); ?>
    <h1>Post</h1>
    <?= @print_r($_POST); ?>

    <?
}
?>