<?php

function getUserOrders($cid) {
    global $db;
    $params = array($cid);

    $r = $db->rawQuery("select a.* , b.title statut_label
                    from av_orders a, av_order_status b
                    where a.current_state = b.id_statut 
                    and a.id_customer= ?
                    and ifnull(current_state,0) >0
                    ", $params);

    foreach ($r as $k => $order) {
        $r[$k]["details"] = getUserOrdersDetail($order["id_order"]);
    }
    return $r;
}

function getOrderInfos($oid) {
    global $db;
    $r = $db->where("id_order", $oid)
            ->get("av_orders");

    foreach ($r as $k => $order) {
        $r[$k]["details"] = getUserOrdersDetail($order["id_order"]);
        $r[$k]["notes"] = getUserOrdersDetailNotes($order["id_order"]);
        $r[$k]["history"] = getUserOrdersDetailHistory($order["id_order"]);
        $r[$k]["customer"] = getUserOrdersCustomer($order["id_customer"]);
        if ($order["id_address_invoice"])
            $r[$k]["address"]["invoice"] = getUserOrdersAddress($order["id_address_invoice"]);
        if ($order["id_address_delivery"])
            $r[$k]["address"]["delivery"] = getUserOrdersAddress($order["id_address_delivery"]);
    }
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

function getOrderPayment($oid) {
    global $db;
    $r = $db->where("id_order", $oid)
            ->get("av_order_payment");
    if ($r)
        return $r[0];


    return $r[0];
}

function getUserOrdersCustomer($cid) {
    global $db;
    $r = $db->where("id_customer", $cid)
            ->get("av_customer");
    return $r[0];
}

function getUserOrdersAddress($iaid) {
    global $db;
    $r = $db->where("id_address", $iaid)
            ->get("av_address");
    if ($r)
        return $r[0];
}

function getUserOrdersDetail($oid, $id_supplier = null) {
    global $db;
    $params = array($oid, $id_supplier, $id_supplier);

    $r = $db->rawQuery("SELECT a.*, b.name supplier_name
                        FROM av_order_detail a LEFT OUTER JOIN av_supplier b 
                        on (a.id_supplier = b.id_supplier)
                        where id_order = ? 
                        and (IFNULL(?,0) = 0 OR a.id_supplier = ?)
                        ", $params);

    foreach ($r as $k => $od) {
        $r[$k]["attributes"] = getOrdersDetailAttribute($od["id_order_detail"]);
    }
    return $r;
}

function getUserOrdersDetailHistory($oid) {
    global $db;
    $params = array($oid);
    $r = $db->rawQuery("SELECT a.id_order_detail, a.id_product, a.id_order, a.product_name,c.title product_current_state_label, b.date_add, a.id_supplier, b.supplier_name, b.bdc_filename, d.prenom
                        FROM av_order_detail a, av_order_bdc b, av_order_status c, admin_user d
                        where a.id_order_detail = b.id_order_detail
                        and  d.id_admin = b.id_user
                        and a.product_current_state = c.id_statut
                        and  a.id_order = ?
                        order by b.id_bdc desc", $params);

    return $r;
}

function getUserOrdersDetailNotes($oid) {
    global $db;
    $params = array($oid);

    $r = $db->rawQuery("SELECT a.id_order, b.date_add , c.prenom, b.message
                        FROM av_orders a, av_order_note b, admin_user c
                        where a.id_order = b.id_order
                        and c.id_admin = b.id_admin
                        and a.id_order = ?
                        order by b.id_message desc", $params);

    return $r;
}

function getOrdersDetailSupplier($oid) {
    global $db;
    $params = array($oid);

    $r = $db->rawQuery("SELECT distinct a.id_supplier, b.name, b.email
                        FROM av_order_detail a, av_supplier b 
                        where a.id_supplier = b.id_supplier 
                        and id_order = ?                         
                        ", $params);

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

function saveOrderPayment($oid, $payment) {
    global $db;
    //paiement
    $order_payment = array(
        "id_order" => $oid,
        "id_currency" => 1,
        "amount" => $_SESSION["cart_summary"]["total_amount"] + $_SESSION["cart_summary"]["total_shipping"],
        "conversion_rate" => 1,
        "payment_method" => $payment,
        "date_add" => date("Y-m-d H:i:s"),
    );

    $db->insert("av_order_payment", $order_payment);
}

function validateOrder($oid, $orderValidateInfo) {
    global $db;

    $order_validate = array(
        "current_state" => $orderValidateInfo["current_state"],
        "payment" => $orderValidateInfo["payment"]
    );

    $r = $db->where("id_order", $oid)
            ->update("av_orders", $order_validate);
}

function saveOrder() {

    global $db, $cartItems;
    $ref = getLastOrderId();

    //global de la commande
    $order_summary = array(
        "id_customer" => $_SESSION["user"]["id_customer"],
        "reference" => $ref,
        "id_address_delivery" => $_SESSION["user"]["delivery"]["id_address"],
        "id_address_invoice" => $_SESSION["user"]["invoice"]["id_address"],
        "total_paid" => $_SESSION["cart_summary"]["total_amount"] + $_SESSION["cart_summary"]["total_shipping"],
        "invoice_date" => date("Y-m-d H:i:s"),
        "delivery_date" => date("Y-m-d H:i:s"),
        "date_add" => date("Y-m-d H:i:s"),
        "date_upd" => date("Y-m-d H:i:s"),
        "order_comment" => $_SESSION["cart_summary"]["order_comment"],
    );

    $oid = $db->insert("av_orders", $order_summary);

    foreach ($cartItems as $item) {

        $p = getProductInfos($item["id"]);

        $order_detail = array(
            "id_order" => $oid,
            "id_product" => $item["id"],
            "product_name" => $item["name"],
            "product_quantity" => $item["quantity"],
            "product_price" => $item["price"],
            "product_shipping" => $item["shipping"],
            "product_width" => $item["dimension"]["width"],
            "product_height" => $item["dimension"]["height"],
            "product_depth" => $item["dimension"]["depth"],
            "product_weight" => $item["quantity"] * $p["weight"] * $item["surface"],
            "total_price_tax_incl" => $item["prixttc"] + $item["shipping"],
            "total_price_tax_excl" => $item["prixttc"] + $item["shipping"]
        );

        $odid = $db->insert("av_order_detail", $order_detail);

        // on rajoute les options
        if (isset($item["options"])) {
            foreach ($item["options"] as $k => $option) {


                $order_product_attributes = array(
                    "id_order" => $oid,
                    "id_order_detail" => $odid,
                    "id_product" => $item["id"],
                    "id_attribute" => $option["o_id"],
                    "prixttc" => $option["o_price"],
                    "name" => $option["o_name"]);

                /* $order_detail["product_attribute_id"] = $option["o_id"];
                  $order_detail["attribute_name"] = $option["o_name"];
                  $order_detail["attribute_quantity"] = $option["o_quantity"];
                  $order_detail["attribute_price"] = $option["o_price"];
                  $order_detail["attribute_shipping"] = $option["o_shipping"];
                  $order_detail["total_price_tax_incl"] += $option["o_quantity"] * $option["o_price"] + $option["o_shipping"];
                  $order_detail["total_price_tax_excl"] += $option["o_quantity"] * $option["o_price"] + $option["o_shipping"];
                 */

                $db->insert("av_order_product_attributes", $order_product_attributes);
            }
        }

        $_SESSION["id_order"] = $oid;
        $_SESSION["reference"] = $ref;
    }
}

function checkOrderPaid() {
    global $db;

    $oid = $_SESSION["id_order"];

    if ($oid) {
        $r = $db->rawQuery("select current_state from av_orders where id_order = ? ", array($oid));

        if ($r[0]["current_state"] != '') {
            unset($_SESSION["cart"]);
            unset($_SESSION["cart_summary"]);
            unset($_SESSION["id_order"]);
            unset($_SESSION["reference"]);
            $cartItems = array();
        }
    }
}

?>
