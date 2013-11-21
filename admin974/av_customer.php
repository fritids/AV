<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");


$opts['tb'] = 'av_customer';

// Name of field which is the unique key
$opts['key'] = 'id_customer';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_customer');

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';

// Display special page elements
$opts['display'] = array(
    'form' => true,
    'query' => true,
    'sort' => true,
    'time' => true,
    'tabs' => true
);


$opts['fdd']['id_customer'] = array(
    'name' => 'ID customer',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);

$opts['fdd']['firstname'] = array(
    'name' => 'Prénom',
    'select' => 'T',
    'maxlen' => 32,
    'sort' => true
);
$opts['fdd']['lastname'] = array(
    'name' => 'Nom',
    'select' => 'T',
    'maxlen' => 32,
    'sort' => true,
    'URL' => 'av_customer_view.php?id_customer=$key'
);
$opts['fdd']['email'] = array(
    'name' => 'Email',
    'select' => 'T',
    'maxlen' => 128,
    'sort' => true
);

$opts['fdd']['passwd'] = array(
    'name' => 'mot de passe',
    'select' => 'T',
    'maxlen' => 32,
    'sort' => true,
    //'sqlw' => 'AES_ENCRYPT(' . _COOKIE_KEY_ . '"$val_as")',
    'sqlw' => 'md5("' . _COOKIE_KEY_ . '$val_as'. '")',
);
$opts['fdd']['customer_group'] = array(
    'name' => 'Group',
    'select' => 'T',
    'maxlen' => 1,
    'sort' => true
);
$opts['fdd']['active'] = array(
    'name' => 'actif ?',
    'select' => 'T',
    'maxlen' => 1,
    'sort' => true
);



// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<h1>Les comptes clients</h1>
<?
new phpMyEdit($opts);
?>

<?
getChangeLog($opts['tb'], @$_GET["PME_sys_rec"]);
?>