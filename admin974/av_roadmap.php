<?php
include ("../configs/settings.php");
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
require('../libs/Smarty.class.php');
require('../classes/tcpdf.php');

$db = new Mysqlidb($bdd_host, $bdd_user, $bdd_pwd, $bdd_name);

$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
);

$dns = 'mysql:host=' . $bdd_host . ';dbname=' . $bdd_name;
$db2 = new PDO($dns, $bdd_user, $bdd_pwd, $options);

$d = $db->rawQuery("select distinct date_livraison from av_tournee order by 1");
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

if (!empty($date_delivery) && !empty($id_truck)) {



    $roadmap_filename = md5(rand());

    // on recupère les produits affectés au camion
    $listOrderProduct = $db->rawQuery("select a.id_address_delivery, a.id_order,
                        a.reference, d.postcode, a.id_customer, b.*, c.*, e.name supplier_name, a.order_comment
                        from av_orders a, av_order_detail b , av_tournee c, av_address d, av_supplier e
                        where a.id_order = b.id_order
                        and b.id_order_detail = c.id_order_detail 
                        and a.id_address_delivery = d.id_address
                        and b.id_supplier = e.id_supplier
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
    <table>';
    $tmpRef = "";
    foreach ($listOrderProduct as $OrderProduct) {

        $customer = getOrderUserDetail($OrderProduct["id_customer"]);
        $adresse = getUserOrdersAddress($OrderProduct["id_address_delivery"]);

        $p_qty = $OrderProduct["nb_product_delivered"];

        $addrs = $adresse["address1"] . "<br>";
        if ($adresse["address2"])
            $addrs .= $adresse["address2"] . "<br>";
        $addrs .= $adresse["postcode"] . " " . $adresse["city"];


        if ($tmpRef != $OrderProduct["reference"]) {

            $pdf_roadmap .= '
                            <tr>
                            <th bgcolor = "#cccccc" >' . $OrderProduct["reference"] . '</th>
                            <th bgcolor = "#cccccc" >' . $customer["firstname"] . ' ' . $customer["lastname"] . ' <br> ' . $addrs . '</th>
                            <th bgcolor = "#cccccc" >' . $adresse["phone_mobile"] . '<br>' . $adresse["phone"] . '</th>
                            <th bgcolor = "#cccccc" >' . $OrderProduct["comment1"] . '</th>
                            <th bgcolor = "#cccccc" >' . $OrderProduct["comment3"] . '</th>
                            <th bgcolor = "#cccccc" >' . $OrderProduct["horaire"] . '</th>
                            <th bgcolor = "#cccccc" >Comm. client: ' . $OrderProduct["order_comment"] . '</th></tr>';

            $tmpRef = $OrderProduct["reference"];
        }
        $pdf_roadmap .= '
                        <tr>                        
                        <td colspan = "4"> ' . $p_qty . ' x ' . $OrderProduct["product_name"] . ' <br> ' .
                ($OrderProduct["product_width"] != "" ? ' Largeur (mm) :' . $OrderProduct["product_width"] : '') .
                ($OrderProduct["product_height"] != "" ? ', Longueur (mm) :' . $OrderProduct["product_height"] : '') .
                '</td>
                        <td>' . $OrderProduct["supplier_name"] . ' ' . $OrderProduct["supplier_date_delivery"] . ' </td>    
                        <td>' . $p_qty * $OrderProduct["product_weight"] . ' Kg</td>
                        <td>' . $p_qty * $OrderProduct["total_price_tax_incl"] . ' €</td>
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

    $pdf->writeHTML($pdf_roadmap, true, false, true, false, '');

    $pdf->lastPage();

    $path = "./ressources/roadmap";
    $pdf_path = $path;
    @mkdir($pdf_path);

    $pdf->Output($pdf_path . "/" . $roadmap_filename . ".pdf", 'F');

    echo '<a href = "' . $pdf_path . '/' . $roadmap_filename . '.pdf" target = "_blank">Télécharger</a>';
} else {
    ?>

    <div class="container">
        <div class="text-center">
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
        </div>
    </form>
    </div>
    <script>
        $("#truck").chained("#date_delivery");
    </script>

    <?
}
?>

