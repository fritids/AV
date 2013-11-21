<?php


include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_category';

// Name of field which is the unique key
$opts['key'] = 'id_category';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_category');

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed


// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';

// Display special page elements
$opts['display'] = array(
	'form'  => true,
	'query' => true,
	'sort'  => true,
	'time'  => true,
	'tabs'  => true
);

$opts['fdd']['id_category'] = array(
  'name'     => 'ID category',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 10,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['id_parent'] = array(
  'name'     => 'ID parent',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);

$opts['fdd']['name'] = array(
  'name'     => 'Titre',
  'select'   => 'T',
  'maxlen'   => 128,
  'sort'     => true
);
$opts['fdd']['description'] = array(
  'name'     => 'Description',
  'select'   => 'T',
  'maxlen'   => 65535,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);
$opts['fdd']['meta_title'] = array(
    'name' => 'Meta Titre',
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
    'name' => 'Meta mot clés',
    'select' => 'T',
    'maxlen' => 255,
    'sort' => true
);

$opts['fdd']['active'] = array(
  'name'     => 'Actif ?',
  'select'   => 'T',
  'maxlen'   => 128,
  'sort'     => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<h1>Les catégories</h1>
<?
new phpMyEdit($opts);

?>

<?
getChangeLog($opts['tb'], @$_GET["PME_sys_rec"]);
?>