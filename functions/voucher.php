<?php

function getVoucherCode($code = "", $cid) {
    global $db;

    $r = $db->rawQuery("select * 
                        from av_voucher 
                        where active = 1
                        and now() between start_date and end_date
                        and quantity > 0
                        and (ifnull(?, 0) = 0 or code = ?)
                        and id_customer = ?
                        ", array($code, $code, $cid));

    if ($r)
        return $r[0];
}

function updVoucherCodeQty($code, $cid) {
    global $db;

    $voucher = getVoucherCode($code, $cid);
    $qty = $voucher["quantity"] - 1;

    $r = $db->where("code", $code)
            ->where("id_customer", $cid)
            ->update("av_voucher", array("quantity" => $qty));
}

?>
