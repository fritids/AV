<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_product_custom';

// Name of field which is the unique key
$opts['key'] = 'id_product_custom';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_product_custom');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 50;

$opts['fdd']['id_product_custom'] = array(
    'name' => 'ID product custom',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 11,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['id_product'] = array(
    'name' => 'ID product',
    'select' => 'D',
    'maxlen' => 11,
    'values' => array(
        'table' => 'av_product',
        'column' => 'id_product',
        'description' => 'name'
    ),
    'sort' => true
);
$opts['fdd']['id_attribute'] = array(
    'name' => 'ID attribute',
    'select' => 'D',
    'maxlen' => 11,
    'values' => array(
        'table' => 'av_attributes',
        'column' => 'id_attribute',
        'description' => 'name'
    ),
    'sort' => true
);
$opts['fdd']['id_attributes_items'] = array(
    'name' => 'ID attributes items',
    'select' => 'D',
    'maxlen' => 11,
    'values' => array(
        'table' => 'av_attributes_items',
        'column' => 'id_attributes_items',
        'description' => 'name'
    ),
    'sort' => true
);
// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Formes par produits</h1>
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
