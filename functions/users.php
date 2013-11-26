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
            ->update("av_address", array("active" => 0));

    $infos["alias"] = $alias;

    createNewAdresse($infos);
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
            ->where("active", 1)
            ->get("av_address");
    if ($r)
        return $r[0];
}

function getAdresseById($aid) {
    global $db;

    $r = $db->where("id_address", $aid)
            ->get("av_address");
    return $r[0];
}

function checkUserLogin($email, $pwd) {
    global $db;

    $user = $db->where('email', $email)
            ->where('passwd', md5(_COOKIE_KEY_ . $pwd))
            ->get('av_customer');

    if (count($user) == 1) {
        @session_start();
        $_SESSION["user"] = $user[0];
        $_SESSION["is_logged"] = true;

        
        // get adresse
        $_SESSION["user"]["delivery"] = getAdresse($user[0]["id_customer"], "delivery");
        $_SESSION["user"]["invoice"] = getAdresse($user[0]["id_customer"], "invoice");
        
        
        if (empty($_SESSION["user"]["delivery"])) {
            $delivery_adresse = $_SESSION["user"]["invoice"];
            $delivery_adresse["alias"] = 'delivery';         
            $delivery_adresse["date_add"] = date("Y-m-d");
            $delivery_adresse["date_upd"] = date("Y-m-d");
            unset($delivery_adresse["id_address"]);
            
            createNewAdresse($delivery_adresse);
            
            $_SESSION["user"]["delivery"] = getAdresse($user[0]["id_customer"], "delivery");
        }      
        
        return(true);
    } else {
        return(FALSE);
    }
}

function getCustomerDetail($id) {
    global $db;
    $r = $db->where("id_customer", $id)
            ->get("av_customer");

    $r[0]["delivery"] = getAdresse($id, "delivery");
    $r[0]["invoice"] = getAdresse($id, "invoice");

    return $r[0];
}

?>
