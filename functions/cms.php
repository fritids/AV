<?php

function getCmsInfo($id) {
    global $db;

    $r = $db->where("id_cms", $id)
            ->get("av_cms_lang");

    return($r[0]);
}

function getAllCmsInfo() {
    global $db;

    $r = $db->get("av_cms_lang");

    return($r);
}

?>
