<?php

include ("header.php");

// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_product';

// Name of field which is the unique key
$opts['key'] = 'id_product';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_product');



$opts['inc'] = 50;

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
// Number of lines to display on multiple selection filters
$opts['multiple'] = '10';

// Navigation style: B - buttons (default), T - text links, G - graphic links
// Buttons position: U - up, D - down (default)
// Display special page elements
$opts['display'] = array(
    'form' => true,
    'query' => true,
    'sort' => true,
    'time' => true,
    'tabs' => true
);

/* Get the user's default language and use it if possible or you can
  specify particular one you want to use. Refer to official documentation
  for list of available languages. */
$opts['language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '-UTF8';


$opts['fdd']['id_product'] = array(
    'name' => 'ID',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['id_category'] = array(
    'name' => 'Catégorie',
    'select' => 'T',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_category',
        'column' => 'id_category',
        'description' => 'name'
    ),
    'sort' => true
);
$opts['fdd']['quantity'] = array(
    'name' => 'Quantité',
    'select' => 'T',
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['price'] = array(
    'name' => 'Prix m²',
    'select' => 'T',
    'maxlen' => 22,
    'default' => '0.000000',
    'sort' => true
);

$opts['fdd']['reference'] = array(
    'name' => 'Ref.',
    'select' => 'T',
    'maxlen' => 32,
    'sort' => true
);

$opts['fdd']['name'] = array(
    'name' => 'Nom',
    'select' => 'T',
    'maxlen' => 150,
    'sort' => true,
    'URL' => 'av_product_attribute.php?p=$key'
);
$opts['fdd']['description'] = array(
    'name' => 'Description',
    'select' => 'T',
    'maxlen' => 65535,
    'options' => 'AC',
    'textarea' => array(
        'html' => true,
        'rows' => 20,
        'cols' => 100),
    'sort' => true,
    'css' => array('id' => 'test', 'page_type' => 'test')
);
$opts['fdd']['description_short'] = array(
    'name' => 'Description courte',
    'select' => 'T',
    'maxlen' => 65535,    
    'textarea' => array(
        'html' => true,
        'rows' => 20,
        'cols' => 100),
    'sort' => true,
    'width' => '200px'
);
$opts['fdd']['video'] = array(
    'name' => 'Video.',
    'options' => 'AC',
    'select' => 'T',
    'maxlen' => 255
);

$opts['fdd']['min_width'] = array(
    'name' => 'Largeur Min.',
    'options' => 'AC',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
$opts['fdd']['min_height'] = array(
    'name' => 'Hauteur Min.',
    'options' =>'AC',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
$opts['fdd']['max_width'] = array(
    'name' => 'Largeur Max',
    'options' =>'AC',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
$opts['fdd']['max_height'] = array(
    'name' => 'Hauteur Max',
    'options' =>'AC',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
$opts['fdd']['min_area_invoiced'] = array(
    'name' => 'Surface Min. facturé',
    'options' =>'AC',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
$opts['fdd']['max_area_invoiced'] = array(
    'name' => 'Surface Max. facturé',
    'options' =>'AC',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
$opts['fdd']['position'] = array(
    'name' => 'Position',
    'options' =>'AC',
    'select' => 'T',
    'maxlen' => 11,
    'sort' => true
);
$opts['fdd']['meta_title'] = array(
    'name' => 'Meta Titre',
    'options' =>'AC',
    'select' => 'T',
    'maxlen' => 128,
    'sort' => true
);
$opts['fdd']['meta_description'] = array(
    'name' => 'meta description',
    'options' =>'AC',
    'select' => 'T',
    'maxlen' => 255,
    'sort' => true
);
$opts['fdd']['meta_keywords'] = array(
    'name' => 'Meta mots clés',
    'options' =>'AC',
    'select' => 'T',
    'maxlen' => 255,
    'sort' => true
);
$opts['fdd']['link_rewrite'] = array(
  'name'     => 'URL Friendly',
  'select'   => 'T',
  'maxlen'   => 128,
  'sort'     => true
);


// Now important call to phpMyEdit
//require_once 'phpMyEdit.class.php';
require_once 'extensions/phpMyEdit-mce-cal.class.php';
?>
<h1>Les produits</h1>
<?
new phpMyEdit_mce_cal($opts);
?>

<?

getChangeLog($opts['tb'], @$_GET["PME_sys_rec"]);
?>