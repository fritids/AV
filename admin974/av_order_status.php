<?php

include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_order_status';

// Name of field which is the unique key
$opts['key'] = 'id_statut';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_statut');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 50;

// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';

/* Get the user's default language and use it if possible or you can
   specify particular one you want to use. Refer to official documentation
   for list of available languages. */
$opts['language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '-UTF8';


$opts['fdd']['id_statut'] = array(
  'name'     => 'ID statut',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['id_level'] = array(
  'name'     => 'Categorie',
  'select'   => 'T',
  'maxlen'   => 1,
  'sort'     => true
);
$opts['fdd']['title'] = array(
  'name'     => 'Title',
  'select'   => 'T',
  'maxlen'   => 50,
  'sort'     => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Statuts des commandes</h1>
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