<?php

function getDevis($did) {
    global $db;
    $r = $db->where("id_devis", $did)
            ->get("av_devis");

    foreach ($r as $k => $devis) {
        $r[$k]["customer"] = getUserDevisCustomer($devis["id_customer"]);
        $r[$k]["details"] = getUserDevisDetail($devis["id_devis"]);
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

function CreateOrder($did) {
    global $db;

    //Order 
    $r = $db->rawQuery("SELECT `id_customer`, `id_address_delivery`, `id_address_invoice`, `total_paid` FROM  av_devis a where id_devis = ? ", array($did));
    $cid = $r[0]["id_customer"];
    
    $oid = $db->insert("av_orders", $r[0]);
    $params = array("invoice_date" => date("Y-m-d H:i:s"),
        "delivery_date" => date("Y-m-d H:i:s"),
        "date_add" => date("Y-m-d H:i:s"),
        "date_upd" => date("Y-m-d H:i:s"),
        "reference" => str_pad($oid, 9, '0', STR_PAD_LEFT),
        "order_comment" => @$r[0]["devis_comment"],
        "current_state" => 2,
        "payment" => "Manuel"
    );
    $r = $db->where("id_order", $oid)
            ->update("av_orders", $params);
    
    // order detail
    $r = $db->rawQuery("SELECT `id_devis_detail`, `id_product`, ? as id_order, `product_name`, `product_quantity`, `product_price`, `product_width`, `product_height`, `product_weight`, `total_price_tax_incl`, `total_price_tax_excl`  FROM `av_devis_detail` WHERE id_devis = ?", array($oid, $did));

    foreach ($r as $details) {
        $ddid = $details["id_devis_detail"];
        unset($details["id_devis_detail"]);
        $odid = $db->insert("av_order_detail", $details);

        //les attributs
        $k = $db->rawQuery("SELECT ? as id_order, ? `id_order_detail`, `id_product`, `id_attribute`, `name`, `prixttc` FROM `av_devis_product_attributes` WHERE id_devis = ? and id_devis_detail = ?", array($oid, $odid, $did, $ddid));
        foreach ($k as $attribute) {
            $db->insert("av_order_product_attributes", $attribute);
        }
    }


    $r = $db->where("id_devis", $did)
            ->update("av_devis", array("current_state" => 4, "id_order" => $oid));

    if ($r)
        echo "<div class='alert alert-success'>La commande " . $oid . " a été créée</div>";

    return $cid;
}

?>
