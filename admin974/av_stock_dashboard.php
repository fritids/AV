<?php
require_once "../configs/settings.php";
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$livree = 0;
$reference = '';
$invoice_stda_date = '1900-01-01';
$invoice_enda_date = '2050-01-01';
$current_state = 0;

if (isset($_POST["status_livree"]))
    $livree = $_POST["status_livree"];
if (isset($_POST["reference"]))
    $reference = $_POST["reference"];
if (isset($_POST["current_state"]))
    $current_state = $_POST["current_state"];
if (isset($_POST["invoice_stda_date"]) && $_POST["invoice_stda_date"] != '')
    $invoice_stda_date = $_POST["invoice_stda_date"];
if (isset($_POST["invoice_enda_date"]) && $_POST["invoice_enda_date"] != '')
    $invoice_enda_date = $_POST["invoice_enda_date"];

$params = array($livree, $livree, $reference, '%' . $reference . '%', $current_state, $current_state, $invoice_stda_date, $invoice_stda_date, $invoice_enda_date);


$r = $db->rawQuery("select a.*, b.name, c.lastname, c.firstname
                    from mv_orders_stock a, av_product b, av_customer c
                    where a.id_product = b.id_product
                    and  a.id_customer = c.id_customer
                    and (? = 0 or LIV_GLOBAL_INFO = ?)
                    and (ifnull(?,0) = 0 or a.reference like ?)
                    and (? = 0 or current_state = ?)
                    and current_state not in (6,5,7,8)
                    and (ifnull(?,0) = 0 or date(a.invoice_date) between ? and ?)
                    and (id_order not in (select id_order from mv_orders where (ARC_INFO = 5 and RECU_INFO = 5 and COMMANDE_INFO = 5 and LIV_INFO = 5))
                    and id_order in (select id_order from mv_orders_stock where is_product_tracking = 1))
                    ", $params);

$orderStates = $db->where("id_level", 0)
        ->get("av_order_status");
?>

<div class="container">     
    <div class="page-header">
        <h1>Commandes <small>avec suivi de stock</small></h1>
    </div>
    <div>
        <p>Commande ayant au moins 1 produit dont le stock est à suivre</p>
    </div>

    <div class="row">
        <form method="post">
            <div class="col-xs-10">
                <div class="col-xs-6">
                    <table class="table table-condensed table-bordered">
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
                    </table>
                </div>

            </div>
            <div class="col-xs-6">
                <input type="submit" value="Filtrer" class="btn btn-primary btn-block ">
            </div>
        </form>

    </div>

    <div>
        <table class="table table-bordered table-condensed">
            <tr>
                <th class="text-center">Commande</th>
                <th class="text-center">Date</th>
                <th class="text-center">Client</th>  
                <th class="text-center">Produits</th>
                <th class="text-center">Stock activé</th>
                <th class="text-center">Qte commandé</th>
                <th class="text-center">Qte dispo</th>   
            </tr>
            <?
            foreach ($r as $row) {
                ?>
                <tr>
                    <td><a href="av_orders_view.php?id_order=<?= $row["id_order"] ?>" target="_blank" ><?= $row["reference"] ?></a></td>
                    <td style="vertical-align: middle;" class="text-center"><?= strftime("%a %d %b %y %T", strtotime($row["invoice_date"])) ?></td>
                    <td style="vertical-align: middle;" class="text-center"><a href="av_customer_view.php?id_customer=<?= $row["id_customer"] ?>" ><?= $row["lastname"] . " " . $row["firstname"] ?></a></td>
                    <td><a href="av_product.php?PME_sys_fl=1&PME_sys_fm=0&PME_sys_sfn[0]=0&PME_sys_operation=PME_op_Change&PME_sys_rec=<?= $row["id_product"] ?>"><?= $row["name"] ?></a></td>
                    <td class="text-center">
                        <?= ($row["is_product_tracking"] == 0) ? "Non" : "Oui" ?>

                        <?
                        /* switch ($row["is_product_tracking"]) {
                          case 1:
                          echo '<span class="glyphicon glyphicon-ok"></span>';
                          break;
                          default :
                          echo '<span class="glyphicon glyphicon-remove"></span>';
                          } */
                        ?>   
                    </td>                   
                    <td class="text-center"><?= $row["quantity_ordered"] ?></td>                   
                    <td class="text-center"><?= $row["quantity_available"] ?></td> 

                </tr>
                <?
            }
            ?>
        </table>
    </div>

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