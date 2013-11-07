<?php

function getProductInfos($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->get("av_product");

    $carac = getProductCaracts($pid);
    $attributes = getProductAttributes($pid);
    $images = getImages($pid);
    $cover = getImageCover($pid);
    $category = getProductCategory($r[0]["id_category"]);

    $r[0]["caracteristiques"] = $carac;
    $r[0]["attributes"] = $attributes;
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
