<?php

function RandomString() {
    $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $randstring = '';
    for ($i = 0; $i < 10; $i++) {
        @$randstring .= $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}

function getDeliveryRatio($pweight) {
    global $db;

    $params = array($pweight);

    $r = $db->rawQuery("
        SELECT delivery_ratio 
        FROM `av_range_weight` 
        WHERE ? between `delimiter1` and `delimiter2`", $params);

    return($r[0]["delivery_ratio"]);
}

function getShippingPrice($pid, $pqte_order) {
    global $db;

    $p_info = getProductInfos($pid);

    $delivery_ratio = getDeliveryRatio($p_info["weight"]);

    return($p_info["weight"] * $ratio);
}

function getLastOrderId() {
    global $db;

    $r = $db->rawQuery("select max(id_order)+1 id from av_orders");

    $id = str_pad($r[0]["id"], 9, '0', STR_PAD_LEFT);
    
    return($id);
}

?>
