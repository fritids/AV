{include file='../pdf_header.tpl'}

<table>
    <tr height="100">
        <td>MERCI DE REGROUPER <br>LES VERRES PAR ZONES<br>SUR LES CHARIOTS</td>
        <td>SAS ALLOVITRES<br>1900 Avenue Paul Julien RN7<br>13100 Le Tholonet<br>email : contact@miroiteriedupaysdaix.com<br>SARL Miroiterie du Pays d'Aix - RC5522928845</td>
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
        <th width="150">Référence Client</th>
        <th width="50">Qte</th>
        <th width="250">Designation</th>        
        <th width="70">Dimension</th>
        <th width="70">&nbsp;</th>
        <th width="70">&nbsp;</th>
        <th width="70">&nbsp;</th>
        <th width="70">&nbsp;</th>
    </tr>
    
    {foreach key=key item=detail from=$orderdetail name=orderdetail}
        <tr>
            <td>{$orderinfo.address.delivery.zone}</td>
            <td>{$orderinfo.customer.lastname} {$orderinfo.customer.firstname} AV</td>
            <td>{$detail.product_quantity}</td>
            <td>{$detail.product_name}</td>
            <td>{$detail.product_width} x {$detail.product_height}</td>
            {foreach key=key2 item=attribut from=$detail.attributes}
                <td>{$attribut.attribute_name} &nbsp;{$attribut.attribute_value}</td>
            {/foreach}            
        </tr>
    {/foreach}
</table>

{include file='../pdf_footer.tpl'}