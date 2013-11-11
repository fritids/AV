<?php

function getSupplierInfo($sid) {
    global $db;

    $r = $db->where(id_supplier, $sid)
            ->get("av_supplier");

    return $r[0];
}

?>
