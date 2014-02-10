<?php
include ("header.php");
require_once ("../configs/settings.php");

$opts['tb'] = 'av_order_refund';

// Name of field which is the unique key
$opts['key'] = 'id_order_refund';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('-id_order_refund');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 30;

$opts['fdd']['id_order_refund'] = array(
  'name'     => 'ID order refund',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 10,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['id_order'] = array(
  'name'     => 'ID order',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);
$opts['fdd']['id_customer'] = array(
  'name'     => 'ID customer',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true,
  'values' => array(
        'table' => 'av_customer',
        'column' => 'id_customer',
        'description' => array("columns" => array('lastname', 'firstname', 'email'),
            "divs" => array(' ', ' - ')),
    ),
);

$opts['fdd']['date_order'] = array(
  'name'     => 'Date Commande',
  'select'   => 'T',
  'maxlen'   => 19,
  'sort'     => true
);
$opts['fdd']['date_refund'] = array(
  'name'     => 'Date Remboursement',
  'select'   => 'T',
  'maxlen'   => 19,
  'sort'     => true
);
$opts['fdd']['payment'] = array(
  'name'     => 'Paiement',
  'select'   => 'T',
  'maxlen'   => 255,
  'sort'     => true
);
$opts['fdd']['total_shipping'] = array(
  'name'     => 'Total fdp',
  'select'   => 'T',
  'maxlen'   => 12,
  'sort'     => true
);
$opts['fdd']['total_refund'] = array(
  'name'     => 'Total Remboursé',
  'select'   => 'T',
  'maxlen'   => 12,
  'sort'     => true
);
$opts['fdd']['vat_rate'] = array(
  'name'     => 'Tva',
  'select'   => 'T',
  'maxlen'   => 7,
  'sort'     => true
);
$opts['fdd']['refund_comment'] = array(
  'name'     => 'Comment',
  'select'   => 'T',
  'maxlen'   => 65535,
  'textarea' => array(
    'rows' => 5,
    'cols' => 50),
  'sort'     => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<div class="container">
    <div class="page-header">
        <h1>Les Remboursements de commande</h1>
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