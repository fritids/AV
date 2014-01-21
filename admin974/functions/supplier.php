<?php

function getSupplierInfo($sid) {
    global $db;
    $r = $db->where(id_supplier, $sid)
            ->get("av_supplier");
    return $r[0];
}

function getSupplierName($id) {
    global $db;    
    $s = $db->rawQuery("select name 
        from av_supplier_warehouse a, av_supplier b
        where a.id_supplier = b.id_supplier
        and id_supplier_warehouse = ? ", array($id));    
    if ($s)
        return ($s[0]["name"]);
}
function getWarehouseName($id) {
    global $db;
    $s = $db->rawQuery("select name 
        from av_supplier_warehouse a, av_warehouse b
        where a.id_warehouse = b.id_warehouse
        and id_supplier_warehouse = ? ", array($id));    
    if ($s)
        return ($s[0]["name"]);
}
?>
