<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/devis.php");
include ("../functions/users.php");
include ("../functions/tools.php");
include ("../functions/orders.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);
$devis = $db->get("av_devis");
$did = "";


if (isset($_GET["create_order"]) && isset($_POST["id_devis"])) {
    $did = $_POST["id_devis"];
    $payment = $_POST["payment"];
    $discount = $_POST["discount"];
    $cid = CreateOrder($did, $payment, $discount);
}
if (isset($_GET["remove"]) && isset($_POST["id_devis"])) {
    $did = $_POST["id_devis"];
    $params = array("current_state" => 5);
    $r = $db->where("id_devis", $did)
            ->update("av_devis", $params);
}


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
if ((isset($_POST["id_customer"]) && $_POST["id_customer"] != "") || !empty($cid)) {
    if (isset($_POST["id_customer"]) && $_POST["id_customer"] != "")
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
    <!---
    <div class="row">          
       <ul class=" col-xs-6 alert alert-success">
           <li class="list-unstyled">Nouveauté : possibilité d'annuler un devis en attente.</li>
       </ul>
   </div>--->
    <div class="page-header">
        <h1>Consultation des devis</h1>
    </div>

    <form method="post"> 
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
            <input type="submit" class="btn btn-sm btn-primary">
        </div>
    </form>
    <div class="clearfix">  </div>
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
                                <table class="table table-bordered table-condensed" style="margin-bottom: 0px;" >
                                    <tr>
                                        <th>
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $i ?>">
                                                <table class="table table-bordered table-condensed" style="margin-bottom: 0px" >
                                                    <tr>
                                                        <th>Devis :</th>
                                                        <td><?= $devis["id_devis"] ?></td>
                                                        <th>Montant :</th>
                                                        <td><?= round($devis["total_paid"] * $config["vat_rate"], 2) ?> €</td>
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
                                                            case 4:
                                                                echo "alert alert-success";
                                                                break;
                                                            case 5:
                                                                echo "alert alert-danger";
                                                                break;
                                                        }
                                                        ?> "
                                                            >
                                                                <?
                                                                switch ($devis["current_state"]) {
                                                                    case 1:
                                                                        echo "En attente";
                                                                        break;

                                                                    case 2:
                                                                        echo "Rejeté";
                                                                        break;
                                                                    case 3:
                                                                        echo "Validé";
                                                                        break;
                                                                    case 4:
                                                                        echo "Converti: " . $devis["id_order"];
                                                                        break;
                                                                    case 5:
                                                                        echo "Annulé";
                                                                        break;
                                                                }
                                                                ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </a>
                                        </th>
                                        <td nowrap>
                                            <?
                                            if ($devis["current_state"] == 1) {
                                                ?>

                                                <form action="?create_order" method="post" class="form-horizontal">
                                                    <input type="hidden" value="<?= $devis["id_devis"] ?>" name="id_devis">
                                                    <div class="col-xs-6">
                                                        <select name="payment" required="required" class="form-control">
                                                            <option value="Carte credit">Carte crédit</option>
                                                            <option value="Chèque">Chèque</option>
                                                            <option value="Virement bancaire">Virement bancaire</option>                                                            
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <input type="text" name="discount" placeholder="Remise en €" size="5" class="form-control"><br>
                                                    </div>
                                                    <br class="clearfix">
                                                    <input type="submit" value="Valider le devis" class="btn btn-block btn-success">

                                                </form>

                                                <div class="clearfix"></div>
                                                <p>
                                                <form action="?remove" method="post">
                                                    <input type="hidden" value="<?= $devis["id_devis"] ?>" name="id_devis">
                                                    <button class="btn btn-warning btn-block" data-toggle="tooltip" title="Annuler le devis"><span class="glyphicon glyphicon-remove"></span> Annuler le devis</button>
                                                </form> 
                                                </p>
                                                <?
                                            } else if ($devis["current_state"] == 4) {
                                                ?>
                                                <p>
                                                    <a href="av_orders_view.php?id_order=<?= $devis["id_order"] ?>" class="btn btn-primary btn-block" data-toggle="tooltip" title="Aller à la facture"><span class="glyphicon glyphicon-zoom-in"></span> Aller à la commande</a>
                                                </p>
                                                <?
                                            }
                                            ?>

                                            <form action="av_download_pdf.php?devis" method="post" target="blank">
                                                <input type="hidden" value="<?= $devis["id_devis"] ?>" name="id_devis">
                                                <button class="btn btn-primary btn-block" data-toggle="tooltip" title="Télécharger le devis au format PDF"><span class="glyphicon glyphicon-floppy-save"></span> Télécharger</button>
                                            </form> 
                                        </td>
                                    </tr>
                                </table>
                            </h4>
                        </div>
                        <div id="collapse<?= $i ?>" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <table class="table table-bordered table-condensed col-xs-12" id="tab_devis" style="margin-bottom: 0px;"    >
                                    <tr>
                                        <th>Produit</th>
                                        <th>Attributs</th>
                                        <th>Larg x Long</th>
                                        <th>Poids Unit.</th>
                                        <th>Quantity</th>
                                        <th>Prix ttc</th>                        
                                    </tr>
                                    <?
                                    foreach ($devis["details"] as $line) {
                                        $attribute_price = 0;
                                        ?>

                                        <tr id="id0">
                                            <td>
                                                <?= $line["product_name"] ?>
                                                <?
                                                if (isset($line["custom"])) {
                                                    echo "<br>";
                                                    foreach ($line["custom"] as $custom) {
                                                        echo " - " . $custom["main_item_name"];
                                                        foreach ($custom["sub_item"] as $sub_item) {
                                                            echo " - " . $sub_item["sub_item_name"] . "<br>";
                                                            foreach ($sub_item["item_values"] as $item_value) {
                                                                echo $item_value["item_value_name"] . ": " . $item_value["custom_value"] . "<br>";
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?
                                                foreach ($line["combinations"] as $attribute) {

                                                    $attribute_price = $attribute["prixttc"];
                                                    ?>
                                                    <?= $attribute["name"] ?><br>
                                                    <?
                                                }
                                                ?>
                                            </td>
                                            <td><?= $line["product_width"] ?> x <?= $line["product_height"] ?></td>
                                            <td><?= $line["product_weight"] ?></td>
                                            <td><?= $line["product_quantity"] ?></td>                                                             
                                            <td><?= round($line["total_price_tax_incl"] * $config["vat_rate"], 2) ?></td>                    
                                        </tr>
                                        <?
                                    }
                                    ?>
                                </table>

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

