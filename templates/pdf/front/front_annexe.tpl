
<body style="margin: 0; padding: 0; font-family:Arial, Helvetica, sans-serif; font-size:15px;">
    <table width="645" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="87">&nbsp;</td>
                        <td width=""><h2>Annexe - FA{$orderinfo.invoice}</h2><br> le {$orderinfo.date_add|date_format:"%d/%m/%y"}</td>
                    </tr>                    
                    <tr>
                        <td height="120">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>                    
                    <tr>
                        <td height="40">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" width="640">
                            <table width="620" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="20%" style="padding:3px;" bgcolor="#f5f5f5"></td>
                                    <td width="80%" style="padding:3px;" bgcolor="#f5f5f5">Description</td>                                    
                                </tr>

                                {foreach key=key item=detail from=$orderinfo.details}
                                    {if $detail.is_product_custom == 1}
                                        <tr>
                                            <td style=" border-bottom:1px #000000 solid; padding:3px;">
                                                {foreach from=$detail.custom item=custom}
                                                    {foreach from=$custom.sub_item item=sub_item}
                                                        {if (isset($sub_item.picture)) && {$sub_item.picture} != ""}
                                                            <img src="img/f/{$sub_item.picture}">
                                                        {/if}
                                                    {/foreach} 
                                                {/foreach} 
                                            </td>

                                            <td style=" border-bottom:1px #000000 solid; padding:3px;">
                                                {$detail.product_name}<br>
                                                {if $detail.attributes}
                                                    {foreach key=key item=attribute from=$detail.attributes}
                                                        {$attribute.attribute_name}: {$attribute.attribute_value} <br>
                                                    {/foreach}
                                                {/if}

                                                {if isset($detail.custom)} 
                                                    {foreach from=$detail.custom item=custom}
                                                        {$custom.main_item_name}                                                    
                                                        {foreach from=$custom.sub_item item=sub_item}
                                                            {$sub_item.sub_item_name} <br>

                                                            {foreach from=$sub_item.item_values item=item_value}
                                                                {$item_value.item_value_name}: {$item_value.custom_value} <br>
                                                            {/foreach} 
                                                        {/foreach} 
                                                    {/foreach} 
                                                {/if}
                                            </td>                                        
                                        </tr>
                                    {/if}
                                {/foreach}

                            </table>    

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
