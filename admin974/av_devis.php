<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");

require('../libs/Smarty.class.php');
require('../classes/class.phpmailer.php');
require('../classes/tcpdf.php');

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$smarty = new Smarty;
$smarty->setTemplateDir(array('../templates', '../templates/mails'));

//Create a new PHPMailer instance
$mail = new PHPMailer();
//Set who the message is to be sent from
$mail->SetFrom($confmail["from"]);
$mail->CharSet = 'UTF-8';

//---------
$pAll = getAllProductInfo();
$a = $db->get("av_attributes");

$customers = $db->get("av_customer");
$cid = "";
$customer_info = array();
$customer_delivery = array();
$customer_invoice = array();

$btn_txt = "Ajouter";
$isNewCustomer = 0;

if (isset($_POST["id_customer"]) && $_POST["id_customer"] != "") {
    $cid = $_POST["id_customer"];
    $btn_txt = "Modifier";

    $customer_info = getCustomerDetail($cid);
    $customer_delivery = getAdresse($cid, 'delivery');
    $customer_invoice = getAdresse($cid, 'invoice');

    if (empty($customer_delivery)) {
        $customer_invoice["alias"] = "delivery";
        $customer_delivery = $customer_invoice;
        createNewAdresse($customer_invoice);
    }
}

/*
  print_r($_POST);
  print_r($customer_info);
  print_r($customer_delivery);
  print_r($customer_invoice);
 */


if (isset($_POST["contact"])) {
    if ($_POST["contact"] == "Ajouter") {
        $customer_info = array(
            "firstname" => $_POST["firstname"],
            "lastname" => $_POST["lastname"],
            "email" => $_POST["email"],
            "passwd" => md5(_COOKIE_KEY_ . $_POST["firstname"]),
            "active" => 1,
            "date_add" => date("Y-m-d"),
            "date_upd" => date("Y-m-d"));
        $cid = createNewAccount($customer_info);

        $customer_invoice = array(
            "alias" => 'invoice',
            "id_customer" => $cid,
            "address1" => @$_POST["invoice_address1"],
            "postcode" => @$_POST["invoice_postcode"],
            "city" => @$_POST["invoice_city"],
            "country" => 'France',
            "phone" => $_POST["invoice_phone"],
            "phone_mobile" => $_POST["invoice_phone_mobile"],
            "active" => 1,
            "date_add" => date("Y-m-d"),
            "date_upd" => date("Y-m-d"));

        $customer_delivery = array(
            "alias" => 'delivery',
            "id_customer" => $cid,
            "address1" => @$_POST["delivery_address1"],
            "postcode" => @$_POST["delivery_postcode"],
            "city" => @$_POST["delivery_city"],
            "country" => 'France',
            "phone" => $_POST["delivery_phone"],
            "phone_mobile" => $_POST["delivery_phone_mobile"],
            "active" => 1,
            "date_add" => date("Y-m-d"),
            "date_upd" => date("Y-m-d"));

        createNewAdresse($customer_invoice);
        createNewAdresse($customer_delivery);

        $btn_txt = "Modifier";

        $isNewCustomer = 1;
    }
    if ($_POST["contact"] == "Modifier") {
        $customer_info = array(
            "firstname" => $_POST["firstname"],
            "lastname" => $_POST["lastname"],
            "email" => $_POST["email"]
        );
        $r = $db->where("id_customer", $cid)
                ->update("av_customer", $customer_info);

        $customer_delivery = array(
            "address1" => $_POST["delivery_address1"],
            "address2" => $_POST["delivery_address2"],
            "city" => $_POST["delivery_city"],
            "postcode" => $_POST["delivery_postcode"],
            "phone" => $_POST["delivery_phone"],
            "phone_mobile" => $_POST["delivery_phone_mobile"],
        );

        $r = $db->where("id_address", $_POST["delivery_id"])
                ->where("alias", 'delivery')
                ->update("av_address", $customer_delivery);

        $customer_invoice = array(
            "address1" => $_POST["invoice_address1"],
            "address2" => $_POST["invoice_address2"],
            "city" => $_POST["invoice_city"],
            "postcode" => $_POST["invoice_postcode"],
            "phone" => $_POST["invoice_phone"],
            "phone_mobile" => $_POST["invoice_phone_mobile"],
        );

        $r = $db->where("id_address", $_POST["invoice_id"])
                ->where("alias", 'invoice')
                ->update("av_address", $customer_invoice);
    }
}


if (isset($_POST["devis_save"])) {
    $total_price_tax_incl = 0;
    $total_paid = 0;

    $isNewCustomer = $_POST["isNewCustomer"];

    $devis_summary = array(
        "id_customer" => $cid,
        "id_user" => $_SESSION['user_id'],
        "id_address_delivery" => $customer_delivery["id_address"],
        "id_address_invoice" => $customer_invoice["id_address"],
        "current_state" => 1,
        "invoice_date" => date("Y-m-d H:i:s"),
        "delivery_date" => date("Y-m-d H:i:s"),
        "date_add" => date("Y-m-d H:i:s"),
        "date_upd" => date("Y-m-d H:i:s"),
        "devis_comment" => $_POST["devis_comment"],
    );

    $did = $db->insert("av_devis", $devis_summary);

    //produit standard
    if (!empty($_POST["product_id"])) {
        foreach ($_POST["product_id"] as $k => $product) {
            if (!empty($product)) {
                $p_qte = $_POST["product_quantity"];
                $p_width = $_POST["product_width"];
                $p_height = $_POST["product_height"];

                $attributes_amount = 0;
                $devis_product_attributes = array();

                // la quantité de la ligne est présent
                if (!empty($p_qte[$k])) {
                    $p = getProductInfos($product);

                    $p_unit_price = $p["price"];
                    $p_unit_weight = $p["weight"];

                    $product_amount = $p["price"] * $p_qte[$k] * $p_width[$k] * $p_height[$k] / 1000000;

                    $devis_detail = array(
                        "id_devis" => $did,
                        "id_product" => $product,
                        "product_name" => $p["name"],
                        "product_quantity" => $p_qte[$k],
                        "product_price" => $p_unit_price,
                        "product_width" => $p_width[$k],
                        "product_height" => $p_height[$k],
                        "product_weight" => $p_width[$k] * $p_height[$k] / 1000000 * $p_unit_weight[$k] * $p_qte[$k],
                    );

                    $ddid = $db->insert("av_devis_detail", $devis_detail);

                    foreach ($_POST["product_attribut"] as $k => $attributes) {
                        $arr = explode("|", $attributes);

                        if ($arr[0] == $product) {
                            $devis_product_attributes = array(
                                "id_devis" => $did,
                                "id_devis_detail" => $ddid,
                                "id_product" => $arr[0],
                                "id_attribute" => $arr[1],
                                "prixttc" => $arr[2],
                                "name" => $arr[3]);

                            $db->insert("av_devis_product_attributes", $devis_product_attributes);

                            $attributes_amount += $arr[2];
                        }
                    }

                    $total_price_tax_incl = $product_amount + $attributes_amount;
                    $total_paid += $total_price_tax_incl;


                    $r = $db->where("id_devis_detail", $ddid)
                            ->update("av_devis_detail", array(
                        "total_price_tax_incl" => $total_price_tax_incl,
                        "total_price_tax_excl" => $total_price_tax_incl)
                    );
                }
            }
        }
    }
// prodiot exotique
    foreach ($_POST["exo_product_name"] as $k => $product) {
        if (!empty($product)) {
            $p_qte = $_POST["exo_product_quantity"];
            $p_unit_price = $_POST["exo_product_unit_price"];
            $p_unit_weight = $_POST["exo_product_unit_weight"];

            $shipping_amount = 0;
            $product_amount = $p_unit_price[$k] * $p_qte[$k];

            $total_price_tax_incl = $shipping_amount + $product_amount;
            $total_paid += $total_price_tax_incl;

            $devis_detail = array(
                "id_devis" => $did,
                "id_product" => 0,
                "product_name" => $product,
                "product_quantity" => $p_qte[$k],
                "product_price" => $p_unit_price[$k],
                "product_weight" => $p_unit_weight[$k],
                "total_price_tax_incl" => $total_price_tax_incl,
                "total_price_tax_excl" => $total_price_tax_incl
            );
            $db->insert("av_devis_detail", $devis_detail);
        }
    }

    $devis_summary = array("total_paid" => $total_paid);
    $r = $db->where("id_devis", $did)
            ->update("av_devis", $devis_summary);


    if ($did) {


        $customer_info = getCustomerDetail($cid);

        //$mail->AddAddress($customer_info["email"]);
        $mail->AddAddress("stephane.alamichel@gmail.com");


        //creation de compte
        if ($isNewCustomer == 1) {

            $mail->Subject = $confmail["welcome"];
            $smarty->assign("email", $customer_info["email"]);
            $smarty->assign("mdp", $customer_info["firstname"]);
            $mail_body = $smarty->fetch('notif_new_account.tpl');
            foreach ($monitoringEmails as $bccer) {
                $mail->AddbCC($bccer);
            }
            $mail->MsgHTML($mail_body);
            $mail->Send();
        }

        $mail->Subject = "Allovitres - vous avez reéu un devis";

        foreach ($monitoringEmails as $bccer) {
            $mail->AddbCC($bccer);
        }

        $mail_body = $smarty->fetch('notif_new_devis.tpl');
        $mail->MsgHTML($mail_body);

        if ($mail->Send()) {
            echo '<div class="alert alert-success text-center">Email envoyé et devis ajouté n° : <b>' . $did . '</b> <a href="av_devis_view.php?id_devis=' . $did . '">Consulter</a></div>';
        }
    }
}
?>

<script>
    var $table;

    var c = 0;
    $(function() {
        $table = $('#tab_devis2');
        $table_exo = $('#tab_exotique');

        var $existRow = $table.find('tr').eq(1);
        /* bind to existing elements on page load*/
        bindAutoComplete($existRow);
    });

    function addRow() {
        var $row = $table.find('#template2').clone();
        var $input = $row.find('input').val("");
        $row.attr('id', 'id' + (++c));

        $row.find('.unit_price').text("");
        $row.find('.unit_weight').text("");
        $row.find('.poids').text("");
        $row.find('.prixttc').val("");
        $row.find('.product_price').text("");
        /*$row.find('.unit_price').removeAttr('readonly');
         $row.find('.unit_weight').removeAttr('readonly');
         */
        $row.find('.attr1_price').val(0);
        $row.find('.attr2_price').val(0);
        $row.find('.attr3_price').val(0);
        $row.find('.attr4_price').val(0);
        $row.find('.attr5_price').val(0);
        $row.find('.attr6_price').val(0);
        $row.find('.attr7_price').val(0);
        $row.find('.prod_price').val();

        //$row.addClass('dupe');
        //$row.attr('id', 'duplicate' + $('#tab_devis tr.dupe').length);

        $("#tab_devis").append($row);
        bindAutoComplete($row);

        //console.log($row.find(".product").text());
        //console.log($row.find(".attributes1").text());

        $row.find(".attributes1").chained($row.find(".product"));
        $row.find(".attributes2").chained($row.find(".product"));
        $row.find(".attributes3").chained($row.find(".product"));
        $row.find(".attributes4").chained($row.find(".product"));
        $row.find(".attributes5").chained($row.find(".product"));
        $row.find(".attributes6").chained($row.find(".product"));
        $row.find(".attributes7").chained($row.find(".product"));
        $row.show();

        //.find(".product_width").val();

        $input.focus();

    }

    function addExoRow() {
        var $row = $table_exo.find('tr:last').clone();
        var $input = $row.find('input').val("");
        $row.attr('id', 'id' + (++c));

        $row.find('.unit_price').text("");
        $row.find('.unit_weight').text("");
        $row.find('.poids').text("");
        $row.find('.prixttc').val("");
        $row.find('.unit_price').removeAttr('readonly');
        $row.find('.unit_weight').removeAttr('readonly');


        $table_exo.append($row);

        bindAutoComplete($row);

        $input.focus();
    }

    function updUnitPrice(mytr) {
        var p_prod_price = mytr.find(".prod_price").val();
        var p_attr1_price = mytr.find(".attr1_price").val();
        var p_attr2_price = mytr.find(".attr2_price").val();
        var p_attr3_price = mytr.find(".attr3_price").val();
        var p_attr4_price = mytr.find(".attr4_price").val();
        var p_attr5_price = mytr.find(".attr5_price").val();
        var p_attr6_price = mytr.find(".attr6_price").val();
        var p_attr7_price = mytr.find(".attr7_price").val();


        mytr.find(".unit_price").val(parseFloat(p_prod_price)
                + parseFloat(p_attr1_price)
                + parseFloat(p_attr2_price)
                + parseFloat(p_attr3_price)
                + parseFloat(p_attr4_price)
                + parseFloat(p_attr5_price)
                + parseFloat(p_attr6_price)
                + parseFloat(p_attr7_price)
                );

        updatePrice(mytr);

    }
    function updatePrice(mytr) {
        //mytr.find(".prixttc").text($(this).val());
        var p_width = mytr.find(".product_width").val();
        var p_height = mytr.find(".product_height").val();
        var p_uweight = mytr.find(".unit_weight").val();
        var p_uprice = mytr.find(".unit_price").val();
        var p_qte = mytr.find(".product_quantity").val();


        var p_min_area_invoiced = mytr.find(".min_area_invoiced").val();
        var p_max_area_invoiced = mytr.find(".max_area_invoiced").val();


        area = (p_width * p_height) / 1000000;
        coef = 1;

        if (area < p_min_area_invoiced) {
            area = p_min_area_invoiced;
        }
        if (area > p_max_area_invoiced) {
            coef = 1.5;
        }

        if (p_qte !== "") {
            if (typeof p_width === "undefined" && typeof p_height === "undefined") {
                pprice = parseFloat(p_uprice) * parseInt(p_qte);

                mytr.find(".prixttc").val(pprice.toFixed(2));
                mytr.find(".poids").text(parseFloat(p_uweight) * parseInt(p_qte));

            } else {
                pprice = p_width * p_height / 1000000 * p_uprice * p_qte;

                mytr.find(".prixttc").val(pprice.toFixed(2));
                mytr.find(".poids").text(p_width * p_height / 1000000 * p_uweight * p_qte);
            }
        }

        /*console.log("p_width " + p_width);
         console.log("p_height " + p_height);
         console.log("p_uweight " + p_uweight);
         console.log("p_uprice " + p_uprice);
         console.log("p_qte " + p_qte);*/
        //console.log($(".prixttc").val());

    }

    function bindAutoComplete($row) {
        /* use row as main element to save traversing back up from input*/
        $row.find(".product_name_disable").autocomplete({
            source: 'functions/ajax_devis.php',
            success: function(data) {

            },
            select: function(event, ui) {
                //console.log(ui);

                $(this).closest('tr').find('.unit_price').val(ui.item.price);
                $(this).closest('tr').find(".unit_weight").val(ui.item.weight);
                $(this).closest('tr').find(".product_width").val(ui.item.min_width);
                $(this).closest('tr').find(".product_height").val(ui.item.min_height);
                $(this).closest('tr').find(".min_area_invoiced").val(ui.item.min_area_invoiced);
                $(this).closest('tr').find(".max_area_invoiced").val(ui.item.max_area_invoiced);

                $(this).closest('tr').find('.unit_price').attr('readonly', 'readonly');
                $(this).closest('tr').find('.unit_weight').attr('readonly', 'readonly');

                var mytr = $(this).closest('tr');
                updatePrice(mytr);
            }
        });

        $(".product_width").change(function() {
            var mytr = $(this).closest('tr');
            updatePrice(mytr);
        })

        $(".product_height").change(function() {
            var mytr = $(this).closest('tr');
            updatePrice(mytr);
        })
        $(".product_depth").change(function() {
            var mytr = $(this).closest('tr');
            updatePrice(mytr);
        })
        $(".product_quantity").change(function() {
            var mytr = $(this).closest('tr');
            updatePrice(mytr);
        })
        $(".unit_weight").change(function() {
            var mytr = $(this).closest('tr');
            updatePrice(mytr);
        })


        $(".product").change(function() {
            //console.log($(this).val());
        })

        $(".attributes1").change(function() {
            $attr = $(this).val();
            var mytr = $(this).closest('tr');
            if ($attr != null) {
                jQuery.ajax({
                    url: "../functions/ajax_declinaison2.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        value: $attr
                    },
                    success: function(result) {
                        //console.log(result.productPrice + " " + result.priceAttribut);
                        mytr.find('.prod_price').val(result.productPrice);
                        mytr.find('.attr1_price').val(result.priceAttribut);
                        updUnitPrice(mytr);

                    }
                });
            }
        });
        $(".attributes1").change(function() {
            $attr = $(this).val();
            var mytr = $(this).closest('tr');
            if ($attr != null) {
                jQuery.ajax({
                    url: "../functions/ajax_declinaison2.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        value: $attr
                    },
                    success: function(result) {
                        //console.log(result.productPrice + " " + result.priceAttribut);
                        mytr.find('.prod_price').val(result.productPrice);
                        mytr.find('.attr1_price').val(result.priceAttribut);
                        updUnitPrice(mytr);

                    }
                });
            }
        });
        $(".attributes2").change(function() {
            $attr = $(this).val();
            var mytr = $(this).closest('tr');
            if ($attr != null) {
                jQuery.ajax({
                    url: "../functions/ajax_declinaison2.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        value: $attr
                    },
                    success: function(result) {
                        //console.log(result.productPrice + " " + result.priceAttribut);
                        mytr.find('.prod_price').val(result.productPrice);
                        mytr.find('.attr2_price').val(result.priceAttribut);
                        updUnitPrice(mytr);

                    }
                });
            }
        });
        $(".attributes3").change(function() {
            $attr = $(this).val();
            var mytr = $(this).closest('tr');
            if ($attr != null) {
                jQuery.ajax({
                    url: "../functions/ajax_declinaison2.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        value: $attr
                    },
                    success: function(result) {
                        //console.log(result.productPrice + " " + result.priceAttribut);
                        mytr.find('.prod_price').val(result.productPrice);
                        mytr.find('.attr3_price').val(result.priceAttribut);
                        updUnitPrice(mytr);

                    }
                });
            }
        });
        $(".attributes3").change(function() {
            $attr = $(this).val();
            var mytr = $(this).closest('tr');
            if ($attr != null) {
                jQuery.ajax({
                    url: "../functions/ajax_declinaison2.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        value: $attr
                    },
                    success: function(result) {
                        //console.log(result.productPrice + " " + result.priceAttribut);
                        mytr.find('.prod_price').val(result.productPrice);
                        mytr.find('.attr3_price').val(result.priceAttribut);
                        updUnitPrice(mytr);

                    }
                });
            }
        });
        $(".attributes4").change(function() {
            $attr = $(this).val();
            var mytr = $(this).closest('tr');
            if ($attr != null) {
                jQuery.ajax({
                    url: "../functions/ajax_declinaison2.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        value: $attr
                    },
                    success: function(result) {
                        console.log(result.productPrice + " " + result.priceAttribut);
                        mytr.find('.prod_price').val(result.productPrice);
                        mytr.find('.attr4_price').val(result.priceAttribut);
                        updUnitPrice(mytr);

                    }
                });
            }
        });
        $(".attributes5").change(function() {
            $attr = $(this).val();
            var mytr = $(this).closest('tr');
            if ($attr != null) {
                jQuery.ajax({
                    url: "../functions/ajax_declinaison2.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        value: $attr
                    },
                    success: function(result) {
                        //console.log(result.productPrice + " " + result.priceAttribut);
                        mytr.find('.prod_price').val(result.productPrice);
                        mytr.find('.attr5_price').val(result.priceAttribut);
                        updUnitPrice(mytr);

                    }
                });
            }
        });
        $(".attributes6").change(function() {
            $attr = $(this).val();
            var mytr = $(this).closest('tr');
            if ($attr != null) {
                jQuery.ajax({
                    url: "../functions/ajax_declinaison2.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        value: $attr
                    },
                    success: function(result) {
                        //console.log(result.productPrice + " " + result.priceAttribut);
                        mytr.find('.prod_price').val(result.productPrice);
                        mytr.find('.attr6_price').val(result.priceAttribut);
                        updUnitPrice(mytr);

                    }
                });
            }
        });
        $(".attributes7").change(function() {
            $attr = $(this).val();
            var mytr = $(this).closest('tr');
            if ($attr != null) {
                jQuery.ajax({
                    url: "../functions/ajax_declinaison2.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        value: $attr
                    },
                    success: function(result) {
                        //console.log(result.productPrice + " " + result.priceAttribut);
                        mytr.find('.prod_price').val(result.productPrice);
                        mytr.find('.attr7_price').val(result.priceAttribut);
                        updUnitPrice(mytr);

                    }
                });
            }
        });

        $(".unit_price").change(function() {
            var mytr = $(this).closest('tr');
            updatePrice(mytr);
        })


    }



</script>
<script>
    $(function() {
        $('.delline').click(function() {
            $(this).parent().remove();

        });
    });
</script>

<script>
    $(function() {
        $("#ajax_customer").autocomplete({
            source: 'functions/ajax_customer.php',
            select: function(event, ui) {
                $("#id_customer").val(ui.item.id_customer);
            }
        });
    })
</script>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <h3>Recherche client existant</h3>
            <form class="form-horizontal" role="form" method="post">
                <input type="text" id ="ajax_customer" />
                <input type="hidden" name ="id_customer"  id ="id_customer"/>



                <input type="submit" >
            </form>
        </div>
        <form class="form-horizontal" method="post" role="form">
            <input type="hidden"  value="<?= $cid ?>" name="id_customer">            
            <div class="col-md-3">
                <h3>Contact</h3>
                <div class="col-xs-12">
                    <div class="form-group">
                        <input type="text" name="firstname" value="<?= @$customer_info["firstname"] ?>" class="form-control" placeholder="Nom">
                        <input type="text" name="lastname" value="<?= @$customer_info["lastname"] ?>" class="form-control" placeholder="Prénom">
                        <input type="text" name="email" value="<?= @$customer_info["email"] ?>" class="form-control" placeholder="E-mail">                    
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <h3>Adresse Livraison</h3>
                <div class="form-group">
                    <input type="hidden" value="<?= @$customer_delivery["id_address"] ?>" name="delivery_id" >
                    <div class="col-xs-6">
                        <input type="text" name="delivery_phone" value="<?= @$customer_delivery["phone"] ?>" class="form-control" placeholder="Téléphone">
                    </div>
                    <div class="col-xs-6">
                        <input type="text" name="delivery_phone_mobile" value="<?= @$customer_delivery["phone_mobile"] ?>" class="form-control" placeholder="Mobile">                    
                    </div>
                    <div class="col-xs-6">
                        <input type="text" value="<?= @$customer_delivery["address1"] ?>" name="delivery_address1" class="form-control" placeholder="Adresse 1">
                        <input type="text" value="<?= @$customer_delivery["address2"] ?>" name="delivery_address2" class="form-control" placeholder="Adresse 2">
                    </div>
                    <div class="col-xs-6">
                        <input type="text" value="<?= @$customer_delivery["postcode"] ?>" name="delivery_postcode" class="form-control" placeholder="Code postal">
                    </div>
                    <div class="col-xs-6">
                        <input type="text" value="<?= @$customer_delivery["city"] ?>" name="delivery_city" class="form-control" placeholder="Ville">                

                    </div>
                </div>

            </div>
            <div class="col-md-3">
                <h3>Adresse Facturation</h3>
                <div class="form-group">
                    <input type="hidden" value="<?= @$customer_invoice["id_address"] ?>" name="invoice_id" >
                    <div class="col-xs-6">
                        <input type="text" name="invoice_phone" value="<?= @$customer_invoice["phone"] ?>" class="col-xs-06 form-control" placeholder="Téléphone">
                    </div>
                    <div class="col-xs-6">
                        <input type="text" name="invoice_phone_mobile" value="<?= @$customer_invoice["phone_mobile"] ?>" class="col-xs-06 form-control" placeholder="Mobile">                                        
                    </div>
                    <div class="col-xs-6">
                        <input type="text" value="<?= @$customer_invoice["address1"] ?>" name="invoice_address1" class="form-control" placeholder="Adresse 1">
                        <input type="text" value="<?= @$customer_invoice["address2"] ?>" name="invoice_address2" class="form-control" placeholder="Adresse 2">
                    </div>
                    <div class="col-xs-6">
                        <input type="text" value="<?= @$customer_invoice["postcode"] ?>" name="invoice_postcode" class="form-control" placeholder="Code postal">
                    </div>
                    <div class="col-xs-6">
                        <input type="text" value="<?= @$customer_invoice["city"] ?>" name="invoice_city" class="form-control" placeholder="Ville">                

                    </div>
                </div>
            </div>
            <button type="submit" name="contact" value="<?= $btn_txt ?>"  id="form_submit" class="col-md-offset-3 col-md-9 btn-lg btn-warning" ><?= $btn_txt ?></button>
        </form>
    </div>
    <?
    if (!empty($cid)) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <h2>Produits</h2>


                <table id="tab_devis2" style="display: none ">
                    <tr id="template2" >

                    <input type="hidden" name="min_area_invoiced[]" class="min_area_invoiced" size="2" />
                    <input type="hidden" name="max_area_invoiced[]" class="max_area_invoiced" size="2" />
                    <input type="hidden" name="" value="0" class="prod_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr1_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr2_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr3_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr4_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr5_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr6_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr7_price" size="2" />
                    <td>
                        <select name="product_id[]" class="product" id="product">
                            <?
                            foreach ($pAll as $product) {
                                if ($product["min_width"] > 0 && $product["min_height"] > 0) {
                                    ?>
                                    <option value="<?= $product["id_product"] ?>"><?= $product["name"] ?> <?= $product["min_height"] ?></option>
                                    <?
                                }
                            }
                            ?>
                        </select>
                    </td>

                    <td>
                        <?
                        $a = $db->get("av_attributes");
                        foreach ($a as $attribute) {
                            ?>
                            <select name = "product_attribut[]" class = "attributes<?= $attribute["id_attribute"] ?>" id="attributes<?= $attribute["id_attribute"] ?>">
                                <?
                                foreach ($pAll as $product) {
                                    if (is_array($product["combinations"])) {
                                        foreach ($product["combinations"] as $idc => $combination) {
                                            if ($idc == $attribute["id_attribute"]) {

                                                foreach ($combination["attributes"] as $ida => $attribute) {
                                                    ?>
                                                    <option value = "<?= $product["id_product"] ?>|<?= $ida ?>|<?= $attribute["price"] ?>|<?= $attribute["name"] ?>" class="<?= $product["id_product"] ?>"><?= $attribute["name"] ?></option>
                                                    <?
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>
                            </select><br>
                            <?
                        }
                        ?>

                    </td>
                    <td><input type="text" name="product_width[]"  class="product_width" size="5" /></td>
                    <td><input type="text" name="product_height[]" class="product_height" size="5" /></td>                                    
                    <td><input type="text" name="product_unit_price[]" class="unit_price" size="5" readonly="readonly" /> €/m²</td>
                    <td><input type="text" name="product_quantity[]" class="product_quantity" size="2" /></td>                                                        

                    <td><input type="text" class="prixttc" size="5" readonly="readonly"> €</td>
                    </tr>

                </table>


                <form action="" method="post">
                    <input type="hidden"  value="<?= $cid ?>" name="id_customer">
                    <input type="hidden" name ="isNewCustomer"  value="<?= $isNewCustomer ?>"/>

                    <ul class="nav nav-tabs">
                        <li><a href="#classique" data-toggle="tab">Classique</a></li>
                        <li><a href="#exotique" data-toggle="tab">Exotique</a></li>
                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane active" id="classique">

                            <table class="table table-bordered table-condensed col-md-12" id="tab_devis">
                                <tr>
                                    <th>Produit</th>
                                    <th>Option</th>
                                    <th>Largeur (mm)</th>
                                    <th>Hauteur (mm)</th>
                                    <th>Prix Unit.</th>                                    
                                    <th>Quantity</th>                                    
                                    <th>Prix TTC</th>
                                </tr>
                                <tr>
                                    <td><button type="button" id="newlines" onclick="javascript:addRow();"><span class="glyphicon glyphicon-plus"></span></button></td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-pane" id="exotique">
                            <table class="table table-bordered table-condensed col-md-12" id="tab_exotique">
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix Unit.</th>
                                    <th>Poids Unit.</th>
                                    <th>Quantity</th>
                                    <th>Poids</th>                        
                                    <th>Prix TTC</th>
                                    <th>Actions</th>
                                </tr>
                                <tr id="id0">
                                    <td><input type="text" name="exo_product_name[]" class="product_name col-xs-12"></td>
                                    <td><input type="text" name="exo_product_unit_price[]" class="unit_price" size="5" /> €</td>
                                    <td><input type="text" name="exo_product_unit_weight[]" class="unit_weight" size="5" /> Kg</td>
                                    <td><input type="text" name="exo_product_quantity[]" class="product_quantity" size="2" /></td>                    
                                    <td><span class="poids"></span> Kg</td>  
                                    <td><input type="text" class="prixttc" size="5" readonly="readonly"> €</td>
                                    <td id="btn_action">
                                        <button type="button" id="newlines" onclick="javascript:addExoRow();"><span class="glyphicon glyphicon-plus"></span></button>
                                        <? /* <button type="button" class="delline"><span class="glyphicon glyphicon-remove"></span></button> */ ?>
                                    </td>
                                </tr>
                            </table>
                        </div>                       
                    </div>
                    <div class="pull-left">
                        <textarea name="devis_comment"></textarea>
                    </div>
                    <div class=" col-xs-6 pull-right">    
                        <div class="alert alert-info"> 
                            Les majorations de prix unitaires lorsque la surface < 0.35m² ou > 3.5m² ne sont pas prises en compte dans le montant calculé.
                        </div>
                        <button type="button" id='calculate_total'>Calculer : prix total</button>
                        Total ttc <b>hors frais de port</b> : <h1><span class="totaldevis"></span></h1> €


                        Valider le devis <input type="checkbox" required="required"> 
                        <input type="submit" name ="devis_save"  class="btn-lg btn-warning pull-right">

                    </div>

                </form>
            </div>
        </div>
        <?
    }
    ?>
</div>

<script>
    $("#calculate_total").click(function() {
        var totaldevis = 0;
        $(".prixttc").each(function(index) {

            if ($(this).val().length !== 0) {
                //console.log($(this).val());
                totaldevis = parseFloat(totaldevis) + parseFloat($(this).val());
            }
        })

        $(".totaldevis").text(totaldevis);
    })
</script>