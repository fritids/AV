<?php
require_once "../configs/settings.php";
include ("header.php");
include ("../functions/products.php");
include ("../functions/orders.php");
include ("../functions/users.php");
include ("../functions/tools.php");
?>

<form action="av_download_pdf.php?all_orders" method="post"  target="blank">
    <input type="text" class="datepicker" value="" name="start_date"> 
    <input type="text" class="datepicker" value="" name="end_date"> 
    <input type="submit">
</form>


<script>
    $(function() {
        $(".datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd"
        });
    });
    
</script>
