<?php
include ("../configs/settings.php");
require "../classes/php-export-data.class.php";
include ("../classes/MysqliDb.php");

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

    $filename = "tmp/av_" . $_POST["date_delivery"] . "-" . $_POST["id_truck"] . ".xls";
  
    $stmt = $db2->prepare("SELECT b.id_truck id_camion, b.name, a.date_livraison, CONCAT( lastname,  ' ', firstname,  ' ', address1,  ' ', address2,  ' ', postcode,  ' ', city ) address, a.nb_product_delivered, CONCAT( product_width,  'x', product_height,  'x', product_depth ) dim, a.comment1, a.comment2, a.comment3, a.horaire
                        FROM  av_tournee a, av_truck b, av_order_detail c, av_orders d, av_address e, av_customer f
                        WHERE a.id_truck = b.id_truck
                        AND a.id_order_detail = c.id_order_detail
                        AND c.id_order = d.id_order
                        AND d.id_address_delivery = e.id_address
                        and a.date_livraison = ?
                        and a.id_truck = ?
                        AND d.id_customer = f.id_customer");

    $stmt->execute(array($_POST["date_delivery"], $_POST["id_truck"]));

    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $excel = new ExportDataExcel('file');
    //$excel = new ExportDataExcel('string');
    $excel->filename = $filename;

    $excel->initialize();

    $header = array_keys($r[0]);
    $excel->addRow($header);

    foreach ($r as $record) {
        $excel->addRow($record);
    }

    $excel->finalize();

    header("Location: " . $filename);
}
?>
