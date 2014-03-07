<?php

function getVoucherCode($code = "", $cid) {
    global $db;

    $r = $db->rawQuery("select * 
                        from av_voucher 
                        where active = 1
                        and now() between start_date and end_date
                        and quantity > 0
                        and (ifnull(?, 0) = 0 or code = ?)
                        and (id_customer = 0 or id_customer = ?)                        
                        ", array($code, $code, $cid));


    if ($r) {
        foreach ($r as $voucher) {
            if ($voucher["code"] == $code) {
                $voucher["nb_customer_used"] = 0;
                if ($voucher["id_customer"] == 0) {
                    $voucher["nb_customer_used"] = getNbVoucherUsed($code, $cid);
                }
                return $voucher;
            }
        }
    }
}

function updVoucherCodeQty($code, $cid) {
    global $db;

    $voucher = getVoucherCode($code, $cid);
    $qty = $voucher["quantity"] - 1;

    $r = $db->where("id_voucher", $voucher["id_voucher"])
            ->update("av_voucher", array("quantity" => $qty));
}

function getNbVoucherUsed($code, $cid) {
    global $db;
    $r = $db->rawQuery("select count(1) nb_used "
            . "from av_orders "
            . "where id_customer = ? "
            . "and order_voucher = ? "
            . "and current_state in (1,2,3,4,5,10)", array($cid, $code));

    return $r[0]["nb_used"];
}

?>
