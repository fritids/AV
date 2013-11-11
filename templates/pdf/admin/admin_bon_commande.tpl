{include file='../pdf_header.tpl'}

<table>
    <tr>
        <th>Bon de commande N°</th>
        <td>{$orderinfo.reference}</td>
        <th>Date :</th>
        <td>{$smarty.now|date_format:"%d/%m/%y"}</td>
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
<br>
<br>
<br>
{include file='../pdf_footer.tpl'}