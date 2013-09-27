<?php

function createNewAccount($infos) {
    global $db;
    $id = $db->insert("av_customer", $infos);
    return $id;
}

function updateUserAccount($infos, $uid) {
    global $db;
    unset($_SESSION["user"]); // on flush la session

    $r = $db->where("id_customer", $uid)
            ->update("av_customer", $infos);

    $user = $db->where('id_customer', $uid)
            ->get('av_customer');

    //on update avec les nouvelles vals
    $_SESSION["user"] = $user[0];
    $_SESSION["user"]["delivery"] = getAdresse($user[0]["id_customer"], "delivery");
    $_SESSION["user"]["invoice"] = getAdresse($user[0]["id_customer"], "invoice");
}

function updateUserAddress($infos, $alias, $aid) {
    global $db;
    $r = $db->where("id_address", $aid)
            ->where("alias", $alias)
            ->update("av_address", $infos);
}

function createNewAdresse($infos) {
    global $db;
    $id = $db->insert("av_address", $infos);
    return $id;
}

function getAdresse($uid, $alias) {
    global $db;

    $r = $db->where("alias", $alias)
            ->where("id_customer", $uid)
            ->get("av_address");
    return $r[0];
}

function checkUserLogin($email, $pwd) {
    global $db;
    $user = $db->where('email', $email)
            ->where('passwd', md5($pwd))
            ->get('av_customer');

    if (count($user) == 1) {
        @session_start();
        $_SESSION["user"] = $user[0];
        $_SESSION["is_logged"] = true;

        // get adresse

        $_SESSION["user"]["delivery"] = getAdresse($user[0]["id_customer"], "delivery");
        $_SESSION["user"]["invoice"] = getAdresse($user[0]["id_customer"], "invoice");


        return(true);
    } else {
        return(FALSE);
    }
}

function getUserDetail($id) {
    global $db;
    $r = $db->where("id_address", $id)
            ->get("av_customer");

    return $r[0];
}

function getUserOrders($id) {
    global $db;
    $r = $db->where("id_customer", $id)
            ->get("av_orders");

    foreach ($r as $k => $order) {
        $r[$k]["details"] = getUserOrdersDetail($order["id_order"]);
    }
    return $r;
}

function getUserOrdersDetail($oid) {
    global $db;
    $r = $db->where("id_order", $oid)
            ->get("av_order_detail");

    return $r;
}

?>
