<?php

function getDevis($did) {
    global $db;
    $r = $db->where("id_devis", $did)
            ->get("av_devis");

    foreach ($r as $k => $devis) {
        $r[$k]["customer"] = getUserDevisCustomer($devis["id_customer"]);
        $r[$k]["details"] = getUserDevisDetail($devis["id_devis"]);
        if ($devis["id_address_invoice"])
            $r[$k]["address"]["invoice"] = getUserOrdersAddress($devis["id_address_invoice"]);
        if ($devis["id_address_delivery"])
            $r[$k]["address"]["delivery"] = getUserOrdersAddress($devis["id_address_delivery"]);
    }
    return $r;
}

function getUserDevisCustomer($cid) {
    global $db;
    $r = $db->where("id_customer", $cid)
            ->get("av_customer");
    return $r[0];
}

function getUserDevis($cid) {
    global $db;


    $r = $db->rawQuery("select a.*, b.nom, b.prenom 
                                from av_devis a , admin_user b 
                                where a.id_user = b.id_admin 
                                and id_customer = ?", array($cid));

    foreach ($r as $k => $devis) {
        $r[$k]["details"] = getUserDevisDetail($devis["id_devis"]);
    }
    return $r;
}

function getUserDevisDetail($oid) {
    global $db;
    $params = array($oid);

    $r = $db->rawQuery("SELECT a.*
        FROM av_devis_detail a         
        where id_devis = ? ", $params);

    foreach ($r as $k => $devisdetail) {
        $r[$k]["combinations"] = getUserDevisProductAttributs($devisdetail["id_devis_detail"]);
    }

    return $r;
}

function getUserDevisProductAttributs($ddid) {
    global $db;
    $params = array($ddid);

    $r = $db->rawQuery("SELECT a.*
        FROM  av_devis_product_attributes a         
        where id_devis_detail = ? ", $params);

    return $r;
}

function CreateOrder($did, $payment) {
    global $db, $config;

    //Order 
    $r = $db->rawQuery("SELECT `id_customer`, `id_address_delivery`, `id_address_invoice`, `total_paid` FROM  av_devis a where id_order is null and id_devis = ? ", array($did));
    if ($r) {
        $cid = $r[0]["id_customer"];
        //shipping
        $r[0]["total_paid"] = $r[0]["total_paid"] * $config["vat_rate"] + 25;
        $total_paid = $r[0]["total_paid"];
        $nb_product = 0;

        $oid = $db->insert("av_orders", $r[0]);
        
        $params = array("invoice_date" => date("Y-m-d H:i:s"),
            "delivery_date" => date("Y-m-d H:i:s"),
            "date_add" => date("Y-m-d H:i:s"),
            "date_upd" => date("Y-m-d H:i:s"),
            "reference" => str_pad($oid, 9, '0', STR_PAD_LEFT),
            "order_comment" => @$r[0]["devis_comment"],
            "current_state" => 2,
            "vat_rate" => ($config["vat_rate"] - 1) * 100,
            "payment" => $payment
        );
        $r = $db->where("id_order", $oid)
                ->update("av_orders", $params);

        //paiement
        $order_payment = array(
            "id_order" => $oid,
            "order_reference" => str_pad($oid, 9, '0', STR_PAD_LEFT),
            "id_currency" => 1,
            "amount" => $total_paid,
            "conversion_rate" => 1,
            "payment_method" => $payment,
            "date_add" => date("Y-m-d H:i:s"),
        );

        $db->insert("av_order_payment", $order_payment);
        
        // order detail
        $r = $db->rawQuery("SELECT `id_devis_detail`, `id_product`, ? as id_order, `product_name`, `product_quantity`, `product_price`, `product_width`, `product_height`, `product_weight`, `total_price_tax_incl`, `total_price_tax_excl`  FROM `av_devis_detail` WHERE id_devis = ?", array($oid, $did));

        foreach ($r as $details) {
            $nb_product++;
            $ddid = $details["id_devis_detail"];
            unset($details["id_devis_detail"]);
            $details["product_price"] *= $config["vat_rate"];
            $details["total_price_tax_incl"] *= $config["vat_rate"];

            $odid = $db->insert("av_order_detail", $details);

            //les attributs
            $k = $db->rawQuery("SELECT ? as id_order, ? `id_order_detail`, `id_product`, `id_attribute`, `name`, `prixttc` FROM `av_devis_product_attributes` WHERE id_devis = ? and id_devis_detail = ?", array($oid, $odid, $did, $ddid));
            foreach ($k as $attribute) {
                $db->insert("av_order_product_attributes", $attribute);
            }
        }


        $r = $db->where("id_order", $oid)
                ->update("av_orders", $params = array("nb_product" => $nb_product));

        $r = $db->where("id_devis", $did)
                ->update("av_devis", array("current_state" => 4, "id_order" => $oid));

        createInvoice($oid);

        if ($r)
            echo "<div class='alert text-center alert-success'>La commande " . $oid . " a été créée</div>";
        return $cid;
    }else {
        echo "<div class='alert  text-center  alert-warning'>Ce devis a déjà été commandé</div>";
    }
}

?>
