<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_attributes_items';

// Name of field which is the unique key
$opts['key'] = 'id_attributes_items';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

$opts['options'] = 'ACDFP';

// Sorting field(s)
$opts['sort_field'] = array('id_attributes_items');

// Number of records to display on the screen
// Value of -1 lists all records in a table

$opts['fdd']['id_attributes_items'] = array(
    'name' => 'ID attributes items',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 11,
    'default' => '0',
    'sort' => true
);

$opts['fdd']['id_attribute'] = array(
    'name' => 'ID attribute',
    'select' => 'T',
    'maxlen' => 11,
    'values' => array(
        'table' => 'av_attributes',
        'column' => 'id_attribute',
        'description' => 'name'
    ),
    'sort' => true
);

$opts['fdd']['name'] = array(
    'name' => 'Name',
    'select' => 'T',
    'maxlen' => 128,
    'sort' => true
);

$opts['fdd']['picture'] = array(
    'name' => 'Picture',
    'select' => 'T',
    'input' => 'F',
    'imagepath' => '../img/f/',
    'maxlen' => 128,
    'sort' => true
);
$opts['fdd']['min_area_invoiced'] = array(
    'name' => 'Min area invoiced',
    'select' => 'T',
    'maxlen' => 12,
    'mask' => '%01.2f m²',
    'sort' => true
);
$opts['fdd']['max_area_invoiced'] = array(
    'name' => 'Max area invoiced',
    'select' => 'T',
    'maxlen' => 12,
    'mask' => '%01.2f m²',
    'sort' => true
);
$opts['fdd']['price_impact_percentage'] = array(
    'name' => 'Price impact percentage',
    'select' => 'T',
    'maxlen' => 7,    
    'sort' => true
);
$opts['fdd']['price_impact_amount'] = array(
    'name' => 'Price impact amount',
    'select' => 'T',
    'maxlen' => 12,
    'mask' => '%01.2f €',
    'sort' => true
);
$opts['fdd']['position'] = array(
    'name' => 'Position',
    'select' => 'T',
    'maxlen' => 2,
    'sort' => true
);
$opts['fdd']['active'] = array(
    'name' => 'Actif',
    'select' => 'D',
    'maxlen' => 1,
    'sort' => true,
    'values2' => array(0 => "Non", 1 => "Oui")
);


// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Les sous attributs</h1>
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