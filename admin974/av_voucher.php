<?php
include ("header.php");
require_once ("../configs/settings.php");

$opts['tb'] = 'av_voucher';

// Name of field which is the unique key
$opts['key'] = 'id_voucher';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_voucher');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 15;


$opts['fdd']['id_voucher'] = array(
    'name' => 'ID voucher',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['id_customer'] = array(
    'name' => 'ID customer',
    'select' => 'T',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_customer',
        'column' => 'id_customer',
        'description' => array("columns" => array('lastname', 'firstname', 'email'),
            "divs" => array(' ', ' - ')),
    ),
    'sort' => true
);
$opts['fdd']['code'] = array(
    'name' => 'Code',
    'select' => 'T',
    'maxlen' => 50,
    'sort' => true
);
$opts['fdd']['title'] = array(
    'name' => 'Title',
    'select' => 'T',
    'maxlen' => 255,
    'sort' => true
);
$opts['fdd']['description'] = array(
    'name' => 'Description',
    'select' => 'T',
    'maxlen' => 255,
    'sort' => true
);
$opts['fdd']['start_date'] = array(
    'name' => 'Start date',
    'select' => 'T',
    'maxlen' => 19,
    'sort' => true
);
$opts['fdd']['end_date'] = array(
    'name' => 'End date',
    'select' => 'T',
    'maxlen' => 19,
    'sort' => true
);

$opts['fdd']['quantity'] = array(
    'name' => 'Quantity',
    'select' => 'T',
    'maxlen' => 10,
    'default' => '1',
    'sort' => true
);
$opts['fdd']['reduction_percent'] = array(
    'name' => 'Reduction pourcentage',
    'select' => 'T',
    'maxlen' => 7,
    'sort' => true
);
$opts['fdd']['reduction_amount'] = array(
    'name' => 'Reduction montant',
    'select' => 'T',
    'maxlen' => 7,
    'sort' => true
);
$opts['fdd']['active'] = array(
    'name' => 'Active',
    'select' => 'T',
    'maxlen' => 1,
    'default' => '1',
    'sort' => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Les bons de réductions</h1>
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