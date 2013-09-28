<?php

function getProductInfos($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->get("av_product");

    $carac = getProductCaracts($pid);
    $attributes = getProductAttributes($pid);

    $r[0]["caracteristiques"] = $carac;
    $r[0]["attributes"] = $attributes;

    return $r[0];
}

function getProductCaracts($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->get("av_product_caract");

    if (empty($r))
        return (null);

    return $r;
}

function getProductAttributes($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->get("av_product_attribute");    
    
    foreach ($r as $k => $values) {
        $t[$values["id_product_attribute"]] = $values;
    }

    if (empty($r))
        return (null);

    return $t;
}
function getAttributes($aid) {
    global $db;
    $r = $db->where("id_product_attribute", $aid)
            ->get("av_product_attribute");    
       
    if (empty($r))
        return (null);

    return $r[0];
}

?>
