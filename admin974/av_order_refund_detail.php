<?php
include ("header.php");
require_once ("../configs/settings.php");

$opts['tb'] = 'av_order_refund_detail';

// Name of field which is the unique key
$opts['key'] = 'id_order_refund_detail';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_order_refund_detail');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 30;



$opts['fdd']['id_order_refund_detail'] = array(
    'name' => 'ID order refund detail',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['id_order_refund'] = array(
    'name' => 'ID order',
    'select' => 'T',
    'maxlen' => 10,
    'sort' => true,
    'values' => array(
        'table' => 'av_order_refund',
        'column' => 'id_order_refund',
        'description' => array("columns" => array('id_order')
        )
    ),
);

$opts['fdd']['id_product'] = array(
    'name' => 'ID product',
    'select' => 'T',
    'maxlen' => 10,
    'sort' => true
);
$opts['fdd']['id_supplier_warehouse'] = array(
    'name' => 'ID supplier warehouse',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
$opts['fdd']['date_add'] = array(
    'name' => 'Date add',
    'select' => 'T',
    'maxlen' => 19,
    'sort' => true
);
$opts['fdd']['date_upd'] = array(
    'name' => 'Date upd',
    'select' => 'T',
    'maxlen' => 19,
    'sort' => true
);
$opts['fdd']['product_name'] = array(
    'name' => 'Product name',
    'select' => 'T',
    'maxlen' => 255,
    'sort' => true
);
$opts['fdd']['product_quantity'] = array(
    'name' => 'Product quantity',
    'select' => 'T',
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['product_price'] = array(
    'name' => 'Product price',
    'select' => 'T',
    'maxlen' => 22,
    'default' => '0.00',
    'sort' => true
);
$opts['fdd']['product_width'] = array(
    'name' => 'Product width',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
$opts['fdd']['product_height'] = array(
    'name' => 'Product height',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
$opts['fdd']['product_weight'] = array(
    'name' => 'Product weight',
    'select' => 'T',
    'maxlen' => 12,
    'sort' => true
);
$opts['fdd']['total_price_tax_incl'] = array(
    'name' => 'Total price tax incl',
    'select' => 'T',
    'maxlen' => 22,
    'default' => '0.00',
    'sort' => true
);
$opts['fdd']['total_price_tax_excl'] = array(
    'name' => 'Total price tax excl',
    'select' => 'T',
    'maxlen' => 22,
    'default' => '0.00',
    'sort' => true
);
$opts['fdd']['is_product_custom'] = array(
    'name' => 'Is product custom',
    'select' => 'T',
    'maxlen' => 1,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['is_debit_stock'] = array(
    'name' => 'Is debit stock',
    'select' => 'T',
    'maxlen' => 1,
    'default' => '0',
    'sort' => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Détails des remboursements</h1>
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