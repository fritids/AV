<?php

include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_address';

// Name of field which is the unique key
$opts['key'] = 'id_address';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_address');

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


/* Get the user's default language and use it if possible or you can
  specify particular one you want to use. Refer to official documentation
  for list of available languages. */
$opts['language'] = $_SERVER['HTTP_ACCEPT_LANGUAGE'] . '-UTF8';

/* Table-level filter capability. If set, it is included in the WHERE clause
  of any generated SELECT statement in SQL query. This gives you ability to
  work only with subset of data from table.

  $opts['filters'] = "column1 like '%11%' AND column2<17";
  $opts['filters'] = "section_id = 9";
  $opts['filters'] = "PMEtable0.sessions_count > 200";
 */

if(isset($_GET["c"]))
        $opts['filters'] = "PMEtable0.id_customer= ".$_GET["c"];

$opts['fdd']['id_address'] = array(
    'name' => 'ID address',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);

$opts['fdd']['alias'] = array(
    'name' => 'Type',
    'select' => 'T',
    'maxlen' => 128,
    'sort' => true
);
$opts['fdd']['address1'] = array(
    'name' => 'Adresse 1',
    'select' => 'T',
    'maxlen' => 128,
    'sort' => true
);
$opts['fdd']['address2'] = array(
    'name' => 'Adresse 2',
    'select' => 'T',
    'maxlen' => 128,
    'sort' => true
);
$opts['fdd']['postcode'] = array(
    'name' => 'CP',
    'select' => 'T',
    'maxlen' => 12,
    'sort' => true
);
$opts['fdd']['country'] = array(
    'name' => 'Ville',
    'select' => 'T',
    'maxlen' => 64,
    'sort' => true
);
$opts['fdd']['phone'] = array(
  'name'     => 'Tel.',
  'select'   => 'T',
  'maxlen'   => 32,
  'sort'     => true
);
$opts['fdd']['phone_mobile'] = array(
  'name'     => 'Tel.2',
  'select'   => 'T',
  'maxlen'   => 32,
  'sort'     => true
);
/*$opts['fdd']['city'] = array(
    'name' => 'Ville',
    'select' => 'T',
    'maxlen' => 64,
    'sort' => true
);*/

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<h1>Carnet d'adresse</h1>
<?
new phpMyEdit($opts);
?>

<?
getChangeLog($opts['tb'], @$_GET["PME_sys_rec"]);
?>