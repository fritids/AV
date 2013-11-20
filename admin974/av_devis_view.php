<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/devis.php");
include ("../functions/users.php");
include ("../functions/tools.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);
$devis = $db->get("av_devis");
$did = "";

if (isset($_POST["id_devis"]) || isset($_GET["id_devis"])) {
    if (isset($_POST["id_devis"]))
        $did = $_POST["id_devis"];
    if (isset($_GET["id_devis"]))
        $did = $_GET["id_devis"];

    $r = $db->rawQuery("select * from av_devis_detail where id_devis = ?", array($did));

    $deviss = getDevis($did);
    if ($deviss) {
        $customer_info = getCustomerDetail($deviss[0]["id_customer"]);
        $customer_delivery = getAdresse($deviss[0]["id_customer"], 'delivery');
        $customer_invoice = getAdresse($deviss[0]["id_customer"], 'invoice');
    }
}
if (isset($_POST["id_customer"]) && $_POST["id_customer"] != "") {
    $cid = $_POST["id_customer"];

    $customer_info = getCustomerDetail($cid);
    $customer_delivery = getAdresse($cid, 'delivery');
    $customer_invoice = getAdresse($cid, 'invoice');

    $deviss = getUserDevis($cid);
}
?>

<?
//print_r($deviss);
?>

<script>
    $(function() {
        $("#accordion")
                .accordion({
            header: "> ul > li",
            heightStyle: "content"
        })
                .sortable({
            axis: "y",
            handle: "li",
            stop: function(event, ui) {
                // IE doesn't register the blur when sorting
                // so trigger focusout handlers to remove .ui-state-focus
                ui.item.children("li").triggerHandler("focusout");
            }
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
<script>
    $(function() {
        $(".collapse").collapse()
    })
</script>


<div class="container">
    <form method="post"> 
        <div class="row">

            <div class="col-xs-3" >
                <label for="ajax_customer">Client :</label>
                <input type="text" id ="ajax_customer" name="ajax_customer"  />
                <input type="hidden" name ="id_customer"  id ="id_customer"/>


            </div>
            <div class="col-xs-3" >
                <label for="id_devis">N° Devis :</label>
                <input type="text" name ="id_devis"  />

            </div>

            <div class="col-xs-3" >
                <!--
                <select name="id_devis" class="pme-input-0">
                <?
                foreach ($devis as $dev) {
                    ?>
                         <option value="<?= $dev["id_devis"] ?>"
                    <? if ($dev["id_devis"] == $did && !empty($did)) echo "selected" ?>
                         ><?= $dev["id_devis"] ?> <?= $dev["date_add"] ?></option>
                    <?
                }
                ?>
                    
                </select>--->
                <input type="submit" class="btn btn-sm btn-primary">
            </div>
        </div>

    </form>
    <hr>



    <?
    if (!empty($deviss)) {
        ?>
        <div class="row">
            <div class="col-xs-4">
                <table class="table table-bordered" >
                    <tr>
                        <th>Nom :</th>
                        <td><?= $customer_info["lastname"] ?></td>
                    </tr>
                    <tr>
                        <th>Prénom :</th>
                        <td><?= $customer_info["firstname"] ?></td>
                    </tr>
                    <tr>
                        <th>Email :</th>
                        <td><?= $customer_info["email"] ?></td>

                    </tr>
                </table>
            </div>
        </div>
        <div class="row">


            <div class="col-xs-10 panel-group" id="accordion">
                <div class="panel panel-default">               

                    <?
                    $i = 0;
                    foreach ($deviss as $devis) {
                        $i++
                        ?>

                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $i ?>">
                                    <table class="table table-bordered table-condensed" style="margin-bottom: 0px" >
                                        <tr>
                                            <th>Devis :</th>
                                            <td><?= $devis["id_devis"] ?></td>
                                            <th>Montant :</th>
                                            <td><?= $devis["total_paid"] ?> €</td>
                                            <th>Date ajout :</th>
                                            <td><?= $devis["date_add"] ?></td>
                                            <th>Etat :</th>
                                            <td class="
                                            <?
                                            switch ($devis["current_state"]) {
                                                case 1:
                                                    echo "alert alert-warning";
                                                    break;

                                                case 2:
                                                    echo "alert alert-danger";
                                                    break;
                                                case 3:
                                                    echo "alert alert-success";
                                                    break;
                                            }
                                            ?> "
                                                >
                                                    <?= $devis["current_state"] ?>
                                            </td>
                                        </tr>
                                    </table>

                                </a>
                            </h4>
                        </div>
                        <div id="collapse<?= $i ?>" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <table class="table table-bordered table-condensed col-xs-12" id="tab_devis">
                                        <tr>
                                            <th>Produit</th>
                                            <th>Attributs</th>
                                            <th>Larg x Long</th>
                                            <th>Prix Unit.</th>
                                            <th>Poids Unit.</th>
                                            <th>Quantity</th>
                                            <th>Prix ttc</th>                        
                                        </tr>
                                        <?
                                        foreach ($devis["details"] as $line) {
                                            ?>

                                            <tr id="id0">
                                                <td><?= $line["product_name"] ?></td>
                                                <td>
                                                    <?
                                                    foreach ($line["combinations"] as $attribute) {
                                                        ?>
                                                        <?= $attribute["name"] ?><br>
                                                        <?
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= $line["product_width"] ?> x <?= $line["product_height"] ?></td>
                                                <td><?= $line["product_price"] ?></td>
                                                <td><?= $line["product_weight"] ?></td>
                                                <td><?= $line["product_quantity"] ?></td>                                                             
                                                <td><?= $line["total_price_tax_incl"] ?></td>                    
                                            </tr>
                                            <?
                                        }
                                        ?>
                                    </table>

                                </div>
                            </div>
                        </div>

                        <?
                    }
                    ?>
                </div>

            </div>
            <?
        }
        ?>
    </div>
</div>

