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

    $r = $db->rawQuery("SELECT a.*, b.name attribut_name
        FROM av_order_detail a 
        LEFT OUTER JOIN av_product_attribute b on (a.product_attribute_id = b.id_product_attribute )
        where id_order = ? ", $params);

    return $r;
}

?>
