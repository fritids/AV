<?php

include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_product_images';

// Name of field which is the unique key
$opts['key'] = 'id_image';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_image');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 50;


// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';



$opts['fdd']['id_image'] = array(
    'name' => 'ID image',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['id_product'] = array(
    'name' => 'ID product',
    'select' => 'D',
    'maxlen' => 10,
    'sort' => true,
    'values' => array(
        'table' => 'av_product',
        'column' => 'id_product',
        'description' => 'name'
        ));
$opts['fdd']['cover'] = array(
    'name' => 'Cover',
    'select' => 'D',
    'maxlen' => 1,
    'default' => '0',
    'sort' => true,
    'values2' => array(0 => "non", 1 => "Oui")
);

$opts['fdd']['filename'] = array(
    'name' => 'Filename',
    'select' => 'T',
    'input' => 'F',
    'maxlen' => 250,
    'imagepath' => '../img/p/',
    'sort' => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Les images produits</h1>
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
