<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);


$customers = $db->get("av_customer");
$cid = "";
$customer_info = array();
$customer_delivery = array();
$customer_invoice = array();

$btn_txt = "Ajouter";

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
            "passwd" => md5(_COOKIE_KEY_.$_POST["firstname"]),
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

    foreach ($_POST["product_name"] as $k => $product) {

        if (!empty($product)) {
            $p_qte = $_POST["product_quantity"];
            /* $p_depth = $_POST["product_depth"];
              $p_width = $_POST["product_width"];
              $p_height = $_POST["product_height"]; */
            $p_unit_price = $_POST["product_unit_price"];
            $p_unit_weight = $_POST["product_unit_weight"];

            $p = getProductInfosByName($product);

            if (empty($p))
                $p = array("id_product" => 0, "name" => $product);

            //$fdp = getDeliveryRatio($p_unit_weight[$k]);

            $shipping_amount = 0;
            $product_amount = $p_unit_price[$k] * $p_qte[$k];

            $total_price_tax_incl = $shipping_amount + $product_amount;
            $total_paid += $total_price_tax_incl;

            $devis_detail = array(
                "id_devis" => $did,
                "id_product" => $p["id_product"],
                "product_name" => $p["name"],
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

    echo '<div class="alert alert-success text-center">Devis ajouté n° : <b>' . $did . '</b> <a href="av_devis_view.php?id_devis='.$did.'">Consulter</a></div>';
}
?>

<script>
    var $table;
    var c = 0;
    $(function() {
        $table = $('#tab_devis');
        var $existRow = $table.find('tr').eq(1);
        /* bind to existing elements on page load*/
        bindAutoComplete($existRow);
    });

    function addRow() {
        var $row = $table.find('tr:last').clone();
        var $input = $row.find('input').val("");
        $row.attr('id', 'id' + (++c));

        $row.find('.unit_price').text("");
        $row.find('.unit_weight').text("");
        $row.find('.poids').text("");
        $row.find('.fdp').text("");
        $row.find('.prixttc').text("");
        $row.find('.product_price').text("");
        $row.find('.unit_price').removeAttr('readonly');
        $row.find('.unit_weight').removeAttr('readonly');


        $table.append($row);
        bindAutoComplete($row);
        $input.focus();

    }

    function updatePrice(mytr) {
        //mytr.find(".prixttc").text($(this).val());
        var p_uweight = mytr.find(".unit_weight").val();
        var p_uprice = mytr.find(".unit_price").val();
        var p_qte = mytr.find(".product_quantity").val();

        /*$.ajax({
         url: "functions/ajax_fdp.php",
         type: "POST",
         dataType: "json",
         async: false,
         data: {
         p_weight: p_uweight,
         },
         success: function(data) {
         mytr.find(".fdp").text(mytr.find(".poids").text() * data);
         //console.log(data);
         },
         error: function(xhr, textStatus, error) {
         console.log(xhr.statusText);
         console.log(textStatus);
         console.log(error);
         }
         });*/

        mytr.find(".prixttc").text(parseFloat(p_uprice) * parseInt(p_qte));
        mytr.find(".poids").text(parseFloat(p_uweight) * parseInt(p_qte));


        console.log("p_uweight " + p_uweight);
        console.log("p_uprice " + p_uprice);
        console.log("p_qte " + p_qte);

    }

    function bindAutoComplete($row) {
        /* use row as main element to save traversing back up from input*/
        $row.find(".product_name_disable").autocomplete({
            source: 'functions/ajax_devis.php',
            select: function(event, ui) {

                $(this).closest('tr').find('.unit_price').val(ui.item.price);
                $(this).closest('tr').find(".unit_weight").val(ui.item.weight);
                $(this).closest('tr').find(".product_width").val(ui.item.min_width);
                $(this).closest('tr').find(".product_height").val(ui.item.min_height);

                $(this).closest('tr').find('.unit_price').attr('readonly', 'readonly');
                $(this).closest('tr').find('.unit_weight').attr('readonly', 'readonly');
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
    <?
    if (!empty($cid)) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <h2>Produits</h2>
                <form action="" method="post">
                    <input type="hidden"  value="<?= $cid ?>" name="id_customer">

                    <table class="table table-bordered table-condensed col-md-12" id="tab_devis">
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
                            <td><input type="text" name="product_name[]" class="product_name col-xs-12"></td>
                            <td><input type="text" name="product_unit_price[]" class="unit_price" size="5" /> €</td>
                            <td><input type="text" name="product_unit_weight[]" class="unit_weight" size="5" /> Kg</td>
                            <td><input type="text" name="product_quantity[]" class="product_quantity" size="2" /></td>                    
                            <td><span class="poids"></span> Kg</td>                            
                            <td><span class="prixttc"></span> €</td>
                            <td id="btn_action">
                                <button type="button" id="newlines" onclick="javascript:addRow()"><span class="glyphicon glyphicon-plus"></span></button>
                                <button type="button" class="delline"><span class="glyphicon glyphicon-remove"></span></button>
                            </td>
                        </tr>
                    </table>
                    <div class="pull-left">
                        <textarea name="devis_comment"></textarea>
                    </div>
                    <div class="pull-right">
                        <input type="submit" name ="devis_save"  class="btn-lg btn-warning">
                    </div>

                </form>
            </div>
        </div>
        <?
    }
    ?>
</div>
