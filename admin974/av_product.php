<?php

include ("header.php");

// MySQL host name, user name, password, database, and table
include ("../configs/settings.php");

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