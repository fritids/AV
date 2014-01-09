<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_supplier_zone';

// Name of field which is the unique key
$opts['key'] = 'id_supplier_zone';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_supplier_zone');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 15;

$opts['fdd']['id_supplier_zone'] = array(
    'name' => 'ID id_supplier_zone',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 11,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['id_zone'] = array(
    'name' => 'Zone',
    'select' => 'D',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_zone',
        'column' => 'id_zone',
        'description' => array("columns" => array('nom')),
    ),
    'values2' => array(0 => "TOUTES LES ZONES"),
    'sort' => true
);
$opts['fdd']['id_supplier'] = array(
    'name' => 'Fournisseur',
    'select' => 'D',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_supplier',
        'column' => 'id_supplier',
        'description' => array("columns" => array('name')),
    ),
    'sort' => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Fournisseurs par zone</h1>
    </div>
    <div>
        <?
        new phpMyEdit($opts);
        ?>
    </div>
</div>

<?
if (isset($_GET["PME_sys_rec"]))
    $id = $_GET["PME_sys_rec"];
if (isset($_POST["PME_sys_rec"]))
    $id = $_POST["PME_sys_rec"];

getChangeLog($opts['tb'], $id);
?>