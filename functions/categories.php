<?php

function getCategorieInfo($cid) {
    global $db;

    $r = $db->where("id_category", $cid)
            ->get("av_category");

    return($r[0]);
}

function getCategories() {
    global $db;

    $r = $db->where("active", 1)
            ->get("av_category");

    return($r);
}

?>
