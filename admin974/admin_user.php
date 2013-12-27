<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");


$opts['tb'] = 'admin_user';

// Name of field which is the unique key
$opts['key'] = 'id_admin';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_admin');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 15;

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';


/* Get the user's default language and use it if possible or you can
  specify particular one you want to use. Refer to official documentation
  for list of available languages. */
$opts['language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '-UTF8';


$opts['fdd']['id_admin'] = array(
    'name' => 'ID admin',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 11,
    'default' => '0',
    'sort' => true
);

$opts['fdd']['email'] = array(
    'name' => 'Email',
    'select' => 'T',
    'maxlen' => 320,
    'sort' => true
);
$opts['fdd']['mdp'] = array(
    'name' => 'Mdp',
    'select' => 'T',
    'maxlen' => 32,
    'sort' => true
);
$opts['fdd']['nom'] = array(
    'name' => 'Nom',
    'select' => 'T',
    'maxlen' => 100,
    'sort' => true
);
$opts['fdd']['prenom'] = array(
    'name' => 'Prenom',
    'select' => 'T',
    'maxlen' => 100,
    'sort' => true
);
$opts['fdd']['role'] = array(
    'name' => 'Roles',
    'select' => 'T',
    'maxlen' => 100,
    'sort' => true,
    'values' => array("ADMIN", "COMMANDE", "LIVRAISON", "LOGISTIC")
);


// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Gestionnaire utilisateurs</h1>
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