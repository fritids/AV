<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_departements';

// Name of field which is the unique key
$opts['key'] = 'id_departement';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'string';

// Sorting field(s)
$opts['sort_field'] = array('id_departement');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 200;


$opts['fdd']['id_departement'] = array(
    'name' => 'ID departement',
    'select' => 'T',
    'maxlen' => 2,
    'sort' => true
);
$opts['fdd']['id_region'] = array(
    'name' => 'Région',
    'select' => 'T',
    'maxlen' => 2,
    'values' => array(
        'table' => 'av_regions',
        'column' => 'id_region',
        'description' => 'nom'
    ),
    'sort' => true
);
$opts['fdd']['id_zone'] = array(
    'name' => 'Zone',
    'select' => 'D',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_zone',
        'column' => 'id_zone',
        'description' => 'nom'
    ),
    'sort' => true
);
$opts['fdd']['nom'] = array(
    'name' => 'Nom',
    'select' => 'T',
    'maxlen' => 32,
    'sort' => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Les départements par zone</h1>
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
