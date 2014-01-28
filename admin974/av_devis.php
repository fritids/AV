<?php
require_once ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");
include ("../functions/devis.php");

require('../libs/Smarty.class.php');
require('../classes/class.phpmailer.php');
require('../classes/tcpdf.php');

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$smarty = new Smarty;
$smarty->setTemplateDir(array('../templates', '../templates/mails', '../templates/pdf/front'));
$smarty->setCompileDir("../templates_c");

//Create a new PHPMailer instance
$mail = new PHPMailer();
//Set who the message is to be sent from
$mail->SetFrom($confmail["from"]);
$mail->CharSet = 'UTF-8';


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


//---------
$pAll = getAllProductInfo();
$a = $db->get("av_attributes");

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
            "secure_key" => genSecureKey(),
            "date_add" => date("Y-m-d"),
            "date_upd" => date("Y-m-d"));
        $cid = createNewAccount($customer_info);

        if ($cid > 0) {
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
        } else {
            echo '<div class="alert alert-danger text-center">Ce compte existe déjà merci de rechercher le contact</div>';
        }
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
            "date_upd" => date("Y-m-d")
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
            "date_upd" => date("Y-m-d")
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
                $p_product_custom_names = $_POST["product_custom_name"];
                $p_product_custom_combination = @$_POST["custom"];
                $shape_impact_coef = 1;
                $attributes_amount = 0;
                $devis_product_attributes = array();
                $devis_custom_attributes = array();

                if (isset($p_product_custom_combination[$product][$k])) {
                    $mapCustomAttribute = mapCustomAttribute($p_product_custom_combination[$product][$k]);
                    foreach ($mapCustomAttribute as $custom_item) {
                        if (is_array($custom_item)) {
                            foreach ($custom_item as $o => $sub_items) {
                                if (is_array($sub_items)) {
                                    foreach ($sub_items as $l => $sub_item) {
                                        if ($sub_item["price_impact_percentage"] > 0) {
                                            $shape_impact_coef = $sub_item["price_impact_percentage"];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                // la quantité de la ligne est présent
                if (!empty($p_qte[$k])) {
                    $p = getProductInfos($product);

                    $p_unit_price = $p["price"] * $shape_impact_coef;
                    $p_unit_weight = $p["weight"];
                    $p_area = $p_width[$k] * $p_height[$k] / 1000000;
                    $p_coef = 1;

                    if ($p_area < $p["min_area_invoiced"]) {
                        $p_area = $p["min_area_invoiced"];
                    }
                    if ($p_area > $p["max_area_invoiced"]) {
                        $p_coef = 1.5;
                    }

                    $product_amount = $p_unit_price * $p_coef * $p_qte[$k] * round($p_area, 2);

                    $devis_detail = array(
                        "id_devis" => $did,
                        "id_product" => $product,
                        "product_name" => ($p_product_custom_names[$k] != "" ? $p_product_custom_names[$k] . "-" : "") . $p["name"],
                        "product_quantity" => $p_qte[$k],
                        "product_price" => $p_unit_price,
                        "product_width" => $p_width[$k],
                        "product_height" => $p_height[$k],
                        "product_weight" => $p_area * $p_unit_weight,
                    );

                    $ddid = $db->insert("av_devis_detail", $devis_detail);

                    foreach ($_POST["product_attribut"][$k] as $attributes) {
                        $arr = explode("|", $attributes);
                        if ($arr[0] == $product) {

                            $arr[2] *= $shape_impact_coef;

                            $devis_product_attributes = array(
                                "id_devis" => $did,
                                "id_devis_detail" => $ddid,
                                "id_product" => $arr[0],
                                "id_attribute" => $arr[1],
                                "prixttc" => $arr[2],
                                "name" => $arr[3]);
                            $db->insert("av_devis_product_attributes", $devis_product_attributes);
                            $attributes_amount += $arr[2] * round($p_area, 2) * $p_coef * $p_qte[$k];
                        }
                    }

                    if (isset($p_product_custom_combination[$product][$k])) {
                        foreach ($p_product_custom_combination[$product][$k] as $d => $main_attribute) {
                            if (is_array($main_attribute)) {
                                $devis_custom_attributes["id_attribute"] = $d;
                                foreach ($main_attribute as $l => $sub_attribute) {
                                    if (is_array($sub_attribute)) {
                                        $devis_custom_attributes["id_attributes_items"] = $l;

                                        foreach ($sub_attribute as $m => $item_values) {
                                            if (is_array($item_values)) {
                                                foreach ($item_values as $n => $item_value) {
                                                    $devis_custom_attributes["id_attributes_items_values"] = $n;
                                                    $devis_custom_attributes["id_devis"] = $did;
                                                    $devis_custom_attributes["id_devis_detail"] = $ddid;
                                                    $devis_custom_attributes["id_product"] = $product;
                                                    $devis_custom_attributes["custom_value"] = $item_value;

                                                    $db->insert("av_devis_product_custom", $devis_custom_attributes);
                                                    $is_product_custom = 1;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        // post update sur les details
                        $r = $db->where("id_devis_detail", $ddid)
                                ->update("av_devis_detail", array("is_product_custom" => $is_product_custom));
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
    // produit exotique
    if (isset($_POST["exo_product_name"]) && !empty($_POST["exo_product_name"]))
        foreach ($_POST["exo_product_name"] as $k => $product) {
            if (!empty($product)) {
                $p_qte = $_POST["exo_product_quantity"];
                $p_unit_price = $_POST["exo_product_unit_price"];
                $p_unit_weight = $_POST["exo_product_unit_weight"];

                $shipping_amount = 0;
                $product_amount = $p_unit_price[$k] / $config["vat_rate"] * $p_qte[$k];

                $total_price_tax_incl = $shipping_amount + $product_amount;
                $total_paid += $total_price_tax_incl;

                $devis_detail = array(
                    "id_devis" => $did,
                    "id_product" => 0,
                    "product_name" => $product,
                    "product_quantity" => $p_qte[$k],
                    "product_price" => $p_unit_price[$k] / $config["vat_rate"],
                    "product_weight" => $p_unit_weight[$k],
                    "total_price_tax_incl" => $total_price_tax_incl,
                    "total_price_tax_excl" => $total_price_tax_incl
                );
                $db->insert("av_devis_detail", $devis_detail);
            }
        }
    if (!empty($_POST["fixe_product_id"])) {
        foreach ($_POST["fixe_product_id"] as $k => $product) {
            if (!empty($product)) {
                $p_qte = $_POST["fixe_product_quantity"];
                $p_product_custom_names = $_POST["product_custom_name"];

                // la quantité de la ligne est présent
                if (!empty($p_qte[$k])) {
                    $p = getProductInfos($product);

                    $p_unit_price = $p["price"];

                    $product_amount = $p["price"] * $p_qte[$k];

                    $devis_detail = array(
                        "id_devis" => $did,
                        "id_product" => $product,
                        "product_name" => ($p_product_custom_names[$k] != "" ? $p_product_custom_names[$k] . "-" : "") . $p["name"],
                        "product_quantity" => $p_qte[$k],
                        "product_price" => $p_unit_price
                    );
                    $ddid = $db->insert("av_devis_detail", $devis_detail);

                    $total_price_tax_incl = $product_amount;
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
    $devis_summary = array("total_paid" => $total_paid);
    $r = $db->where("id_devis", $did)
            ->update("av_devis", $devis_summary);


    if ($did) {

        $customer_info = getCustomerDetail($cid);

        $now = date("Y-M-d");

        $devisinfo = getDevis($did);
        $smarty->assign("devisinfo", $devisinfo[0]);
        $content_body = $smarty->fetch('front_devis.tpl');

        $pdf->AddPage('P', 'A4');
        $pdf->writeHTML($content_body, true, false, true, false, '');
        $pdf->lastPage();

        $path = "../tmp";

        $filename = $path . "/" . "AV_DE_" . $did . "_" . $now . ".pdf";
        $pdf->Output($filename, 'F');



        $mail->AddAddress($customer_info["email"]);

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

        $mail->Subject = "Allovitres - vous avez reçu un devis";

        foreach ($monitoringEmails as $bccer) {
            $mail->AddbCC($bccer);
        }

        $mail_body = $smarty->fetch('notif_new_devis.tpl');
        $mail->AddAttachment($path . "/" . $filename);
        $mail->MsgHTML($mail_body);

        if ($mail->Send()) {
            unlink($path . "/" . $filename);
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
        $table_exo = $('#tab_exotique2');
        $table_fixe = $('#tab_fixe2');

        var $existRow = $table.find('tr').eq(1);
        /* bind to existing elements on page load*/
        bindAutoComplete($existRow);
    });

    function addRow() {
        var $row = $table.find('#template2').clone();
        var $input = $row.find('input').val("");
        $row.attr('id', (++c));

        $row.find('.unit_price').text("");
        $row.find('.unit_weight').text("");
        $row.find('.poids').text("");
        $row.find('.prixttc').val("");
        $row.find('.product_price').text("");
        $row.find('.product_area_invoice').text("");
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
        $row.find('.product_custom_name').val();

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
        $row.find(".attributes8").chained($row.find(".product"));

        $row.find(".attributes1").attr('name', $row.find(".attributes1").attr('name') + "[" + c + "][]");
        $row.find(".attributes2").attr('name', $row.find(".attributes2").attr('name') + "[" + c + "][]");
        $row.find(".attributes3").attr('name', $row.find(".attributes3").attr('name') + "[" + c + "][]");
        $row.find(".attributes4").attr('name', $row.find(".attributes4").attr('name') + "[" + c + "][]");
        $row.find(".attributes5").attr('name', $row.find(".attributes5").attr('name') + "[" + c + "][]");
        $row.find(".attributes6").attr('name', $row.find(".attributes6").attr('name') + "[" + c + "][]");
        $row.find(".attributes7").attr('name', $row.find(".attributes7").attr('name') + "[" + c + "][]");
        $row.find(".attributes8").attr('name', $row.find(".attributes8").attr('name') + "[" + c + "][]");

        $row.find(".product").attr('name', $row.find(".product").attr('name') + "[" + c + "]");

        $row.find(".product_width").attr('name', $row.find(".product_width").attr('name') + "[" + c + "]");
        $row.find(".product_height").attr('name', $row.find(".product_height").attr('name') + "[" + c + "]");
        $row.find(".product_quantity").attr('name', $row.find(".product_quantity").attr('name') + "[" + c + "]");

        $row.find(".product_custom_name").attr('name', $row.find(".product_custom_name").attr('name') + "[" + c + "]");


        $row.show();

        //.find(".product_width").val();

        $input.focus();

    }

    function addExoRow() {
        var $row = $table_exo.find('#template_exo').clone();
        var $input = $row.find('input').val("");
        $row.attr('id', 'id' + (++c));

        $row.find('.unit_price').text("");
        $row.find('.unit_weight').text("");
        $row.find('.poids').text("");
        $row.find('.prixttc').val("");
        $row.find('.unit_price').removeAttr('readonly');
        $row.find('.unit_weight').removeAttr('readonly');


        $("#tab_exotique").append($row);

        bindAutoComplete($row);

        $input.focus();
    }
    function addFixeRow() {
        var $row = $table_fixe.find('#template_fixe').clone();
        var $input = $row.find('input').val("");
        $row.attr('id', 'id' + (++c));

        $row.find('.unit_price').text("");
        $row.find('.prixttc').val("");
        $row.find('.product_custom_name').val("");

        $row.find(".product").attr('name', $row.find(".product").attr('name') + "[" + c + "]");
        $row.find(".product_quantity").attr('name', $row.find(".product_quantity").attr('name') + "[" + c + "]");
        $row.find(".product_custom_name").attr('name', $row.find(".product_custom_name").attr('name') + "[" + c + "]");

        $("#tab_fixe").append($row);

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
        var p_shape_impact_coef = mytr.find(".shape_impact_coef").val();


        mytr.find(".unit_price").val(parseFloat(p_prod_price)
                + parseFloat(p_attr1_price)
                + parseFloat(p_attr2_price)
                + parseFloat(p_attr3_price)
                + parseFloat(p_attr4_price)
                + parseFloat(p_attr5_price)
                + parseFloat(p_attr6_price)
                + parseFloat(p_attr7_price)
                );

        if (parseFloat(p_shape_impact_coef) > 1) {
            mytr.find(".unit_price").val(mytr.find(".unit_price").val() * parseFloat(p_shape_impact_coef));
        }

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

        area_calc = (p_width * p_height) / 1000000;
        area_invoiced = area_calc;
        coef = 1;

        if (area_calc < p_min_area_invoiced) {
            area_invoiced = parseFloat(p_min_area_invoiced);
        }
        if (area_calc > p_max_area_invoiced) {
            coef = 1.5;
        }

        if (p_qte !== "") {
            if (typeof p_width === "undefined" && typeof p_height === "undefined") {
                pprice = parseFloat(p_uprice) * parseInt(p_qte);
                mytr.find(".prixttc").val(pprice.toFixed(2));
                mytr.find(".poids").text(parseFloat(p_uweight) * parseInt(p_qte));

            } else {
                pprice = area_invoiced.toFixed(2) * coef * p_uprice * p_qte;

                mytr.find(".prixttc").val(pprice.toFixed(2));
                mytr.find(".poids").text(area_invoiced.toFixed(2) * p_uweight * p_qte);
                mytr.find(".product_area_invoice").text(area_invoiced.toFixed(2));
                mytr.find(".product_area").text(area_calc.toFixed(2));
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
            pid = $(this).val();
            var mytr = $(this).closest('tr');
            //console.log("pid " + pid);
            jQuery.ajax({
                url: "functions/ajax_devis.php",
                type: "POST",
                dataType: "json",
                async: false,
                data: {
                    id_product: pid
                },
                success: function(result) {
                    //console.log(result);
                    mytr.find(".forme").text("");
                    mytr.find('.min_area_invoiced').val(result.min_area_invoiced);
                    mytr.find(".max_area_invoiced").val(result.max_area_invoiced);
                    mytr.find(".min_width").val(result.min_width);
                    mytr.find(".max_width").val(result.max_width);
                    mytr.find(".min_height").val(result.min_height);
                    mytr.find(".max_height").val(result.max_height);
                    mytr.find(".min_width").text(result.min_width);
                    mytr.find(".max_width").text(result.max_width);
                    mytr.find(".min_height").text(result.min_height);
                    mytr.find(".max_height").text(result.max_height);
                    mytr.find(".unit_price").val(result.price);

                    if (result.info.specific_combinations) {
                        combinations = result.info.specific_combinations;

                        $.each(combinations, function(k, data) {
                            if (data.type == 1) {

                                mytr.find(".forme").append('<b>' + data.name + "</b>:");

                                var s = $("<select id=\"" + k + "\" name=\"main_item\" class=\"main_item\" />");
                                $("<option />", {value: "", text: "--"}).appendTo(s);
                                for (var val in data.items) {
                                    //console.log(val);
                                    $("<option />", {value: data.items[val].id_attributes_items, text: data.items[val].name}).appendTo(s);
                                }
                                mytr.find(".forme").append(s);
                                mytr.find(".forme").append('<div id="i_' + k + '"><div class="list_' + k + '_i_' + k + '"></div></div>');

                                /*if (data.is_duplicable == 1) {
                                 mytr.find(".forme").append('test');
                                 }*/
                            }
                        });
                    }

                    $('.main_item').change(function() {
                        //console.log($(this).val());  
                        var mytr = $(this).closest('tr');

                        var min_width = result.min_width;
                        var max_width = result.max_width;
                        var min_height = result.min_height;
                        var max_height = result.max_height;

                        //id_block = $(this).parent().attr("id");
                        id_block = $(this).attr("id");
                        id_sub_item = $(this).val();
                        id_item = $(this).attr("id");


                        if ($(this).val() === "") {
                            mytr.find('.product_height').removeAttr('readonly');
                            mytr.find('.product_width').removeAttr('readonly');
                            mytr.find('.list_' + id_item + '_i_' + id_block).text("");
                            if (id_item == 6) {
                                mytr.find(".custom_img").attr("src", "");
                            }
                        }



                        // recupe info de la forme
                        $.ajax({
                            url: "../functions/ajax_declinaison.php",
                            type: "POST",
                            dataType: "json",
                            async: false,
                            data: {
                                //id: id_block,
                                id_product: pid,
                                //ids: $('.attribute').serializeArray(),
                                //subItems: $('.main_item').serializeArray(),
                                main_item_ids: $('.main_item').serializeArray(),
                            },
                            success: function(result2) {
                                option_price = result2.price_option;
                                shape_impact_coef = result2.impact_coef;
                                mytr.find(".shape_impact_coef").val(shape_impact_coef);
                                //console.log(result2);                                
                            }
                        });

                        $.ajax({
                            url: "../functions/ajax_custom_sub_items.php",
                            type: "POST",
                            dataType: "json",
                            async: false,
                            data: {
                                id_item: id_item,
                                id_sub_item: id_sub_item,
                                id_product: pid
                            },
                            success: function(result) {
                                //console.log(result);                    
                                //console.log('#list_' + id_item + '_' + id_block);
                                mytr.find('.list_' + id_item + '_i_' + id_block).text("");
                                mytr.find('.product_height').attr('readonly', true);
                                mytr.find('.product_width').attr('readonly', true);

                                tr_id = mytr.attr("id");

                                if (result.picture !== '') {
                                    mytr.find(".custom_img").attr("src", "../img/f/" + result.picture);
                                    console.log(result.picture);
                                }

                                //calculateprice();
                                $.each(result.item_values, function(key, value) {

                                    //console.log(value);
                                    /*$("#list_Formes").append($('<input />').attr({'type':'text', 'id':'url' + key}));*/
                                    $item_input = $('<input />').attr({
                                        type: 'text',
                                        id: 'view_' + key,
                                        class: 'range_text',
                                        side: value.name,
                                        name: 'custom[' + pid + '][' + tr_id + '][' + result.id_attribute + '][' + result.id_attributes_items + '][' + tr_id + '][' + key + ']',
                                        myid: key,
                                        value: value.min_width
                                    });
                                    if (value.is_width === 1) {
                                        $item_input.addClass("primary_width");
                                    }
                                    if (value.is_height === 1) {
                                        $item_input.addClass("primary_height");
                                    }

                                    if (value.is_width === 1 && value.max_width > max_width) {
                                        item_max_width = max_width;
                                    } else if (value.is_height === 1 && value.max_width > max_height) {
                                        item_max_width = max_height;
                                    } else {
                                        item_max_width = value.max_width;
                                    }

                                    mytr.find('.list_' + id_item + '_i_' + id_block).append("<div>");
                                    mytr.find('.list_' + id_item + '_i_' + id_block).append(value.name + ": ");
                                    mytr.find('.list_' + id_item + '_i_' + id_block).append($item_input);
                                    mytr.find('.list_' + id_item + '_i_' + id_block).append("</div>");

                                });

                            }
                        });
                        $(".primary_width").change(function() {
                            var mytr = $(this).closest('tr');

                            A = mytr.find("input[side*='A']").val();
                            B = mytr.find("input[side*='B']").val();
                            C = mytr.find("input[side*='C']").val();

                            if (id_sub_item == 5) {
                                mytr.find('.product_width').val(parseInt(A) * 2);
                            } else if (id_sub_item == 6) {
                                mytr.find('.product_width').val(parseInt(A) * 2);
                            } else if (id_sub_item == 8) {
                                mytr.find('.product_width').val(mytr.find('product_height').val());
                            } else {
                                mytr.find('.product_width').val($(this).val());
                            }

                            updUnitPrice(mytr);
                        });
                        $(".primary_height").change(function() {
                            var mytr = $(this).closest('tr');

                            A = mytr.find("input[side*='A']").val();
                            B = mytr.find("input[side*='B']").val();
                            C = mytr.find("input[side*='C']").val();

                            if (id_sub_item == 2) {
                                if (parseInt(A) > parseInt(B)) {
                                    mytr.find('.product_height').val(parseInt(A));
                                } else {
                                    mytr.find('.product_height').val(parseInt(B));
                                }
                            } else if (id_sub_item == 4) {
                                mytr.find('.product_height').val(parseInt(A) + parseInt(C));
                            } else if (id_sub_item == 5) {
                                mytr.find('.product_height').val(parseInt(A) * 2);
                            } else if (id_sub_item == 6) {
                                mytr.find('.product_height').val(parseInt(A) * 2);
                            } else if (id_sub_item == 8) {
                                mytr.find('.product_height').val(parseInt(B));
                                mytr.find('.product_width').val(parseInt(B));
                            } else {
                                mytr.find('.product_height').val($(this).val());
                            }

                            updUnitPrice(mytr);
                        });
                        //$('.attribute').change();
                        mytr.find(".primary_width").change();
                        mytr.find(".primary_height").change();

                    });
                }
            });
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
                        //console.log(result.productPrice + " " + result.priceAttribut);
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
        $(".attributes8").change(function() {
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
                        mytr.find('.attr8_price').val(result.priceAttribut);
                        updUnitPrice(mytr);

                    }
                });
            }
        });

        $(".unit_price").change(function() {
            var mytr = $(this).closest('tr');
            updatePrice(mytr);
        })
        $('.delline').click(function() {
            $(this).closest('tr').remove();

        });
    }

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
    <!---<div class="row">          
        <ul class=" col-xs-6 alert alert-info">
            <li class="list-unstyled">Mise en place des majorations </li>
        </ul>
    </div>--->
    <div class="page-header">
        <h1>Création de devis</h1>        
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Recherche un client</h3>
                </div>  
            </div>
            <form class="form form-horizontal" role="form" method="post">
                <input type="text" id ="ajax_customer" class="form-control"/>
                <input type="hidden" name ="id_customer"  id ="id_customer"/>
                <input type="submit" class="btn btn-primary btn-block" value="Rechercher">
            </form>
        </div>
        <form class="form form-horizontal" method="post" role="form">
            <input type="hidden"  value="<?= $cid ?>" name="id_customer">            
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Contact</h3>
                    </div>  
                </div>                
                <div class="col-xs-12">
                    <div class="form-group">
                        <input type="text" required="required" name="firstname" value="<?= @$customer_info["firstname"] ?>" class="form-control" placeholder="Nom">
                        <input type="text" required="required" name="lastname" value="<?= @$customer_info["lastname"] ?>" class="form-control" placeholder="Prénom">
                        <input type="text" required="required" name="email" value="<?= @$customer_info["email"] ?>" class="form-control" placeholder="E-mail">                    
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Adresse de livraison</h3>
                    </div>  
                </div>

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
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Adresse de facturation</h3>
                    </div>  
                </div>
                <div class="form-inline">
                    <input type="hidden" value="<?= @$customer_invoice["id_address"] ?>" name="invoice_id" >
                    <div class="col-xs-6">
                        <input type="text" name="invoice_phone" value="<?= @$customer_invoice["phone"] ?>" class="col-xs-6 form-control" placeholder="Téléphone">
                    </div>
                    <div class="col-xs-6">
                        <input type="text" name="invoice_phone_mobile" value="<?= @$customer_invoice["phone_mobile"] ?>" class="col-xs-6 form-control" placeholder="Mobile">                                        
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
    <hr>
    <?
    if (!empty($cid)) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <h2>Contenu du devis</h2>

                <table id="tab_devis2" style="display: none ">
                    <tr id="template2" >

                    <input type="hidden" name="" value="0" class="min_area_invoiced" size="2" />
                    <input type="hidden" name="" value="0" class="max_area_invoiced" size="2" />                    
                    <input type="hidden" name="" value="0" class="shape_impact_coef" value="1" size="2" />                    
                    <input type="hidden" name="" value="0" class="min_width" size="2" />
                    <input type="hidden" name="" value="0" class="max_width" size="2" />
                    <input type="hidden" name="" value="0" class="prod_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr1_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr2_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr3_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr4_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr5_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr6_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr7_price" size="2" />
                    <input type="hidden" name="" value="0" class="attr8_price" size="2" />
                    <td>
                        <select name="product_id" class="product" id="product">
                            <?
                            foreach ($pAll as $product) {
                                if ($product["min_width"] > 0 && $product["min_height"] > 0) {
                                    ?>
                                    <option value="<?= $product["id_product"] ?>"><?= $product["name"] ?></option>
                                    <?
                                }
                            }
                            ?>
                        </select><br>
                        Préfixe : <input type="text" name="product_custom_name" class="product_custom_name" maxlength="10" ><br>
                        <img src="" width="95" class="custom_img" style="float: right;"/>
                        <span id="forme" class="forme"></span>
                    </td>

                    <td>
                        <?
                        $a = $db->get("av_attributes");
                        foreach ($a as $attribute) {
                            ?>
                            <select name = "product_attribut" class = "attributes<?= $attribute["id_attribute"] ?>" id="attributes<?= $attribute["id_attribute"] ?>">
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
                            </select>
                            <br>
                            <?
                        }
                        ?>
                    </td>                    
                    <td>
                        <input type="text" name="product_width"  class="product_width" size="5" /> <br>
                        [ <span class="min_width"></span> - <span class="max_width"></span> ]
                    </td>
                    <td>
                        <input type="text" name="product_height" class="product_height" size="5" /><br>
                        [ <span class="min_height"></span> - <span class="max_height"></span> ]
                    </td>                                    
                    <td><input type="text" name="product_unit_price" class="unit_price" size="5" readonly="readonly" /> €/m²</td>
                    <td><input type="text" name="product_quantity" class="product_quantity" size="2" /></td>                                                        
                    <td>
                        Facturé : <span class="product_area_invoice"></span> m² <br>
                        Calculé : <span class="product_area"></span> m² <br>
                    </td>                                    
                    <td>
                        <input type="text" class="prixttc" size="5" readonly="readonly"> €                        
                    </td>
                    <td>
                        <button type="button" class="delline"><span class="glyphicon glyphicon-remove"></span></button>
                    </td>
                    </tr>
                </table>

                <table id="tab_exotique2" style="display: none">
                    <tr id="template_exo">
                        <td><input type="text" name="exo_product_name[]" class="product_name col-xs-12"></td>
                        <td><input type="text" name="exo_product_unit_price[]" class="unit_price" size="5" /> €</td>
                        <td><input type="text" name="exo_product_unit_weight[]" class="unit_weight" size="5" required=""/> Kg</td>
                        <td><input type="text" name="exo_product_quantity[]" class="product_quantity" size="2" /></td>                    
                        <td><span class="poids"></span> Kg</td>  
                        <td>
                            <input type="text" class="prixttc" size="5" readonly="readonly"> €                            
                        </td>
                        <td>
                            <button type="button" class="delline"><span class="glyphicon glyphicon-remove"></span></button>
                        </td>
                    </tr>
                </table>
                <table id="tab_fixe2" style="display: none">
                    <tr id="template_fixe">
                        <td>
                            <select name="fixe_product_id" class="product" id="product">
                                <option>--</option>

                                <?
                                foreach ($pAll as $product) {
                                    if ($product["width"] > 0 || $product["height"] > 0 || $product["id_category"] == 19) {
                                        ?>
                                        <option value="<?= $product["id_product"] ?>"><?= $product["name"] ?></option>
                                        <?
                                    }
                                }
                                ?>                                        
                            </select><br>
                            Préfix : <input type="text" name="product_custom_name" class="product_custom_name" maxlength="10">
                        </td>
                        <td><input type="text" name="fixe_product_unit_price" class="unit_price" size="5" readonly="readonly" /></td>
                        <td><input type="text" name="fixe_product_quantity" class="product_quantity" size="2" /></td>                    
                        <td>
                            <input type="text" class="prixttc" size="5" readonly="readonly"> €                            
                        </td>
                        <td>
                            <button type="button" class="delline"><span class="glyphicon glyphicon-remove"></span></button>
                        </td>
                    </tr>
                </table>
                <form action="" method="post">
                    <input type="hidden"  value="<?= $cid ?>" name="id_customer">
                    <input type="hidden" name ="isNewCustomer"  value="<?= $isNewCustomer ?>"/>

                    <ul class="nav nav-pills">
                        <li class="active"><a href="#classique" data-toggle="tab">Classique</a></li>
                        <li><a href="#fixe" data-toggle="tab">Taille fixe</a></li>
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
                                    <th>Quantité</th>  
                                    <th>Surface</th>
                                    <th>Prix TTC</th>
                                    <th>&nbsp;</th>
                                </tr>
                                <tr>
                                    <td><button type="button" id="newlines" onclick="javascript:addRow();"><span class="glyphicon glyphicon-plus"></span></button></td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-pane" id="fixe">
                            <table class="table table-bordered table-condensed col-md-12" id="tab_fixe">
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix Unit.</th>                                    
                                    <th>Quantité</th>  
                                    <th>Prix TTC</th>
                                    <th>&nbsp;</th>
                                </tr>
                                <tr>
                                    <td><button type="button" id="newlines" onclick="javascript:addFixeRow();"><span class="glyphicon glyphicon-plus"></span></button></td>
                                </tr>
                            </table>
                        </div>
                        <div class="tab-pane" id="exotique">
                            <table class="table table-bordered table-condensed col-md-12" id="tab_exotique">
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix Unit. TTC</th>
                                    <th>Poids Unit.</th>
                                    <th>Quantity</th>
                                    <th>Poids</th>                        
                                    <th>Prix TTC</th>
                                    <th>&nbsp;</th>
                                </tr>
                                <tr>
                                    <td>
                                        <button type="button" id="newlines" onclick="javascript:addExoRow();"><span class="glyphicon glyphicon-plus"></span></button>
                                    </td>
                                </tr>

                            </table>
                        </div>                       
                    </div>
                    <div class="col-xs-7 pull-left">
                        <textarea name="devis_comment" cols="40"></textarea>
                    </div>
                    <div class=" col-xs-5 pull-right">    
                        <button type="button" id='calculate_total'>Calculer : prix total</button>
                        Total ttc <b>hors frais de port</b> : <h1><span class="totaldevis">0</span> €</h1> 

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
        });
        $(".totaldevis").text(totaldevis.toFixed(2));
    });
</script>