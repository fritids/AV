<?php
// MySQL host name, user name, password, database, and table

include ("header.php");
require_once ("../configs/settings.php");

$opts['tb'] = 'av_truck';

// Name of field which is the unique key
$opts['key'] = 'id_truck';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_truck');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 15;


$opts['fdd']['id_truck'] = array(
  'name'     => 'ID truck',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 10,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['imma'] = array(
  'name'     => 'Imma',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);
$opts['fdd']['name'] = array(
  'name'     => 'Name',
  'select'   => 'T',
  'maxlen'   => 100,
  'sort'     => true
);

$opts['fdd']['is_actif'] = array(
  'name'     => 'Is actif',
  'select'   => 'T',
  'maxlen'   => 1,
  'default'  => '1',
  'sort'     => true
);
$opts['fdd']['capacity'] = array(
  'name'     => 'Capacity',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);
$opts['fdd']['status'] = array(
  'name'     => 'Status',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Les camions</h1>
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