<?php
require_once "../configs/settings.php";
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");
require('../libs/Smarty.class.php');

$arc = 0;
$recu = 0;
$comm = 0;
$liv = 0;
$reference = '';
$invoice_stda_date = '1900-01-01';
$invoice_enda_date = '2050-01-01';
$current_state = 0;

if (isset($_POST["status_arc"]))
    $arc = $_POST["status_arc"];
if (isset($_POST["status_recu"]))
    $recu = $_POST["status_recu"];
if (isset($_POST["status_comm"]))
    $comm = $_POST["status_comm"];
if (isset($_POST["status_liv"]))
    $liv = $_POST["status_liv"];
if (isset($_POST["reference"]))
    $reference = $_POST["reference"];
if (isset($_POST["current_state"]))
    $current_state = $_POST["current_state"];
if (isset($_POST["invoice_stda_date"]) && $_POST["invoice_stda_date"] != '')
    $invoice_stda_date = $_POST["invoice_stda_date"];
if (isset($_POST["invoice_enda_date"]) && $_POST["invoice_enda_date"] != '')
    $invoice_enda_date = $_POST["invoice_enda_date"];

$params = array($arc, $arc, $recu, $recu, $comm, $comm, $liv, $liv, $reference, '%' . $reference . '%', $current_state, $current_state, $invoice_stda_date, $invoice_stda_date, $invoice_enda_date);

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);
$r = $db->rawQuery("select a.* , b.lastname, b.firstname, c.*
            from mv_orders a, av_customer b, av_address c 
            where a.id_customer = b.id_customer
            and a.id_address_delivery = c.id_address
            and (ARC_INFO > 0 or RECU_INFO > 0 or COMMANDE_INFO > 0 or LIV_INFO > 0)
            and (? = 0 or ARC_INFO = ? )
            and (? = 0 or RECU_INFO = ? )
            and (? = 0 or COMMANDE_INFO = ?)
            and (? = 0 or LIV_INFO = ?)
            and (ifnull(?,0) = 0 or reference like ?)
            and (? = 0 or current_state = ?)
            and (ifnull(?,0) = 0 or date(a.invoice_date) between ? and ?)
            and id_order not in (select id_order from mv_orders where (ARC_INFO = 5 and RECU_INFO = 5 and COMMANDE_INFO = 5 and LIV_INFO = 5))", $params);

$orderStates = $db->where("id_level", 0)
        ->get("av_order_status");
?>
<div class="container">
    <div class="page-header">
        <h1>Tableau de bord des ventes</h1>
    </div>


    <div class="row">
        <form method="post">

            <div class="col-xs-10">

                <div class="col-xs-6">
                    <table class="table table-bordered">
                        <tr>
                            <td>Date commande</td>
                            <td> 
                                <table>
                                    <tr>
                                        <td>début </td>
                                        <td>
                                            <input type="text" name="invoice_stda_date" class="datepicker"  value="<?= ($invoice_stda_date != '1900-01-01') ? $invoice_stda_date : "" ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>fin </td>
                                        <td>
                                            <input type="text" name="invoice_enda_date" class="datepicker"  value="<?= ($invoice_enda_date != '2050-01-01') ? $invoice_enda_date : "" ?>">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>   
                        <tr>
                            <td>Référence</td>
                            <td><input type="text" name="reference" value="<?= $reference ?>"></td>
                        </tr>

                        <tr>
                            <td>Statuts Commande</td>
                            <td>
                                <select name="current_state" class="pme-input-0">
                                    <option value="">--</option>
                                    <?
                                    foreach ($orderStates as $orderState) {
                                        ?>
                                        <option value="<?= $orderState["id_statut"] ?>"
                                        <?= ($orderState["id_statut"] == $current_state) ? "selected" : "" ?>
                                                ><?= $orderState["title"] ?></option>
                                                <?
                                            }
                                            ?>
                                </select>
                            </td>
                        </tr>

                    </table>
                </div>
                <div class="col-xs-6">
                    <table class="table table-bordered">
                        <tr>
                            <td>#</td>
                            <td class="text-center"><span class="glyphicon glyphicon-refresh"></span></td>
                            <td class="text-center alert-5"><span class="glyphicon glyphicon-ok"></span></td>
                            <td class="text-center alert-6"><span class="glyphicon glyphicon-bullhorn"></span></td>
                            <td class="text-center alert-8"><span class="glyphicon glyphicon-exclamation-sign"></span></td>
                        </tr>
                        <tr>
                            <th>COMM</th>
                            <td class="text-center"><input type="radio" name="status_comm" value="0" <?= (@$_POST["status_comm"] == 0) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_comm" value="5" <?= (@$_POST["status_comm"] == 5) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_comm" value="6" <?= (@$_POST["status_comm"] == 6) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_comm" value="8" <?= (@$_POST["status_comm"] == 8) ? "checked" : "" ?>></td>
                        </tr>
                        <tr>
                            <th>ARC</th>
                            <td class="text-center"><input type="radio" name="status_arc" value="0" <?= (@$_POST["status_arc"] == 0) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_arc" value="5" <?= (@$_POST["status_arc"] == 5) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_arc" value="6" <?= (@$_POST["status_arc"] == 6) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_arc" value="8" <?= (@$_POST["status_arc"] == 8) ? "checked" : "" ?>></td>
                        </tr>                    
                        <tr>
                            <th>RECU</th>
                            <td class="text-center"><input type="radio" name="status_recu" value="0" <?= (@$_POST["status_recu"] == 0) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_recu" value="5" <?= (@$_POST["status_recu"] == 5) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_recu" value="6" <?= (@$_POST["status_recu"] == 6) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_recu" value="8" <?= (@$_POST["status_recu"] == 8) ? "checked" : "" ?>></td>
                        </tr>
                        <tr>
                            <th>LIV</th>
                            <td class="text-center"><input type="radio" name="status_liv" value="0" <?= (@$_POST["status_liv"] == 0) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_liv" value="5" <?= (@$_POST["status_liv"] == 5) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_liv" value="6" <?= (@$_POST["status_liv"] == 6) ? "checked" : "" ?>></td>
                            <td class="text-center"><input type="radio" name="status_liv" value="8" <?= (@$_POST["status_liv"] == 8) ? "checked" : "" ?>></td>
                        </tr>
                    </table>
                </div>



            </div>
            <div class="col-xs-10">
                <input type="submit" value="Filtrer" class="btn btn-primary btn-block ">
            </div>
        </form>

    </div>

    <hr>
    <table>
        <tr>
            <td>Nb commandes</td><td><?= count($r) ?></td>
        </tr>
    </table>

    <table class="table table-bordered table-condensed">
        <tr valign="middle">
            <th class="text-center">#</th>
            <th class="text-center">Date</th>
            <th class="text-center">Client</th>            
            <th class="text-center">Address Liv.</th>            
            <th class="text-center" style="width:130px">Status</th>            
            <th class="text-center" style="width:90px">Montant</th>
            <th class="text-center">Nb jrs ouvrés</th>
            <th class="text-center" style="width:70px">COMM</th>
            <th class="text-center" style="width:70px">ARC</th>            
            <th class="text-center" style="width:70px">RECU</th>        
            <th class="text-center" style="width:70px">LIV PROG.</th>        
        </tr>
        <?
        foreach ($r as $row) {
            ?>
            <tr>
                <td class="text-center"><a href="av_orders_view.php?id_order=<?= $row["id_order"] ?>" target="_blank" ><?= $row["reference"] ?></a></td>
                <td class="text-center"><?= strftime("%a %d %b %y %T", strtotime($row["invoice_date"])) ?></td>
                <td class="text-center"><?= $row["lastname"] . " " . $row["firstname"] ?></td>
                <td class="text-center"><?= $row["address1"] . " <br> " . $row["address2"] . " <br> " . $row["postcode"] . " " . $row["city"] ?></td>
                <td class="text-center alert-<?= $row["current_state"] ?>"><?= $row["state_label"] ?></td>
                <td class="text-center"><?= number_format($row["total_paid"], 2, ".", " ") ?> €</td>
                <td class="text-center"><?= getJours($row["invoice_date"], date("Y-m-d")) ?> j</td>
                <td style="vertical-align: middle;" class="text-center alert-<?= $row["COMMANDE_INFO"] ?>">
                    <?
                    switch ($row["COMMANDE_INFO"]) {
                        case 5:
                            echo '<span class="glyphicon glyphicon-ok"></span>';
                            break;
                        case 6:
                            echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                            break;
                        case 8:
                            echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                            break;
                        default :
                    }
                    ?>                        
                </td>
                <td style="vertical-align: middle;"  class="text-center alert-<?= $row["ARC_INFO"] ?>">
                    <?
                    switch ($row["ARC_INFO"]) {
                        case 5:
                            echo '<span class="glyphicon glyphicon-ok"></span>';
                            break;
                        case 6:
                            echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                            break;
                        case 8:
                            echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                            break;
                        default :
                    }
                    ?>                        
                </td>
                <td style="vertical-align: middle;"  class="text-center alert-<?= $row["RECU_INFO"] ?>">
                    <?
                    switch ($row["RECU_INFO"]) {
                        case 5:
                            echo '<span class="glyphicon glyphicon-ok"></span>';
                            break;
                        case 6:
                            echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                            break;
                        case 8:
                            echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                            break;
                        default :
                    }
                    ?>                        
                </td>
                <td style="vertical-align: middle;" class="text-center alert-<?= $row["LIV_INFO"] ?>">
                    <?
                    switch ($row["LIV_INFO"]) {
                        case 5:
                            echo '<span class="glyphicon glyphicon-ok"></span>';
                            break;
                        case 6:
                            echo '<span class="glyphicon glyphicon-bullhorn"></span>';
                            break;
                        case 8:
                            echo '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                            break;
                        default :
                    }
                    ?>                        
                </td>

            </tr>
            <?
        }
        ?>

        <tr></tr>
    </table>

</div>

<script>
    $(".toggle_order").click(function() {
        if (this.checked) {
            $(".orders").attr('checked', 'checked');
        } else {
            $(".orders").removeAttr('checked');
        }

    });

    $(function() {
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd"
        });
    });
</script>