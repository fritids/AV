<?php

include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_product_attribute';

// Name of field which is the unique key
$opts['key'] = 'id_product_attribute';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_product_attribute');

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed


// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';

/* Table-level filter capability. If set, it is included in the WHERE clause
   of any generated SELECT statement in SQL query. This gives you ability to
   work only with subset of data from table.

$opts['filters'] = "column1 like '%11%' AND column2<17";
$opts['filters'] = "section_id = 9";
$opts['filters'] = "PMEtable0.sessions_count > 200";
*/

if (isset($_GET["p"]))
    $opts['filters'] = "PMEtable0.id_product = ". $_GET["p"];
    

$opts['fdd']['id_product_attribute'] = array(
  'name'     => 'ID product attribute',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 10,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['id_product'] = array(
  'name'     => 'ID product',
  'select'   => 'D',
  'maxlen'   => 10,
  'values' => array(
    'table'  => 'av_product',
    'column' => 'id_product',
    'description' => 'name'
  ),
  'sort'     => true
);
$opts['fdd']['id_attribute'] = array(
  'name'     => 'Attribut',
  'select'   => 'D',
  'maxlen'   => 10,
  'values' => array(
    'table'  => 'av_attributes',
    'column' => 'id_attribute',
    'description' => 'name'
  ),
  'sort'     => true
);

$opts['fdd']['name'] = array(
  'name'     => 'Nom',
  'select'   => 'T',
  'maxlen'   => 128,
  'sort'     => true
);
$opts['fdd']['price'] = array(
  'name'     => 'Prix unitaire',
  'select'   => 'T',
  'maxlen'   => 22,
  'default'  => '0.000000',
  'sort'     => true
);
$opts['fdd']['weight'] = array(
  'name'     => 'Poids',
  'select'   => 'T',
  'maxlen'   => 22,
  'default'  => '0.000000',
  'sort'     => true
);
// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Attributs produits</h1>
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