<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_attributes';

// Name of field which is the unique key
$opts['key'] = 'id_attribute';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_attribute');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['options'] = 'ACDFP';

$opts['fdd']['id_attribute'] = array(
    'name' => 'ID attribute',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 11,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['name'] = array(
    'name' => 'Name',
    'select' => 'T',
    'maxlen' => 50,
    'sort' => true
);
$opts['fdd']['type'] = array(
    'name' => 'type',
    'select' => 'T',
    'maxlen' => 50,
    'values2' => array(0 => "Général", 1 => "Spécifique"),
    'sort' => true
);
$opts['fdd']['is_duplicable'] = array(
    'name' => 'is_duplicable',
    'select' => 'T',
    'maxlen' => 50,  
    'values2' => array(0 => "Non", 1 => "Oui"),
    'sort' => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Les attributs principaux</h1>
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