<?php

function getPostCodeZone($postcode) {
    global $db;

    $query = "select b.nom
            from av_departements a , av_zone b
            where  a.id_zone = b.id_zone
            and  a.id_departement = ? ";

    $z = $db->rawQuery($query, array(substr($postcode, 0, 2)));

    if ($z)
        return ($z[0]["nom"]);
}

function getZoneInfos($postcode) {
    global $db;

    $query = "select b.id_zone, b.nom, b.nom zone_name, c.address warehouse, c.id_warehouse, c.name warehouse_name, c.address warehouse_address
            from av_departements a , av_zone b, av_warehouse c 
            where  a.id_zone = b.id_zone
            and b.id_warehouse = c.id_warehouse 
            and a.id_departement = ? ";

    $z = $db->rawQuery($query, array(substr($postcode, 0, 2)));

    if ($z)
        return ($z[0]);
}

function getWarehouseInfos($iwid) {
    global $db;

    $query = "select a.*
            from av_warehouse a, av_supplier_warehouse b
            where a.id_warehouse = b.id_warehouse
            and  b.id_supplier_warehouse = ? ";

    $z = $db->rawQuery($query, array($iwid));

    if ($z)
        return ($z[0]);
}

function getUserOrders($cid) {
    global $db;
    $params = array($cid);

    $r = $db->rawQuery("select a.* , b.title statut_label
                    from mv_orders a, av_order_status b
                    where a.current_state = b.id_statut 
                    and a.id_customer= ?
                    and ifnull(current_state,0) >0
                    order by a.date_add desc
                    ", $params);

    foreach ($r as $k => $order) {
        $r[$k]["details"] = getUserOrdersDetail($order["id_order"]);
    }
    return $r;
}

function getOrderInfos($oid) {
    global $db;
    $r = $db->where("id_order", $oid)
            ->get("mv_orders");

    foreach ($r as $k => $order) {
        $r[$k]["invoice"] = getUserOrdersInvoice($order["id_order"]);
        $r[$k]["details"] = getUserOrdersDetail($order["id_order"]);
        $r[$k]["notes"] = getUserOrdersDetailNotes($order["id_order"]);
        $r[$k]["history"] = getUserOrdersDetailHistory($order["id_order"]);
        $r[$k]["customer"] = getUserOrdersCustomer($order["id_customer"]);
        $r[$k]["refund"] = getUserOrdersRefund($order["id_order"]);
        if ($order["id_address_invoice"])
            $r[$k]["address"]["invoice"] = getUserOrdersAddress($order["id_address_invoice"]);
        if ($order["id_address_delivery"])
            $r[$k]["address"]["delivery"] = getUserOrdersAddress($order["id_address_delivery"]);
    }
    return $r[0];
}

function getMvOrdersInfos($oid) {
    global $db;
    $r = $db->where("id_order", $oid)
            ->get("mv_orders");
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
    return null;
}

function getUserOrdersCustomer($cid) {
    global $db;
    $r = $db->where("id_customer", $cid)
            ->get("av_customer");
    return $r[0];
}

function getUserOrdersRefund($oid) {
    global $db;
    $r = $db->where("id_order", $oid)
            ->get("av_order_refund");

    if ($r) {
        foreach ($r as $k => $rf) {
            $r[$k]["refund_detail"] = getUserOrdersRefundDetail($rf["id_order_refund"]);
        }
        return $r;
    } else {
        return null;
    }
}

function getUserOrdersRefundDetail($rid) {
    global $db;
    $r = $db->where("id_order_refund", $rid)
            ->get("av_order_refund_detail");

    return $r[0];
}

function getUserOrdersAddress($iaid) {
    global $db;
    $r = $db->where("id_address", $iaid)
            ->get("av_address");

    if ($r[0]["postcode"]) {
        $r[0]["zone"] = getPostCodeZone($r[0]["postcode"]);
        $r[0]["warehouse"] = getZoneInfos($r[0]["postcode"]);
    }
    if ($r)
        return $r[0];
}

function getUserOrdersDetail($oid, $id_supplier_warehouse = null) {
    global $db;
    $params = array($oid, $id_supplier_warehouse, $id_supplier_warehouse);

    $r = $db->rawQuery("SELECT a.*, c.title product_state_label, b.name supplier_name, a.id_supplier_warehouse
                        FROM av_order_detail a                        
                        LEFT OUTER JOIN av_order_status c on (a.product_current_state = c.id_statut)                        
                        LEFT OUTER JOIN av_supplier_warehouse d on (a.id_supplier_warehouse = d.id_supplier_warehouse)                                                
                        LEFT OUTER JOIN av_supplier b on (d.id_supplier = b.id_supplier)                        
                        where id_order = ? 
                        and (IFNULL(?,0) = 0 OR (a.id_supplier_warehouse = ? and IFNULL(product_current_state,0) in (0,21, 22)))                        
                        ", $params);

    foreach ($r as $k => $od) {
        $w = getWarehouseInfos($od["id_supplier_warehouse"]);

        $r[$k]["attributes"] = getOrdersDetailAttribute($od["id_order_detail"]);
        $r[$k]["custom"] = getOrdersCustomMainItem($od["id_order_detail"]);
        $r[$k]["id_warehouse"] = $w["id_warehouse"];
    }
    return $r;
}

function getOrdersDetail($odid) {
    global $db;
    $params = array($odid);

    $r = $db->rawQuery("SELECT a.*, c.title product_state_label, b.name supplier_name
                        FROM av_order_detail a
                        LEFT OUTER JOIN av_supplier b on (a.id_supplier = b.id_supplier)                        
                        LEFT OUTER JOIN av_order_status c on (a.product_current_state = c.id_statut)
                        where id_order_detail = ?
                        ", $params);

    foreach ($r as $k => $od) {
        $r[$k]["attributes"] = getOrdersDetailAttribute($od["id_order_detail"]);
        $r[$k]["custom"] = getOrdersCustomMainItem($od["id_order_detail"]);
    }
    return $r;
}

function getUserOrdersInvoice($oid) {
    global $db;

    $r = $db->where("id_order", $oid)
            ->get("av_order_invoice");

    $r[0]["ref_invoice"] = str_pad($r[0]["id_order_invoice"], 9, '0', STR_PAD_LEFT);

    if ($r)
        return $r[0];
    return null;
}

function getUserOrdersDetailHistory($oid) {
    global $db;
    $params = array($oid);
    $r = $db->rawQuery("SELECT distinct b.date_add, b.category, b.id_order, b.supplier_name, b.bdc_filename, d.prenom
                        FROM av_order_bdc b, admin_user d
                        where d.id_admin = b.id_user
                        and  b.id_order = ?
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

    $r = $db->rawQuery("SELECT distinct a.id_supplier_warehouse, b.id_supplier, c.name, c.email
                        FROM av_order_detail a, av_supplier_warehouse b, av_supplier c
                        where a.id_supplier_warehouse = b.id_supplier_warehouse
                        and b.id_supplier = c.id_supplier
                        and a.id_order = ?  
                        order by a.id_supplier_warehouse, a.id_supplier
                        ", $params);

    return $r;
}

function getOrdersDetailAttribute($odid) {
    global $db;
    $params = array($odid);

    $r = $db->rawQuery("SELECT c.id_attribute index_attribute,a.*, a.name attribute_value, b.weight, c.name attribute_name
                        FROM av_order_product_attributes a , av_product_attribute b, av_attributes c
                        where a.id_attribute = b.id_product_attribute
                        and b.id_attribute = c.id_attribute 
                        and  id_order_detail = ?  
                        order by c.id_attribute", $params);

    return $r;
}

function getOrdersCustomMainItem($odid) {
    global $db;
    $params = array($odid);

    $r = $db->rawQuery("SELECT distinct a.id_order_detail, a.id_attribute, b.name as main_item_name 
                        FROM av_order_product_custom a , av_attributes b
                        where a.id_attribute = b.id_attribute
                        and  id_order_detail = ?  ", $params);

    foreach ($r as $k => $od) {
        $r[$k]["sub_item"] = getOrdersCustomSubItem($od["id_order_detail"], $od["id_attribute"]);
    }

    return $r;
}

function getOrdersCustomSubItem($odid, $iaid) {
    global $db;
    $params = array($odid, $iaid);

    $r = $db->rawQuery("SELECT distinct a.id_order_detail, a.id_attributes_items, c.name as sub_item_name , c.picture 
                        FROM av_order_product_custom a , av_attributes_items c
                        where a.id_attributes_items = c.id_attributes_items                         
                        and a.id_order_detail = ?  
                        and a.id_attribute = ?", $params);

    foreach ($r as $k => $od) {
        $r[$k]["item_values"] = getOrdersCustomItemValues($od["id_order_detail"], $iaid, $od["id_attributes_items"]);
    }

    return $r;
}

function getOrdersCustomItemValues($odid, $iaid, $iaiid) {
    global $db;
    $params = array($odid, $iaid, $iaiid);

    $r = $db->rawQuery("SELECT a.*, d.name as item_value_name
                        FROM av_order_product_custom a , av_attributes_items_values d
                        where a.id_attributes_items_values = d.id_attributes_items_values 
                        and a.id_order_detail = ?  
                        and a.id_attribute = ?
                        and a.id_attributes_items = ?
                        ", $params);

    foreach ($r as $k => $od) {
        $r[$k] = $od;
    }
    return $r;
}

function saveOrderPayment($oid, $payment) {
    global $db;
//paiement
    $order_payment = array(
        "id_order" => $oid,
        "id_currency" => 1,
        "amount" => $_SESSION["cart_summary"]["total_amount"] + $_SESSION["cart_summary"]["total_shipping"] - $_SESSION["cart_summary"]["total_discount"],
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

    updQuantity($oid);

// on retire 1 à nombre coupon utilisable
    if (isset($_SESSION["cart_summary"]["discount_code"]) != "") {
        $code = $_SESSION["cart_summary"]["discount_code"];
        $cid = $_SESSION["user"]["id_customer"];
        updVoucherCodeQty($code, $cid);
    }
}

function createInvoice($oid) {
    global $db;

    $params = array(
        "id_order" => $oid,
        "invoice_date" => date("y-m-d H:i:s")
    );

    $r = $db->insert("av_order_invoice", $params);
}

function getOptionWeight($ipaid) {
    global $db;
    $r = $db->where("id_product_attribute", $ipaid)
            ->get("av_product_attribute");
    return $r[0];
}

function saveOrder() {

    global $db, $cartItems, $config;
//$ref = getLastOrderId();
    $alert_sms = 0;
    $is_product_custom = 0;
    $nb_product = 0;
    $nb_custom_product = 0;
    $alert_sms_phone = "";

    if ($_SESSION["cart_summary"]["order_option"] == "SMS") {
        $alert_sms = 1;
        $alert_sms_phone = $_SESSION["cart_summary"]["alert_sms_phone"];
    }
//global de la commande
    $order_summary = array(
        "id_customer" => $_SESSION["user"]["id_customer"],
        "id_address_delivery" => $_SESSION["user"]["delivery"]["id_address"],
        "id_address_invoice" => $_SESSION["user"]["invoice"]["id_address"],
        "total_paid" => $_SESSION["cart_summary"]["total_amount"] + $_SESSION["cart_summary"]["total_shipping"] - $_SESSION["cart_summary"]["total_discount"],
        "invoice_date" => date("Y-m-d H:i:s"),
        "delivery_date" => date("Y-m-d H:i:s"),
        "date_add" => date("Y-m-d H:i:s"),
        "order_comment" => $_SESSION["cart_summary"]["order_comment"],
        "vat_rate" => ( $config["vat_rate"] - 1 ) * 100,
        "alert_sms" => $alert_sms,
        "alert_sms_phone" => $alert_sms_phone,
        "total_discount" => @$_SESSION["cart_summary"]["total_discount"],
        "order_voucher" => @$_SESSION["cart_summary"]["discount_code"]
    );

    $oid = $db->insert("av_orders", $order_summary);

    $r = $db->where("id_order", $oid)
            ->update("av_orders", array("reference" => str_pad($oid, 9, '0', STR_PAD_LEFT)));

    foreach ($cartItems as $item) {

        $nb_product++;
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
            "total_price_tax_incl" => $item["prixttc"] + $item["shipping"],
            "total_price_tax_excl" => $item["prixttc"] + $item["shipping"]
        );

        if (isset($item["discount"])) {
            $order_detail["discount"] = $item["discount"];
            $order_detail["voucher_code"] = $item["voucher_code"];
            $order_detail["total_price_tax_incl"] = $item["prixttc"] + $item["shipping"] - $item["discount"];
            $order_detail["total_price_tax_excl"] = $item["prixttc"] + $item["shipping"] - $item["discount"];
        }

        $odid = $db->insert("av_order_detail", $order_detail);

// on rajoute les options
        if (isset($item["options"])) {
            $option_unit_weight = 0;
            $option_weight = 0;
            $options_weight = 0;
            foreach ($item["options"] as $k => $option) {
                $option_unit_weight = getOptionWeight($option["o_id"]);
                $o_surface = $option["o_surface"];
                $option_weight = $o_surface * $option_unit_weight["weight"];
                $options_weight = $option_weight;

                $order_product_attributes = array(
                    "id_order" => $oid,
                    "id_order_detail" => $odid,
                    "id_product" => $item["id"],
                    "id_attribute" => $option["o_id"],
                    "prixttc" => $option["o_price"],
                    "name" => $option["o_name"],
                    "weight" => $option_weight
                );

                $db->insert("av_order_product_attributes", $order_product_attributes);
            }
        }
// on rajoute les options personalisé
        if (isset($item["custom"])) {
            foreach ($item["custom"] as $k => $main_attribute) {
                $nb_custom_product += 1;
                if (is_array($main_attribute)) {
                    $order_custom_attributes["id_attribute"] = $k;
                    foreach ($main_attribute as $l => $sub_attribute) {
                        if (is_array($sub_attribute)) {
                            $order_custom_attributes["id_attributes_items"] = $l;

                            foreach ($sub_attribute as $m => $item_values) {
                                if (is_array($item_values)) {
                                    foreach ($item_values as $n => $item_value) {
                                        $order_custom_attributes["id_attributes_items_values"] = $n;
                                        $order_custom_attributes["id_order"] = $oid;
                                        $order_custom_attributes["id_order_detail"] = $odid;
                                        $order_custom_attributes["id_product"] = $item["id"];
                                        $order_custom_attributes["custom_value"] = $item_value;

                                        $db->insert("av_order_product_custom", $order_custom_attributes);
                                        $is_product_custom = 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
// post update sur les details
            $r = $db->where("id_order_detail", $odid)
                    ->update("av_order_detail", array("is_product_custom" => $is_product_custom));
        }

// post update sur les details
        $param = array(
            "product_weight" => ($p["weight"] * $item["surface"] + $option_weight)
        );
        $r = $db->where("id_order_detail", $odid)
                ->update("av_order_detail", $param);

// post update global
        $param = array(
            "nb_product" => $nb_product - $nb_custom_product,
            "nb_custom_product" => $nb_custom_product
        );

        $r = $db->where("id_order", $oid)
                ->update("av_orders", $param);

        $_SESSION["id_order"] = $oid;
        $_SESSION["reference"] = str_pad($oid, 9, '0', STR_PAD_LEFT);
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

function splitOrderDetail($odid, $qty_request) {
    global $db;

    $r = $db->where("id_order_detail", $odid)
            ->get("av_order_detail");

    $coef = $qty_request / $r[0]["product_quantity"];

    $oid = $r[0]["id_order"];
    $init_qte = $r[0]["product_quantity"];
    $init_ttc = $r[0]["total_price_tax_incl"];
    $init_excl = $r[0]["total_price_tax_incl"];

    $r[0]["product_quantity"] = $r[0]["product_quantity"] * $coef;
    $r[0]["total_price_tax_incl"] = $r[0]["total_price_tax_incl"] * $coef;
    $r[0]["total_price_tax_excl"] = $r[0]["total_price_tax_excl"] * $coef;

    unset($r[0]["id_order_detail"]);

    $new_odid = $db->insert("av_order_detail", $r[0]);

    if ($r) {
        $params = array(
            "product_quantity" => $init_qte * (1 - $coef),
            "total_price_tax_incl" => $init_ttc * (1 - $coef),
            "total_price_tax_excl" => $init_excl * (1 - $coef),
        );

        $r = $db->where("id_order_detail", $odid)
                ->update("av_order_detail", $params);
    }

//les attributs
    $r = $db->where("id_order_detail", $odid)
            ->get("av_order_product_attributes");
    foreach ($r as $attribut) {
        unset($attribut["id_order_prd_attr"]);
        $attribut["id_order_detail"] = $new_odid;
        $prd_aid = $db->insert("av_order_product_attributes", $attribut);
    }
    return $oid;
}

function updQuantity($oid) {
    global $db;

    $r = $db->where("id_order", $oid)
            ->get("av_order_detail");

    foreach ($r as $item) {

        $qte_ordered = $item["product_quantity"];
        $pid = $item["id_product"];

        $p = $db->where("id_product", $pid)
                ->get("av_product");

        $params = array("quantity" => $p[0]["quantity"] - $qte_ordered);

        $r = $db->where("stock_tracking", 1)
                ->where("id_product", $pid)
                ->update("av_product", $params);
    }
}

function updQuantityWarehouse($odid, $sns = 0) {
    global $db;
    $stock = false;

    $r = $db->where("id_order_detail", $odid)
            ->where("is_debit_stock", $sns)
            ->get("av_order_detail");

    foreach ($r as $item) {

        if ($sns == 1)
            $item["product_quantity"] *=-1; // on remet en stock; 

        $qte_ordered = $item["product_quantity"];
        $pid = $item["id_product"];
        $id_sw = $item["id_supplier_warehouse"];


        $s = $db->where("id_supplier_warehouse", $id_sw)
                ->get("av_supplier_warehouse");

        if ($s) {
            $id_warehouse = $s[0]["id_warehouse"];

            $p = $db->where("id_product", $pid)
                    ->where("id_warehouse", $id_warehouse)
                    ->get("av_product_warehouse");

            if ($p) {
                $id_product_warehouse = $p[0]["id_product_warehouse"];
                $new_qte = $p[0]["quantity"] - $qte_ordered;
                $params = array("quantity" => $new_qte);

                $r = $db->where("id_product_warehouse", $id_product_warehouse)
                        ->where("id_product", $pid)
                        ->update("av_product_warehouse", $params);

                if ($r) {
                    $params = array(
                        "id_order_detail" => $odid,
                        "date_add" => date("Y-m-d H:i:s"),
                        "id_product_warehouse" => $id_product_warehouse,
                        "old_quantity" => $p[0]["quantity"],
                        "quantity" => -$qte_ordered
                    );

                    $db->insert("av_product_warehouse_hist", $params);

                    $db->where("id_order_detail", $odid)
                            ->update("av_order_detail", array("is_debit_stock" => 1));
                    $stock = true;
                }
            }
        }
    }
    return($stock);
}

function refundOrder($oid, $payment = "Carte credit", $refund_comment = "", $is_suppl_shipping = 0, $refund_suppl = 0, $refundDetailList = array()) {
    global $db;

    $o = $db->where("id_order", $oid)
            ->get("av_orders");

    $now = date("Y-m-d H:i:s");
    $orig_total_refund = $o[0]["total_paid"];
    $total_refund = 0;
    $shipping = 25;

    if (count($refundDetailList) > 0)
        $shipping = 0;

    if ($is_suppl_shipping) {
        $shipping = $refund_suppl;
        $refund_suppl = 0;
    } else {
        $orig_total_refund += $refund_suppl;
    }

    $refundParam = array(
        "id_order" => $o[0]["id_order"],
        "date_add" => $now,
        "date_order" => $o[0]["date_add"],
        "date_refund" => $now,
        "payment" => $payment,
        "id_customer" => $o[0]["id_customer"],
        "total_shipping" => $shipping,
        "total_refund" => $orig_total_refund,
        "vat_rate" => $o[0]["vat_rate"],
        "refund_comment" => $refund_comment
    );

    $ior = $db->insert("av_order_refund", $refundParam);

    if ($ior) {
        $did_query = "";

        $db->where("id_order", $oid);
        if (count($refundDetailList) > 0) {
            $oid_list = implode(",", $refundDetailList);
            $did_query = " AND id_order_detail in (" . $oid_list . ")";
        }
        $query = "select * from av_order_detail where id_order = ? " . $did_query;

        $od = $db->rawQuery($query, array($oid));

        foreach ($od as $detail) {
            $total_refund+= $detail["total_price_tax_incl"];
            $refundDetail = array(
                "id_order_refund" => $ior,
                "id_order_detail" => $detail["id_order_detail"],
                "id_product" => $detail["id_product"],
                "id_supplier_warehouse" => $detail["id_supplier_warehouse"],
                "date_add" => $now,
                "product_name" => $detail["product_name"],
                "product_quantity" => $detail["product_quantity"],
                "product_price" => $detail["product_price"],
                "product_width" => $detail["product_width"],
                "product_height" => $detail["product_height"],
                "product_weight" => $detail["product_weight"],
                "total_price_tax_incl" => $detail["total_price_tax_incl"],
                "total_price_tax_excl" => $detail["total_price_tax_excl"],
                "is_product_custom" => $detail["is_product_custom"],
                "is_debit_stock" => $detail["is_debit_stock"],
            );

            updQuantityWarehouse($detail["id_order_detail"], $detail["is_debit_stock"]);

            //on retire de la tournee
            $db->where("id_order_detail", $detail["id_order_detail"])
                    ->delete("av_tournee");

            $db->where("id_order_detail", $detail["id_order_detail"])
                    ->update("av_order_detail", array("product_current_state" => 23));

            $db->insert("av_order_refund_detail", $refundDetail);
        }

        $total_refund += $shipping + $refund_suppl;
        if ($total_refund != $orig_total_refund) {
            $db->where("id_order_refund", $ior)
                    ->update("av_order_refund", array("total_refund" => $total_refund));
        }

        return $ior;
    }
    return false;
}

?>
