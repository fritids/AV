<?php

function getUserOrders($id) {
    global $db;
    $r = $db->where("id_customer", $id)
            ->get("av_orders");

    foreach ($r as $k => $order) {
        $r[$k]["details"] = getUserOrdersDetail($order["id_order"]);
    }
    return $r;
}

function getOrderInfos($oid) {
    global $db;
    $r = $db->where("id_order", $oid)
            ->get("av_orders");

    return $r[0];
}

function getOrderUserDetail($id) {
    global $db;
    $r = $db->where("id_customer", $id)
            ->get("av_customer");

    return $r[0];
}

function getOrderPaypalPaiement($oid) {
    global $db;
    $r = $db->where("id_order", $oid)
            ->get("av_paypal_order");

    return $r[0];
}

function getOrderPayment($oref) {
    global $db;
    $r = $db->where("order_reference", $oref)
            ->get("av_order_payment");

    
    return $r[0];
}

function getUserOrdersDetail($oid) {
    global $db;
    $params = array($oid);

    $r = $db->rawQuery("SELECT a.*
        FROM av_order_detail a 
        where id_order = ? ", $params);

    return $r;
}
function getOrdersDetailAttribute($odid) {
    global $db;
    $params = array($odid);

    $r = $db->rawQuery("SELECT a.*
        FROM av_order_product_attributes a 
        where id_order_detail = ? ", $params);

    return $r;
}

?>
