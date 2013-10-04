<?php

function getCategorieInfo($cid) {
    global $db;

    $r = $db->where("id_category", $cid)
            ->get("av_category");

    return($r[0]);
}

function getCategories() {
    global $db;

    $r = $db->get("av_category");

    return($r);
}

?>
