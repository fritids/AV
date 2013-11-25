<?php
include ("header.php");
// MySQL host name, user name, password, database, and table
require_once ("../configs/settings.php");

$opts['tb'] = 'av_order_detail';

// Name of field which is the unique key
$opts['key'] = 'id_order_detail';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = array('id_order_detail');

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
// Number of lines to display on multiple selection filters
$opts['multiple'] = '4';

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

if (isset($_GET["o"]))
    $opts['filters'] = "PMEjoin1.id_order = " . $_GET["o"];


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

$opts['fdd']['id_order_detail'] = array(
    'name' => 'ID order detail',
    'select' => 'T',
    'options' => 'AVCPDR', // auto increment
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['id_order_detail'] = array(
    'name' => 'Seq',
    'select' => 'T',
    'maxlen' => 10,
    'default' => '0',
    'sort' => true
);
$opts['fdd']['id_order'] = array(
    'name' => 'Référence',
    'select' => 'T',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_orders',
        'column' => 'id_order',
        'description' => 'reference'
    ),
    'sort' => true
);

$opts['fdd']['id_supplier'] = array(
    'name' => 'Founisseur',
    'select' => 'T',
    'maxlen' => 255,
    'values' => array(
        'table' => 'av_supplier',
        'column' => 'id_supplier',
        'description' => 'name'
    ),
    'sort' => true    
);

$opts['fdd']['supplier_date_delivery'] = array(
    'name' => 'Date livraison Fournisseur',
    'select' => 'T',    
    'sort' => true    
);

$opts['fdd']['product_name'] = array(
    'name' => 'Nom produit',
    'select' => 'T',
    'maxlen' => 255,
    'sort' => true
);
$opts['fdd']['product_quantity'] = array(
    'name' => 'Quantité',
    'select' => 'T',
    'maxlen' => 11,
    'default' => '0.00',
    'sort' => true
);
$opts['fdd']['product_price'] = array(
    'name' => 'Prix (€)',
    'select' => 'T',
    'maxlen' => 22,
    'default' => '0.000000',
    'sort' => true
);
$opts['fdd']['product_shipping'] = array(
    'name' => 'Frais de port (€)',
    'select' => 'T',
    'maxlen' => 22,
    'default' => '0.000000',
    'sort' => true
);
$opts['fdd']['attribute_name'] = array(
    'name' => 'Option',
    'select' => 'T',
    'maxlen' => 255,
    'sort' => true
);
$opts['fdd']['attribute_price'] = array(
    'name' => 'Option - Prix (€)',
    'select' => 'T',
    'maxlen' => 22,
    'default' => '0.000000',
    'sort' => true
);
$opts['fdd']['attribute_shipping'] = array(
    'name' => 'Option - FdP (€)',
    'select' => 'T',
    'maxlen' => 22,
    'default' => '0.000000',
    'sort' => true
);
$opts['fdd']['total_price_tax_incl'] = array(
    'name' => 'Prix TTC (€)',
    'select' => 'T',
    'maxlen' => 22,
    'default' => '0.000000',
    'sort' => true
);
$opts['fdd']['product_current_state'] = array(
    'name' => 'Etat',
    'select' => 'D',
    'maxlen' => 22,
    'values' => array(
        'table' => 'av_order_status',
        'column' => 'id_statut',
        'description' => 'title'
    ),
    "colattrs" => "name='product_current_state'",
    'sort' => true
);

// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<h1>Détail commande</h1>
<?
new phpMyEdit($opts);
?>

<?
getChangeLog($opts['tb'], @$_GET["PME_sys_rec"]);
?>

<script>    

    $().ready(function() {
       
        var i = 0;
        $('td[name=product_current_state]').each(function(index) {
            id = $("input[class=pme-navigation-" + i).val();
            dat = "";

            id_order_detail = $(this).parent().children(':nth-child(3)').text();

            //console.log("->" + id_order_detail + " " + index + ": " + $(this).text());

            $.ajax({
                url: "av_utilities.php",
                type: "POST",
                dataType: "json",
                async: false,
                data: {
                    action: 'getOrderDetailCombobox',
                    module: 'orders_detail',
                    id_order_detail: id_order_detail,
                },
                success: function(data) {
                    dat = data;
                },
                error: function(xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                }
            });
            $(this).html(dat);
            i++;
            if (i > 1)
                i = 0;
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
                    module: 'orders_detail',
                    id_order_detail: this.name,
                    product_current_state: this.value
                },
                success: function(data) {
                    //alert(data);
                },
                error: function(xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                }
            });
        })

    })

</script>
