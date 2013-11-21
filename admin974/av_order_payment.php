<?php

include ("header.php");

// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_order_payment';

// Name of field which is the unique key
$opts['key'] = 'id_order_payment';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_order_payment');

// Number of records to display on the screen
// Value of -1 lists all records in a table
$opts['inc'] = 15;

// Display special page elements
$opts['display'] = array(
	'form'  => true,
	'query' => true,
	'sort'  => true,
	'time'  => true,
	'tabs'  => true
);

$opts['fdd']['id_order_payment'] = array(
  'name'     => 'ID order payment',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 11,
  'default'  => '0',
  'sort'     => true
);
$opts['fdd']['id_order'] = array(
  'name'     => 'ID order',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);
$opts['fdd']['order_reference'] = array(
  'name'     => 'Order reference',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);
$opts['fdd']['id_currency'] = array(
  'name'     => 'ID currency',
  'select'   => 'T',
  'maxlen'   => 10,
  'sort'     => true
);
$opts['fdd']['amount'] = array(
  'name'     => 'Amount',
  'select'   => 'T',
  'maxlen'   => 12,
  'sort'     => true
);
$opts['fdd']['payment_method'] = array(
  'name'     => 'Payment method',
  'select'   => 'T',
  'maxlen'   => 255,
  'sort'     => true
);
$opts['fdd']['conversion_rate'] = array(
  'name'     => 'Conversion rate',
  'select'   => 'T',
  'maxlen'   => 15,
  'default'  => '1.000000',
  'sort'     => true
);
$opts['fdd']['transaction_id'] = array(
  'name'     => 'Transaction ID',
  'select'   => 'T',
  'maxlen'   => 254,
  'sort'     => true
);
$opts['fdd']['card_number'] = array(
  'name'     => 'Card number',
  'select'   => 'T',
  'maxlen'   => 254,
  'sort'     => true
);
$opts['fdd']['card_brand'] = array(
  'name'     => 'Card brand',
  'select'   => 'T',
  'maxlen'   => 254,
  'sort'     => true
);
$opts['fdd']['card_expiration'] = array(
  'name'     => 'Card expiration',
  'select'   => 'T',
  'maxlen'   => 7,
  'sort'     => true
);
$opts['fdd']['card_holder'] = array(
  'name'     => 'Card holder',
  'select'   => 'T',
  'maxlen'   => 254,
  'sort'     => true
);
$opts['fdd']['date_add'] = array(
  'name'     => 'Date add',
  'select'   => 'T',
  'maxlen'   => 19,
  'sort'     => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
new phpMyEdit($opts);

?>

