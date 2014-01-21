<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_warehouse';

// Name of field which is the unique key
$opts['key'] = 'id_warehouse';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_warehouse');

// Number of records to display on the screen
// Value of -1 lists all records in a table


$opts['fdd']['id_warehouse'] = array(
  'name'     => 'ID warehouse',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['name'] = array(
  'name'     => 'Name',
  'select'   => 'T',
  'maxlen'   => 128,
  'sort'     => true
);
$opts['fdd']['address'] = array(
  'name'     => 'Address',
  'select'   => 'T',
  'maxlen'   => 255,
  'sort'     => true
);


// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Les entrepôts</h1>
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