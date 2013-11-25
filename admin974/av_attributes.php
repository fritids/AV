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
$opts['inc'] = 15;


$opts['fdd']['id_attribute'] = array(
  'name'     => 'ID attribute',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['name'] = array(
  'name'     => 'Name',
  'select'   => 'T',
  'maxlen'   => 50,
  'sort'     => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<h1>Les attributs</h1>
<?
new phpMyEdit($opts);
?>
<?
getChangeLog($opts['tb'], @$_GET["PME_sys_rec"]);
?>
