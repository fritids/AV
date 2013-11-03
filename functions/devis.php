<?php

function getDevis($did) {
    global $db;
    $r = $db->where("id_devis", $did)
            ->get("av_devis");

    foreach ($r as $k => $devis) {
        $r[$k]["details"] = getUserDevisDetail($devis["id_devis"]);
    }
    return $r;
}

function getUserDevis($cid) {
    global $db;
    $r = $db->where("id_customer", $cid)
            ->get("av_devis");

    foreach ($r as $k => $devis) {
        $r[$k]["details"] = getUserDevisDetail($devis["id_devis"]);
    }
    return $r;
}


function getUserDevisDetail($oid) {
    global $db;
    $params = array($oid);

    $r = $db->rawQuery("SELECT a.*, b.name attribut_name
        FROM av_devis_detail a 
        LEFT OUTER JOIN av_product_attribute b on (a.product_attribute_id = b.id_product_attribute )
        where id_devis = ? ", $params);

    return $r;
}

?>
