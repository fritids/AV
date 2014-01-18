<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_attributes_items_values';

// Name of field which is the unique key
$opts['key'] = 'id_attributes_items_values';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

$opts['options'] = 'ACDFP';

// Sorting field(s)
$opts['sort_field'] = array('id_attributes_items');

// Number of records to display on the screen
// Value of -1 lists all records in a table

$opts['fdd']['id_attributes_items_values'] = array(
    'name' => 'ID attributes items values',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 11,
    'default' => '0',
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
$opts['fdd']['name'] = array(
    'name' => 'Name',
    'select' => 'T',
    'maxlen' => 20,
    'sort' => true
);
$opts['fdd']['min_width'] = array(
  'name'     => 'Min width',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);
$opts['fdd']['max_width'] = array(
  'name'     => 'Max width',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);
$opts['fdd']['is_width'] = array(
    'name' => 'Is width',
    'select' => 'D',    
    'maxlen' => 1,
    'default' => '0',
    'values2' => array(0 => "Non", 1 => "Oui"),
    'sort' => true
);
$opts['fdd']['is_height'] = array(
    'name' => 'Is height',
    'select' => 'D',
    'maxlen' => 1,
    'values2' => array(0 => "Non", 1 => "Oui"),
    'sort' => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Les valeurs d'attributs</h1>
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