<?php
session_start();
require_once ("../configs/settings.php");
mysql_connect($bdserv, $bduser, $bdpass);
mysql_select_db($bdname);
include('securite.php');
?>

<?php
include ("header.php");
// MySQL host name, user name, password, database, and table


$opts['tb'] = 'av_employee';

// Name of field which is the unique key
$opts['key'] = 'id_employee';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_employee');

// Set default prefixes for variable

/* Table-level filter capability. If set, it is included in the WHERE clause
  of any generated SELECT statement in SQL query. This gives you ability to
  work only with subset of data from table.

  $opts['filters'] = "column1 like '%11%' AND column2<17";
  $opts['filters'] = "section_id = 9";
  $opts['filters'] = "PMEtable0.sessions_count > 200";
 */

/* Field definitions

  Fields will be displayed left to right on the screen in the order in which they
  appear in generated list. Here are some most used field options documented.

  ['name'] is the title used for column headings, etc.;
  ['maxlen'] maximum length to display add/edit/search input boxes
  ['trimlen'] maximum length of string content to display in row listing
  ['width'] is an optional display width specification for the column
  e.g.  ['width'] = '100px';
  ['mask'] a string that is used by sprintf() to format field output
  ['sort'] true or false; means the users may sort the display on this column
  ['strip_tags'] true or false; whether to strip tags from content
  ['nowrap'] true or false; whether this field should get a NOWRAP
  ['select'] T - text, N - numeric, D - drop-down, M - multiple selection
  ['options'] optional parameter to control whether a field is displayed
  L - list, F - filter, A - add, C - change, P - copy, D - delete, V - view
  Another flags are:
  R - indicates that a field is read only
  W - indicates that a field is a password field
  H - indicates that a field is to be hidden and marked as hidden
  ['URL'] is used to make a field 'clickable' in the display
  e.g.: 'mailto:$value', 'http://$value' or '$page?stuff';
  ['URLtarget']  HTML target link specification (for example: _blank)
  ['textarea']['rows'] and/or ['textarea']['cols']
  specifies a textarea is to be used to give multi-line input
  e.g. ['textarea']['rows'] = 5; ['textarea']['cols'] = 10
  ['values'] restricts user input to the specified constants,
  e.g. ['values'] = array('A','B','C') or ['values'] = range(1,99)
  ['values']['table'] and ['values']['column'] restricts user input
  to the values found in the specified column of another table
  ['values']['description'] = 'desc_column'
  The optional ['values']['description'] field allows the value(s) displayed
  to the user to be different to those in the ['values']['column'] field.
  This is useful for giving more meaning to column values. Multiple
  descriptions fields are also possible. Check documentation for this.
 */

$opts['fdd']['id_employee'] = array(
    'name' => 'ID employee',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['id_profile'] = array(
    'name' => 'ID profile',
    'select' => 'T',
    'maxlen' => 10,
    'sort' => true
);
$opts['fdd']['lastname'] = array(
    'name' => 'Lastname',
    'select' => 'T',
    'maxlen' => 32,
    'sort' => true
);
$opts['fdd']['firstname'] = array(
    'name' => 'Firstname',
    'select' => 'T',
    'maxlen' => 32,
    'sort' => true
);
$opts['fdd']['email'] = array(
    'name' => 'Email',
    'select' => 'T',
    'maxlen' => 128,
    'sort' => true
);
$opts['fdd']['passwd'] = array(
    'name' => 'Passwd',
    'select' => 'T',
    'maxlen' => 32,
    'sort' => true
);
$opts['fdd']['last_passwd_gen'] = array(
    'name' => 'Last passwd gen',
    'select' => 'T',
    'options' => 'AVCPDR', // updated automatically (MySQL feature)
    'maxlen' => 19,
    'default' => 'CURRENT_TIMESTAMP',
    'sort' => true
);
$opts['fdd']['active'] = array(
    'name' => 'Active',
    'select' => 'T',
    'maxlen' => 1,
    'default' => '0',
    'sort' => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<h1>Les employés</h1>
<?
new phpMyEdit($opts);
?>

<?
getChangeLog($opts['tb'], @$_GET["PME_sys_rec"]);
?>