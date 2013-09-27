<?php

function getProductInfos($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->get("av_product");

    $carac = getProductCaracts($pid);

    $r[0]["carac"] = $carac;

    return $r[0];
}

function getProductCaracts($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->get("av_product_caract");

    if (empty($r))
        return (null);

    return $r[0];
}

?>
