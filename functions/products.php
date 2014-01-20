<?php

function getAllProductInfo() {
    global $db, $vat_rate;

    $r = $db->rawQuery("select * from av_product where active = 1 order by name asc");

    foreach ($r as $k => $p) {
        $t[$k] = getProductInfos($p["id_product"]);
    }
    return ($t);
}

function getProductInfos($pid) {
    global $db;
    $r = $db->where("id_product", $pid)
            ->get("av_product");

    $carac = getProductCaracts($pid);
    //$attributes = getProductAttributes($pid);
    $combinations = getProductCombination($pid);
    $customCombinations = getProductCustomAttribut($pid, 1);
    $images = getImages($pid);
    $cover = getImageCover($pid);
    $category = getProductCategory($r[0]["id_category"]);

    $r[0]["caracteristiques"] = $carac;
    //$r[0]["attributes"] = $attributes;
    $r[0]["combinations"] = $combinations;
    $r[0]["specific_combinations"] = $customCombinations;
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
        //print_r($attribut);
        if (is_array($attribut) && $attribut["id_attribute"] != "") {
            $o[$attribut["id_attribute"]]["name"] = $attribut["name"];
            $o[$attribut["id_attribute"]]["attributes"] = getProductAttributes($pid, $attribut["id_attribute"]);
        }
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

function getProductCustomAttribut($pid) {
    global $db;
    $o = array();

    $r = $db->rawQuery("select b.*
        from av_product_custom a , av_attributes b 
        where a.id_attribute = b.id_attribute
        and id_product = ? ", array($pid));

    foreach ($r as $k => $attribut) {
        foreach (array_keys($attribut) as $key)
            $o[$attribut["id_attribute"]][$key] = $attribut[$key];
        $o[$attribut["id_attribute"]]["items"] = getProductCustomAttributItem($pid, $attribut["id_attribute"]);
    }
    if (empty($o))
        return (null);
    return $o;
}

function getProductCustomAttributItem($pid, $piad) {
    global $db;

     $r = $db->rawQuery("select b.*
        from av_product_custom a , av_attributes_items b 
        where a.id_attributes_items = b.id_attributes_items         
        and a.id_product = ? 
        and a.id_attribute = ? 
        and b.active = 1
        order by position
        ", array($pid, $piad));     

    foreach ($r as $k => $item) {
        
        $t[$item["id_attributes_items"]] = $item;
        $t[$item["id_attributes_items"]]["item_values"] = getProductCustomAttributItemValues($item["id_attributes_items"]);
    }

    if (empty($r))
        return (null);

    return $t;
}

function getProductCustomAttributItemValues($iaid) {
    global $db;

    $r = $db->where("id_attributes_items", $iaid)
            ->get("av_attributes_items_values");

    foreach ($r as $k => $item_values) {
        $t[$item_values["id_attributes_items_values"]] = $item_values;
    }

    if (empty($r))
        return (null);

    return $t;
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

    $r = $db->rawQuery("select * 
                        from av_product 
                        where active=1 
                        and id_category = ? 
                        order by position asc", array($cid));

    $p = array();
    foreach ($r as $k => $product) {
        $p[$k] = getProductInfos($product["id_product"]);
    }
    return ($p);
}

function getProductAttributeWeight($iad) {
    global $db;

    $r = $db->where("id_product_attribute", $iad)
            ->get("av_product_attribute");

    return $r[0];
}

?>
