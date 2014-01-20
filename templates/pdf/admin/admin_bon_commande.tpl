{include file='../pdf_header.tpl'}

<table>
    <tr height="100">
        <td>MERCI DE REGROUPER <br>LES VERRES PAR ZONES<br>SUR LES CHARIOTS</td>
        <td>{$orderinfo.address.delivery.warehouse.warehouse}</td>
    </tr>
</table>
<br>
<br>
<table>
    <tr>
        <td height="50">
            <table border="1" width="450" >
                <tr>
                    <td  height="30"><h1>BON DE COMMANDE {$supplier.name}</h1></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td height="30" width="650"  align="right">
            <table border="1" width="150">
                <tr>
                    <td>Date : {$smarty.now|date_format:"%d/%m/%y"}</td>
                </tr>
            </table> 
        </td>
    </tr>
    <tr>
        <td height="30">
            <table border="1" width="350">
                <tr>
                    <td>Interlocuteur : {$user_email}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br>
<br>
<table border="1">
    <tr>
        <th width="50">Zone</th>
        <th width="110">Référence Client</th>
        <th width="110">Com. AV</th>
        <th width="30">Qte</th>
        <th width="200">Designation</th>        
        <th width="50">Dimension</th>    
        <th width="60">Epaisseur</th>
        <th width="60">Façonnage</th>
        <th width="60">Couleur</th>
        <th width="60">Nat. verre</th>
        <th width="60">Finition</th>
        <th width="60">Trait. Verre</th>        

        {if $orderinfo.nb_custom_product > 0}
            <th width="60">Trait. manuel</th>
            {/if}
    </tr>

    {foreach key=key item=detail from=$orderdetail name=orderdetail}
        <tr>
            <td>{$orderinfo.address.delivery.zone}</td>
            <td>
            {$orderinfo.customer.lastname} {$orderinfo.customer.firstname} AV
        </td>
        <td>
            {if $detail.product_current_state == 21}SAV&nbsp;{/if}
            {if $detail.product_current_state == 22}CASSE&nbsp;{/if}
            {$detail.product_supplier_comment}
        </td>
        <td>{$detail.product_quantity}</td>
        <td>
            {$detail.product_name}
            {if $detail.is_product_custom == 1}
                <b>forme spécifique voir annexe</b>
            {/if}
        </td>
        <td>{$detail.product_width} x {$detail.product_height}</td>


        {for $i=0 to 5}
            {if isset($detail.attributes[$i].index_attribute)}
                {if $detail.attributes[$i].index_attribute == 1}
                    <td>{$detail.attributes[$i].attribute_value}</td>
                {elseif $detail.attributes[$i].index_attribute == 2}
                    <td>{$detail.attributes[$i].attribute_value}</td>                        
                {elseif $detail.attributes[$i].index_attribute == 3}
                    <td>{$detail.attributes[$i].attribute_value}</td>                        
                {elseif $detail.attributes[$i].index_attribute == 4}
                    <td>{$detail.attributes[$i].attribute_value}</td>                        
                {elseif $detail.attributes[$i].index_attribute == 5}
                    <td>{$detail.attributes[$i].attribute_value}</td>                        
                {elseif $detail.attributes[$i].index_attribute == 7}
                    <td>{$detail.attributes[$i].attribute_value}</td>
                {/if}
            {else}
                <td>&nbsp;</td>
            {/if}
        {/for}   

        {if isset($detail.is_product_custom) && $detail.is_product_custom == 1}
            <td>OUI</td>
        {/if}
    </tr>
{/foreach}
</table>

{include file='../pdf_footer.tpl'}