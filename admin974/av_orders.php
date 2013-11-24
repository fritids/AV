<?php
// MySQL host name, user name, password, database, and table

include ("header.php");
require_once ("../configs/settings.php");

$opts['tb'] = 'mv_orders';

// Name of field which is the unique key
$opts['key'] = 'id_order';

// Type of key field (int/real/string/date etc.)
$opts['key_type'] = 'int';

// Sorting field(s)
$opts['sort_field'] = "-id_order";

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
$opts['options'] = 'CF';

// Number of lines to display on multiple selection filters
$opts['multiple'] = '10';

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
    'options' => 'VL',
    'maxlen' => 10,
    'sort' => true,
    'URL' => 'av_orders_view.php?id_order=$key'
);
$opts['fdd']['date_add'] = array(
    'name' => 'Date Commande',
    'options' => 'L',
    'select' => 'D',
    'maxlen' => 10,
    'sort' => true,
    'strftimemask' => "%d %b %y %H:%M:%S"
);
$opts['fdd']['id_customer'] = array(
    'name' => 'Client',
    'select' => 'T',
    'options' => 'VL',
    'maxlen' => 10,
    'values' => array(
        'table' => 'av_customer',
        'column' => 'id_customer',
        'description' => array("columns" => array('lastname', 'firstname', 'email'),
            "divs" => array(' ', ' - ')),
    ),
    'sort' => true
);


$opts['fdd']['id_address_invoice'] = array(
    'name' => 'Adresse facturation',
    'select' => 'T',
    'maxlen' => 10,
    'options' => 'VL',
    'values' => array(
        'table' => 'av_address',
        'column' => 'id_address',
        'description' => array("columns" => array('address1', 'address2', 'postcode', 'country'),
            "divs" => array(' ', ' ', ' ', ' ')),
    ),
    'sort' => true
);
$opts['fdd']['id_address_delivery'] = array(
    'name' => 'Adresse livraison',
    'select' => 'T',
    'maxlen' => 10,
    'options' => 'V',
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
    'options' => 'LC',
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
    'URL' => 'av_orders_view.php?id_order=$key'
);
$opts['fdd']['order_comment'] = array(
    'name' => 'Commentaire client',
    'select' => 'T',
    'maxlen' => 65535,
    'options' => 'VC',
    'textarea' => array(
        'html' => true,
        'rows' => 20,
        'cols' => 100),
    'sort' => true,
);


// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
?>
<h1>Les ventes</h1>

<?
/* <button class="btn btn-primary modif">Modifier les statuts</button> */

new phpMyEdit($opts);
?>

<br>

<?
getChangeLog($opts['tb'], @$_GET["PME_sys_rec"]);
?>

<script>
    $(".couleur").click(function() {
        $('td[name=order_state]').each(function(index) {


            $(this).closest("tr").addClass("alert alert-success");

        });
    });

    $(".modif").click(function() {
        var i = 0;
        $('td[name=order_state]').each(function(index) {
            dat = "";
            reference = $(this).parent().children(':nth-child(3)').text();
            //console.log("->" + reference + " " + index + ": " + $(this).text());

            $.ajax({
                url: "av_utilities.php",
                type: "POST",
                dataType: "json",
                async: false,
                data: {
                    action: 'getOrderCombobox',
                    module: 'orders',
                    reference: reference
                },
                success: function(data) {
                    dat = data;
                },
                error: function() {
                    console.log('Error occured');
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
                    module: 'orders',
                    id_order: this.name,
                    current_state: this.value
                },
                success: function(data) {
                    console.log('OK');
                },
                error: function(xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                }
            });
        })
    });

</script>


