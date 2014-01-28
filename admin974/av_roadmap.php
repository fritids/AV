<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
require('../libs/Smarty.class.php');
require('../classes/tcpdf.php');
require("./functions/supplier.php");

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
);

$dns = 'mysql:host=' . $bdd_host . ';dbname=' . $bdd_name;
$db2 = new PDO($dns, $bdd_user, $bdd_pwd, $options);

$d = $db->rawQuery("select distinct date_livraison from av_tournee order by 1 desc");
$t = $db->rawQuery("select distinct date_livraison, a.id_truck, b.name from av_tournee a, av_truck b where a.id_truck = b.id_truck order by 1,2");


if (isset($_POST) && !(empty($_POST))) {
    $date_delivery = $_POST["date_delivery"];
    $id_truck = $_POST["id_truck"];
}
if (isset($_GET) && !(empty($_GET))) {
    $date_delivery = $_GET["date_livraison"];
    $id_truck = $_GET["id_truck"];
}


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Allovitre');
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetFont('times', '', 10);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '', PDF_HEADER_STRING);
$pdf->AddPage('L', 'A4');
?>
<div class="container">
    <div class="page-header">
        <h1>Feuille de route</h1>

    </div>
    <div class="text-center">
        <?
        if (!empty($date_delivery) && !empty($id_truck)) {

            $roadmap_filename = md5(rand());

            // on recupère les produits affectés au camion
            $listOrderProduct = $db->rawQuery("select a.id_address_delivery, a.id_address_invoice, a.id_order,
                        a.reference, d.postcode, a.id_customer, b.*, c.*, a.order_comment, a.delivery_comment
                        from av_orders a, av_order_detail b , av_tournee c, av_address d
                        where a.id_order = b.id_order
                        and b.id_order_detail = c.id_order_detail 
                        and a.id_address_delivery = d.id_address
                        and c.id_truck = ? 
                        and c.date_livraison = ?                                                                     
                        order by c.position
                        ", array($id_truck, $date_delivery))
            ?>

            <?
            $camion = $db->where("id_truck", $id_truck)
                    ->get("av_truck");

            $pdf_roadmap = 'TOURNEE : ' . $date_delivery . ' // ' . strtoupper($camion[0]["name"]) . '<br> 
    <table class = "col-md-12 table-condensed">
    <tr>
    <td>
    <table style="border:1px solid #ccc" border="1" cellpadding="5">';
            $tmpRef = "";
            foreach ($listOrderProduct as $OrderProduct) {

                $customer = getOrderUserDetail($OrderProduct["id_customer"]);
                $adresse = getUserOrdersAddress($OrderProduct["id_address_delivery"]);
                $adresseInvoice = getUserOrdersAddress($OrderProduct["id_address_invoice"]);
                $attributes = getOrdersDetailAttribute($OrderProduct["id_order_detail"]);
                $customs = getOrdersCustomMainItem($OrderProduct["id_order_detail"]);
                $SupplierName = getSupplierName($OrderProduct["id_supplier_warehouse"]);
                $WarehouseName = getWarehouseName($OrderProduct["id_supplier_warehouse"]);

                $p_qty = $OrderProduct["nb_product_delivered"];

                $addrs = $adresse["address1"] . "<br>";
                if ($adresse["address2"])
                    $addrs .= $adresse["address2"] . "<br>";
                $addrs .= $adresse["postcode"] . " " . $adresse["city"] . "<br>[". $adresse["warehouse"]["zone_name"]."]<br>[". $adresse["warehouse"]["warehouse_name"]."]";

                if ($tmpRef != $OrderProduct["reference"]) {

                    $pdf_roadmap .= '
            <tr>
                <th bgcolor = "#ccc">' . $OrderProduct["reference"] . '</th>
                <th bgcolor = "#ccc">' . $customer["firstname"] . ' ' . $customer["lastname"] . ' <br> ' . $addrs . '</th>
                <th bgcolor = "#ccc">liv.: ' . $adresse["phone_mobile"] . '<br>' . $adresse["phone"] . '<br>fact.:' . $adresseInvoice["phone_mobile"] . '<br>' . $adresseInvoice["phone"] . '</th>
                <th bgcolor = "#ccc">' . $OrderProduct["comment1"] . '</th>
                <th bgcolor = "#ccc">' . $OrderProduct["comment3"] . '</th>
                <th bgcolor = "#ccc">' . $OrderProduct["horaire"] . '</th>                       
                <th bgcolor = "#ccc">Comm. client: ' . htmlspecialchars($OrderProduct["order_comment"]) . '</th>
                <th bgcolor = "#ccc">Comm. Interne:' . htmlspecialchars($OrderProduct["delivery_comment"]) . '</th>
                </tr>';

                    $tmpRef = $OrderProduct["reference"];
                }

                $pdf_roadmap .= '
            <tr>
            <td colspan = "3"> ' . $p_qty . ' x ' . $OrderProduct["product_name"] . ' <br> ' .
                        ($OrderProduct["product_width"] != "" ? ' Largeur (mm): ' . $OrderProduct["product_width"] : '') .
                        ($OrderProduct["product_height"] != "" ? ', Longueur (mm): ' . $OrderProduct["product_height"] : '') . "<br>";


                foreach ($attributes as $attribute) {
                    $pdf_roadmap .= '- ' . $attribute["attribute_name"] . ': ' . $attribute["attribute_value"] . '<br>';
                }

                if ($customs) {

                    foreach ($customs as $custom) {
                        $pdf_roadmap .= '- ' . $custom["main_item_name"];
                        foreach ($custom["sub_item"] as $sub_item) {
                            $pdf_roadmap .= '- ' . $sub_item["sub_item_name"] . " : ";
                            foreach ($sub_item["item_values"] as $item_value) {
                                $pdf_roadmap .= $item_value["item_value_name"] . " : " . $item_value["custom_value"] . " - ";
                            }
                        }
                    }
                }

                $pdf_roadmap .= '
            </td>
            <td colspan="2">' . $SupplierName . ' [' . $WarehouseName . '] ' . $OrderProduct["supplier_date_delivery"] . '</td>
            <td>' . $p_qty * $OrderProduct["product_weight"] . ' Kg</td>
            <td>' . $OrderProduct["total_price_tax_incl"] . ' €</td>
            </tr>';


                $param = array(
                    "id_order" => $OrderProduct["id_order"],
                    "id_user" => $_SESSION["user_id"],
                    "id_order_detail" => $OrderProduct["id_order_detail"],
                    "category" => "roadmap",
                    "bdc_filename" => $roadmap_filename
                );
                $r = $db->insert("av_order_bdc", $param);
            }

            $pdf_roadmap .= '</table></td></tr></table>';

            if($_SESSION['email'] =="stephane.alamichel@gmail.com")
                echo $pdf_roadmap;

            $pdf->writeHTML($pdf_roadmap, true, false, true, false, '');

            $pdf->lastPage();

            $path = "./ressources/roadmap";
            $pdf_path = $path;
            @mkdir($pdf_path);

            $pdf->Output($pdf_path . "/" . $roadmap_filename . ".pdf", 'F');

            echo '<a href = "' . $pdf_path . '/' . $roadmap_filename . '.pdf" class="btn btn-lg btn-primary" target = "_blank"><span class="glyphicon glyphicon-download"></span> Télécharger</a>';
        } else {
            ?>
            <form action="" method="post">

                <div class="col-md-12"> 
                    <div class="col-md-3">
                        <label for="date_delivery" > Date livraison :
                            <select id="date_delivery" class="pme-input-0" name="date_delivery">
                                <option value="--"  >--</option>
                                <?
                                foreach ($d as $rec) {
                                    ?>
                                    <option value="<?= $rec["date_livraison"] ?>"  ><?= $rec["date_livraison"] ?></option>
                                    <?
                                }
                                ?>
                            </select>
                    </div>

                    <div class="col-md-3">
                        <label for="truck" > Camion :
                            <select id="truck" class="pme-input-0" name="id_truck">
                                <option value="--"  >--</option>
                                <?
                                foreach ($t as $rec) {
                                    ?>
                                    <option value="<?= $rec["id_truck"] ?>" class="<?= $rec["date_livraison"] ?>" ><?= $rec["name"] ?></option>
                                    <?
                                }
                                ?>
                            </select>
                    </div>

                    <div class="col-md-2">
                        <input type="submit" class="btn btn-primary" value="Télécharger"  />

                    </div>
                </div>
            </form>
        </div>    
    </div>
    <script>
        $("#truck").chained("#date_delivery");
    </script>

    <?
}
?>

