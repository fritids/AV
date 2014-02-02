<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_product_warehouse';

// Name of field which is the unique key
$opts['key'] = 'id_product_warehouse';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_product_warehouse');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 50;


// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';




$opts['fdd']['id_product_warehouse'] = array(
    'name' => 'ID product warehouse',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 11,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['id_warehouse'] = array(
    'name' => 'Entrepot',
    'select' => 'D',
    'maxlen' => 11,
    'values' => array(
        'table' => 'av_warehouse',
        'column' => 'id_warehouse',
        'description' => 'name'
    ),
    'sort' => true
);
$opts['fdd']['id_product'] = array(
    'name' => 'Produit',
    'select' => 'D',
    'maxlen' => 11,
    'values' => array(
        'table' => 'av_product',
        'column' => 'id_product',
        'description' => 'name'
    ),
    'sort' => true
);
$opts['fdd']['quantity'] = array(
    'name' => 'Quantité',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Les produits par entrepots</h1>
    </div>
    <?
    new phpMyEdit($opts);

    if (isset($_GET["PME_sys_rec"]))
        $id = $_GET["PME_sys_rec"];
    if (isset($_POST["PME_sys_rec"]))
        $id = $_POST["PME_sys_rec"];

    getChangeLog($opts['tb'], $id);
    ?>

</div>
