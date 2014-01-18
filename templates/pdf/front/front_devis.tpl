
<body style="margin: 0; padding: 0; font-family:Arial, Helvetica, sans-serif; font-size:15px;">
    <table width="645" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="87">&nbsp;</td>
                        <td width=""><h2>Devis DE{$devisinfo.id_devis}</h2><br> le {$devisinfo.date_add|date_format:"%d/%m/%y"}</td>
                    </tr>
                    <tr>
                        <td>
                            {$devisinfo.customer.lastname} {$devisinfo.customer.firstname}<br>
                            {$devisinfo.address.delivery.address1}<br>
                            {$devisinfo.address.delivery.address2}<br>
                            {$devisinfo.address.delivery.postcode} {$devisinfo.address.delivery.city}
                        </td>
                        <td width="">{$devisinfo.customer.lastname} {$devisinfo.customer.firstname}<br>
                            {$devisinfo.address.invoice.address1}<br>
                            {$devisinfo.address.invoice.address2}<br>
                            {$devisinfo.address.invoice.postcode} {$devisinfo.address.invoice.city}
                        </td>
                    </tr>
                    <tr>
                        <td height="120">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="640" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #000;" >
                                <tr>
                                    <td colspan="2" bgcolor="#f5f5f5" style="padding:5px; border:1px solid #000;">Devis n°{$devisinfo.id_devis}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #000;padding:2px; ">Devis n° {$devisinfo.id_devis}</td>
                                    <td style="border:1px solid #000;padding:2px; ">LIVRAISON A DOMICILE ALLOVITRES</td>                                    
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="40">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" width="640">
                            <table width="620" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="66%" style="padding:3px;" bgcolor="#f5f5f5">Description</td>
                                    <td width="14%" bgcolor="#f5f5f5">Dimension</td>
                                    {*<td width="14%" bgcolor="#f5f5f5">Référence</td>*}
                                    <td width="10%" bgcolor="#f5f5f5">Qté</td>
                                    <td width="10%" bgcolor="#f5f5f5">Prix TTc</td>
                                </tr>

                                {foreach key=key item=detail from=$devisinfo.details}
                                    <tr>
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">
                                            {$detail.product_name}
                                            {if $detail.combinations}
                                                <br>
                                                {foreach key=key item=attribute from=$detail.combinations}
                                                    {$attribute.name}
                                                {/foreach}
                                            {/if}
                                        </td>
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">
                                            {if $detail.product_width}
                                                {$detail.product_width} x {$detail.product_height} 
                                            {/if}
                                        </td>
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">{$detail.product_quantity}</td>
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">{($detail.total_price_tax_incl*1.20)|round:"2"}</td>
                                    </tr>
                                {/foreach}
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td height="40">
                            {if $devisinfo.devis_comment}
                                <table width="620" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">
                                            {$devisinfo.devis_comment}
                                        </td>
                                    </tr>
                                </table>
                            {/if}
                        </td>                        
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            Total produits TTC: {($devisinfo.total_paid*1.20)|round:"2"} €<br>
                            Frais de transport: 25€ <br>
                            Total TTC: {($devisinfo.total_paid*1.20+25)|round:"2"} €<br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
