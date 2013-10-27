<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);


$customers = $db->get("av_customer");
$cid = "";
$customer_info = array();
$customer_delivery = array();
$customer_invoice = array();



if (isset($_POST["id_customer"])) {
    $cid = $_POST["id_customer"];

    $customer_info = getCustomerDetail($cid);
    $customer_delivery = getAdresse($cid, 'delivery');
    $customer_invoice = getAdresse($cid, 'invoice');
}

print_r($_POST);
print_r($customer_info);
print_r($customer_delivery);
print_r($customer_invoice);


if (isset($_POST["devis_save"])) {
    $devis_summary = array(
        "id_customer" => $cid,
        "id_address_delivery" => $customer_delivery["id_address"],
        "id_address_invoice" => $customer_invoice["id_address"],
        "current_state" => 1,
        //"total_paid" => $_SESSION["cart_summary"]["total_amount"],
        "invoice_date" => date("Y-m-d h:i:s"),
        "delivery_date" => date("Y-m-d h:i:s"),
        "date_add" => date("Y-m-d h:i:s"),
        "date_upd" => date("Y-m-d h:i:s"),
        "devis_comment" => $_POST["devis_comment"],
    );

    $oid = $db->insert("av_devis", $devis_summary);

    foreach ($_POST["product_name"] as $k => $product) {

        if (!empty($product)) {
            $p_qte = $_POST["product_quantity"];
            $p_depth = $_POST["product_depth"];
            $p_width = $_POST["product_width"];
            $p_height = $_POST["product_height"];


            $p = getProductInfosByName($product);

            $devis_detail = array(
                "id_devis" => $oid,
                "id_product" => $p["id_product"],
                "product_name" => $p["name"],
                "product_quantity" => $p_qte[$k],
                //"product_price" => $item["price"],
                //"product_shipping" => $item["shipping"],
                "product_width" => $p_width[$k],
                "product_height" => $p_height[$k],
                "product_depth" => $p_depth[$k],
                    //"product_weight" => $item["quantity"] * $p["weight"] * $item["surface"],
                    //"total_price_tax_incl" => $item["quantity"] * $item["price"] + $item["shipping"],
                    //"total_price_tax_excl" => $item["quantity"] * $item["price"] + $item["shipping"]
            );

            $db->insert("av_devis_detail", $devis_detail);
        }



        // les options
        /* if (isset($item["options"])) {
          foreach ($item["options"] as $k => $option) {
          $devis_detail["product_attribute_id"] = $option["o_id"];
          $devis_detail["attribute_name"] = $option["o_name"];
          $devis_detail["attribute_quantity"] = $option["o_quantity"];
          $devis_detail["attribute_price"] = $option["o_price"];
          $devis_detail["attribute_shipping"] = $option["o_shipping"];
          $devis_detail["total_price_tax_incl"] += $option["o_quantity"] * $option["o_price"] + $option["o_shipping"];
          $devis_detail["total_price_tax_excl"] += $option["o_quantity"] * $option["o_price"] + $option["o_shipping"];

          $db->insert("av_devis_detail", $devis_detail);
          }
          } else { // pas d'option
          $db->insert("av_devis_detail", $devis_detail);
          } */
    }
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
        $table.append($row);
        bindAutoComplete($row);
        $input.focus();

    }

    function updatePrice(mytr) {
        //mytr.find(".prixttc").text($(this).val());
        var p_width = mytr.find(".product_width").val();
        var p_height = mytr.find(".product_height").val();
        var p_depth = mytr.find(".product_depth").val();
        var p_uweight = mytr.find(".unit_weight").val();
        var p_uprice = mytr.find(".unit_price").val();
        var p_qte = mytr.find(".product_quantity").val();

        
        mytr.find(".poids").text(p_width * p_height / 10000 * p_uweight * p_qte);
        mytr.find(".product_price").text(p_width * p_height / 10000 * p_uprice * p_qte);

        $.ajax({
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
        });

        mytr.find(".prixttc").text(parseFloat(mytr.find(".product_price").text()) + parseFloat(mytr.find(".fdp").text()));


        console.log("p_width " + p_width);
        console.log("p_height " + p_height);
        console.log("p_depth " + p_depth);
        console.log("p_uweight " + p_uweight);
        console.log("p_uprice " + p_uprice);
        console.log("p_qte " + p_qte);

    }

    function bindAutoComplete($row) {
        /* use row as main element to save traversing back up from input*/
        $row.find(".product_name").autocomplete({
            source: 'functions/ajax_devis.php',
            select: function(event, ui) {

                $(this).closest('tr').find('.unit_price').val(ui.item.price);
                $(this).closest('tr').find(".unit_weight").val(ui.item.weight);
                $(this).closest('tr').find(".product_width").val(ui.item.min_width);
                $(this).closest('tr').find(".product_height").val(ui.item.min_height);
                
                $(this).closest('tr').find('.unit_price').attr('disabled','disabled');
                $(this).closest('tr').find('.unit_weight').attr('disabled','disabled');
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


<div class="container">
    <div class="row">
        <div class="col-md-3">
            <h3>Client existant</h3>
            <form class="form-horizontal" role="form" method="post">
                <select  name ="id_customer" class="pme-input-0" >
                    <?
                    foreach ($customers as $customer) {
                        ?>
                        <option value="<?= $customer["id_customer"] ?>"><?= $customer["firstname"] ?> <?= $customer["lastname"] ?></option>
                        <?
                    }
                    ?>
                </select>
                <input type="submit" >
            </form>
        </div>
        <form class="form-horizontal" role="form">
            <div class="col-md-3">
                <h3>Contact</h3>
                <div class="form-group">
                    <input type="text" name="firstname" value="<?= @$customer_info["firstname"] ?>" class="form-control" placeholder="Nom">
                    <input type="text" name="lastname" value="<?= @$customer_info["lastname"] ?>" class="form-control" placeholder="Prénom">
                    <input type="text" name="email" value="<?= @$customer_info["email"] ?>" class="form-control" placeholder="E-mail">
                </div>
            </div>

            <div class="col-md-3">
                <h3>Addresse Livraison</h3>
                <div class="form-group">
                    <input type="text" value="<?= @$customer_delivery["address1"] ?>" name="address1" class="form-control" placeholder="Adresse 1">
                    <input type="text" value="<?= @$customer_delivery["address2"] ?>" name="address2" class="form-control" placeholder="Adresse 2">
                    <input type="text" value="<?= @$customer_delivery["postcode"] ?>" name="postcode" class="form-control" placeholder="Code postal">
                    <input type="text" value="<?= @$customer_delivery["city"] ?>" name="city" class="form-control" placeholder="Ville">                
                </div>

            </div>
            <div class="col-md-3">
                <h3>Addresse Facturation</h3>
                <div class="form-group">
                    <input type="text" value="<?= @$customer_invoice["address1"] ?>" name="address1" class="form-control" placeholder="Adresse 1">
                    <input type="text" value="<?= @$customer_invoice["address2"] ?>" name="address2" class="form-control" placeholder="Adresse 2">
                    <input type="text" value="<?= @$customer_invoice["postcode"] ?>" name="postcode" class="form-control" placeholder="Code postal">
                    <input type="text" value="<?= @$customer_invoice["city"] ?>" name="city" class="form-control" placeholder="Ville">                
                </div>
            </div>
            <input type="submit" class="col-md-offset-3 col-md-9 btn-lg btn-warning">
        </form>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>Produits</h2>
            <form action="" method="post">
                <input type="hidden"  value="<?= $cid ?>" name="id_customer">
                <table class="table table-bordered table-condensed col-md-12" id="tab_devis">
                    <tr>
                        <th>Produit</th>
                        <th>Option</th>
                        <th>Largeur (mm)</th>
                        <th>Hauteur (mm)</th>
                        <th>Profondeur (mm)</th>
                        <th>Prix Unit.</th>
                        <th>Poids Unit.</th>
                        <th>Quantity</th>
                        <th>Poids</th>                        
                        <th>Prix Produit</th>
                        <th>FdP</th>
                        <th>Prix TTC</th>
                        <th>Actions</th>
                    </tr>
                    <tr id="id0">
                        <td><input type="text" name="product_name[]" class="product_name"></td>
                        <td>
                            <select name="product_option" class="pme-input-0" >
                                <option>----</option>
                            </select>
                        </td>
                        <td><input type="text" name="product_width[]"  class="product_width" size="5" /></td>
                        <td><input type="text" name="product_height[]" class="product_height" size="5" /></td>
                        <td><input type="text" name="product_depth[]"  class="product_depth" size="5" /></td>
                        <td><input type="text" name="product_unit_price[]" class="unit_price" size="5" />€</td>
                        <td><input type="text" name="product_unit_weight[]" class="unit_weight" size="5" />Kg/m²</td>
                        <td><input type="text" name="product_quantity[]" class="product_quantity" size="2" /></td>                    
                        <td><span class="poids"></span>Kg</td>
                        <td><span class="product_price"></span>€</td>
                        <td><span class="fdp"></span>€</td>
                        <td><span class="prixttc"></span>€</td>
                        <td id="btn_action">
                            <button type="button" id="newlines" onclick="javascript:addRow()"><span class="glyphicon glyphicon-plus"></span></button>
                            <button type="button" id="delline"><span class="glyphicon glyphicon-remove"></span></button>
                        </td>
                    </tr>
                </table>
                <div class="pull-left">
                    <textarea name="devis_comment"></textarea>
                </div>
                <div class="pull-right">
                    <input type="submit" name ="devis_save" class="btn-lg btn-warning">
                </div>

            </form>
        </div>
    </div>

</div>

