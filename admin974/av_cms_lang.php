<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_cms_lang';

// Name of field which is the unique key
$opts['key'] = 'id_cms';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_cms');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 15;


// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';


$opts['fdd']['id_cms'] = array(
    'name' => 'ID cms',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['content'] = array(
    'name' => 'Content',
    'select' => 'T',
    'options' => 'VC', // auto increment    
    'maxlen' => -1,
    'textarea' => array(
        'rows' => 40,
        'cols' => 150),
    'sort' => true
);
$opts['fdd']['meta_title'] = array(
    'name' => 'Meta title',
    'select' => 'T',
    'maxlen' => 128,
    'sort' => true
);
$opts['fdd']['meta_description'] = array(
    'name' => 'Meta description',
    'select' => 'T',
    'maxlen' => 255,
    'sort' => true
);
$opts['fdd']['meta_keywords'] = array(
    'name' => 'Meta mots clés',
    'select' => 'T',
    'maxlen' => 255,
    'sort' => true
);
$opts['fdd']['link_rewrite'] = array(
    'name' => 'Lien',
    'select' => 'T',
    'maxlen' => 128,
    'sort' => true
);


// Now important call to phpMyEdit
require_once 'extensions/phpMyEdit-mce-cal.class.php';
?>


<div class="container">
    <div class="page-header">
        <h1>Les pages de contenu</h1>
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