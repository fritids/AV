<?php

function getProductInfos($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->get("av_product");

    $carac = getProductCaracts($pid);
    //$attributes = getProductAttributes($pid);
    $combinations = getProductCombination($pid);
    $images = getImages($pid);
    $cover = getImageCover($pid);
    $category = getProductCategory($r[0]["id_category"]);

    $r[0]["caracteristiques"] = $carac;
    //$r[0]["attributes"] = $attributes;
    $r[0]["combinations"] = $combinations;
    $r[0]["images"] = $images;
    $r[0]["cover"] = $cover;
    $r[0]["category"] = $category;

    return $r[0];
}

function getProductInfosByName($name) {
    global $db;
    $r = $db->where("name", $name)
            ->get("av_product");

    if (!empty($r)) {
        $pid = $r[0]["id_product"];

        $carac = getProductCaracts($pid);
        $attributes = getProductAttributes($pid);
        $images = getImages($pid);
        $cover = getImageCover($pid);

        $r[0]["caracteristiques"] = $carac;
        $r[0]["attributes"] = $attributes;
        $r[0]["images"] = $images;
        $r[0]["cover"] = $cover;
        return $r[0];
    }
    return null;
}

function getProductCaracts($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->get("av_product_caract");

    if (empty($r))
        return (null);

    return $r;
}

function getProductCategory($cid) {
    global $db;
    $r = $db->where("id_category", $cid)
            ->get("av_category");

    if (empty($r))
        return (null);

    return $r[0];
}

function getProductCombination($pid) {
    global $db;
    $o = array();

    $r = $db->rawQuery("select distinct a.id_attribute, b.name 
        from av_product_attribute a , av_attributes b 
        where a.id_attribute = b.id_attribute 
        and id_product = ? ", array($pid));


    foreach ($r as $k => $attribut) {
        $o[$attribut["id_attribute"]]["name"] = $attribut["name"];
        $o[$attribut["id_attribute"]]["attributes"] = getProductAttributes($pid, $attribut["id_attribute"]);
    }

    if (empty($o))
        return (null);

    return $o;
}

function getProductAttributes($pid, $paid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->where("id_attribute", $paid)
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

function getImages($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->where("cover", 0)
            ->get("av_product_images");
    if (empty($r))
        return (null);
    return $r;
}

function getImageCover($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->where("cover", 1)
            ->get("av_product_images");
    if (empty($r))
        return (null);
    return $r[0];
}

function getProductByCategorie($cid) {
    global $db;
    $r = $db->where("id_category", $cid)
            ->get("av_product");
    $p = array();
    foreach ($r as $k => $product) {
        $p[$k] = getProductInfos($product["id_product"]);
    }
    return ($p);
}

?>
