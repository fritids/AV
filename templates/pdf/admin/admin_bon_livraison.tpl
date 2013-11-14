{include file='../pdf_header.tpl'}

<table>
    <tr>
        <th>Bon de livraison N°</th>
        <td>{$orderinfo.reference}</td>
        <th>Date :</th>
        <td>{$smarty.now|date_format:"%d/%m/%y"}</td>
    </tr>
</table>
<table>
    <tr>
        <td>Livraison</td>
        <td>Facturation</td>
    </tr>
    <tr>
        <td>
            {$orderinfo.customer.lastname} {$orderinfo.customer.firstname}<br>
            {$orderinfo.address.delivery.address1}<br>
            {$orderinfo.address.delivery.address2}<br>
            {$orderinfo.address.delivery.postcode} {$orderinfo.address.delivery.city}
        </td>
        <td>
            {$orderinfo.customer.lastname} {$orderinfo.customer.firstname}<br>
            {$orderinfo.address.invoice.address1}<br>
            {$orderinfo.address.invoice.address2}<br>
            {$orderinfo.address.invoice.postcode} {$orderinfo.address.invoice.city}
        </td>
    </tr>
</table>
<br>
<br>
<br>
<table>
    <tr>
        <td>Commande n° {$orderinfo.reference}</td>
        <td>Transporteur : <br> LIVRAISON A DOMICILE ALLOVITRES</td>
        <td>Méthode de paiement :<br>{$orderinfo.payment}</td>
    </tr>
</table>

<table border="1">
    <tr>
        <th width="250">Designation</th>
        <th width="70">Largeur</th>
        <th width="70">Longueur</th>
        <th width="50">Quantité</th>
    </tr>
    {foreach key=key item=detail from=$orderdetails}
        <tr>
            <td>{$detail.product_name}</td>
            <td>{$detail.product_width} </td>
            <td>{$detail.product_height} </td>                      
            <td>{$detail.product_quantity}</td>
        </tr>
    {/foreach}
</table>
<br>
<br>
<br>
{include file='../pdf_footer.tpl'}