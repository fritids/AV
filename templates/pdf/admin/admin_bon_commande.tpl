{include file='../pdf_header.tpl'}

<table>
    <tr height="100">
        <th>
            <strong>Allovitres</strong><br>
            {$user_email}
        </th>
        <th><strong>{$supplier.name}</strong></th>
    </tr>
</table>
<br>
<br>
<br>
<table  border="1">
    <tr height="100">
        <td>{$orderinfo.customer.lastname}_AV</td>
        <td>{$orderinfo.address.delivery.zone}<br>
            Allovitres<br>
            1900 RN 7<br>
            Quartier langesse<br>
            13100 Le Tholonet<br>
        </td>        
        <td>{$smarty.now|date_format:"%d/%m/%y %R:%S"}</td>
    </tr>    
</table>
<br>
<br>
<br>
<table border="1">
    <tr>
        <th width="50">Id</th>
        <th width="250">Designation</th>
        <th width="70">Largeur</th>
        <th width="70">Longueur</th>
        <th>Attribut</th>
        <th width="50">Quantité</th>
    </tr>
    {foreach key=key item=detail from=$orderdetail name=orderdetail}
        <tr>
            <td>{$detail.id_product}</td>
            <td>{$detail.product_name}</td>
            <td>{$detail.product_width} </td>
            <td>{$detail.product_height} </td>
            <td>
                {foreach key=key2 item=attribut from=$detail.attributes}
                    {$attribut.name}<br>
                {/foreach}
            </td>
            <td>{$detail.product_quantity}</td>
        </tr>
    {/foreach}
</table>

{include file='../pdf_footer.tpl'}