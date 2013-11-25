<?php

$opts['inc'] = 50;

// Navigation style: B - buttons (default), T - text links, G - graphic links
// Buttons position: U - up, D - down (default)
$opts['navigation'] = 'BGD';

// Set default prefixes for variables
$opts['js']['prefix'] = 'PME_js_';
$opts['dhtml']['prefix'] = 'PME_dhtml_';
$opts['cgi']['prefix']['operation'] = 'PME_op_';
$opts['cgi']['prefix']['sys'] = 'PME_sys_';
$opts['cgi']['prefix']['data'] = 'PME_data_';

$opts['logtable'] = 'changelog';

// Options you wish to give the users
// A - add,  C - change, P - copy, V - view, D - delete,
// F - filter, I - initial sort suppressed
$opts['options'] = 'ACDF';

$opts['cgi']['append']['PME_sys_fl'] = 1;

$opts['display'] = array('form' => true, 'num_pages' => true, 'num_records' => true, 'sort' => true, 'tabs' => true, 'time' => false);
?>
