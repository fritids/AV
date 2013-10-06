<?php
// MySQL host name, user name, password, database, and table
include ("../configs/settings.php");
include ("header.php");

$opts['tb'] = 'av_orders';

// Name of field which is the unique key
$opts['key'] = 'id_order';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_order');

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
$opts['options'] = 'ACPVDF';

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

$opts['fdd']['id_order'] = array(
    'name' => 'ID order',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['reference'] = array(
    'name' => 'Reference',
    'select' => 'T',
    'maxlen' => 9,
    'sort' => true
);
$opts['fdd']['id_customer'] = array(
    'name' => 'Client',
    'select' => 'T',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_customer',
        'column' => 'id_customer',
        'description' => array("columns" => array('lastname', 'firstname'),
            "divs" => array(' ', ' ')),
    ),
    'sort' => true
);


$opts['fdd']['id_address_delivery'] = array(
    'name' => 'Adresse livraison',
    'select' => 'T',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_address',
        'column' => 'id_address',
        'description' => array("columns" => array('address1', 'address2', 'postcode', 'country'),
            "divs" => array(' ', ' ', ' ', ' ')),
    ),
    'sort' => true
);
$opts['fdd']['id_address_invoice'] = array(
    'name' => 'Adresse facturation',
    'select' => 'T',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_address',
        'column' => 'id_address',
        'description' => array("columns" => array('address1', 'address2', 'postcode', 'country'),
            "divs" => array(' ', ' ', ' ', ' ')),
    ),
    'sort' => true
);
$opts['fdd']['current_state'] = array(
    'name' => 'Status',
    'select' => 'D',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_order_status',
        'column' => 'id_statut',
        'description' => 'title'
    ),
    "colattrs" => "name='order_state'",
    'sort' => true
);
$opts['fdd']['total_paid'] = array(
    'name' => 'Total TTC',
    'select' => 'T',
    'maxlen' => 19,
    'default' => '0.00',
    'sort' => true,
    'URL' => 'av_order_detail.php?o=$key'
);



// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
new phpMyEdit($opts);
?>
<br>
<button id="edit" class="btn btn-primary"> Modifier les status </button>
<?
getChangeLog($opts['tb'], @$_GET["PME_sys_rec"]);
?>

 <script>
        $("#edit").click(function() {
            if ($(this).text() == "Terminer") {
                location.reload();
            }
            var i = 0;
            $('td[name=order_state]').each(function(index) {
                id = $("input[class=pme-navigation-" + i).val();
                dat = "";
                //console.log("->" + id + " " + index + ": " + $(this).text());

                $.ajax({
                    url: "av_utilities.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        action: 'getOrderCombobox',
                        module: 'orders',
                        id_order: id,
                        current_state: index
                    },
                    success: function(data) {
                        dat = data;
                    },
                    error: function() {
                        alert('Error occured');
                    }
                });
                $(this).html(dat);
                i++;
            });


            $("select")
                    .change(function(i, v) {

                //console.log(this.name + " " + this.value);

                $.ajax({
                    url: "av_utilities.php",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        action: 'update',
                        module: 'orders',
                        id_order: this.name,
                        current_state: this.value
                    },
                    success: function(data) {
                        //alert(data);
                    }
                });
            })
            $(this).text("Terminer");

        })

    </script>


