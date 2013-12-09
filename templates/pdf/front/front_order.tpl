
<body style="margin: 0; padding: 0; font-family:Arial, Helvetica, sans-serif; font-size:15px;">
    <table width="645" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="87">&nbsp;</td>
                        <td width=""><h2>Facture FA{$orderinfo.reference}</h2><br> le {$orderinfo.date_add|date_format:"%d/%m/%y"}</td>
                    </tr>
                    <tr>
                        <td>
                            {$orderinfo.customer.lastname} {$orderinfo.customer.firstname}<br>
                            {$orderinfo.address.delivery.address1}<br>
                            {$orderinfo.address.delivery.address2}<br>
                            {$orderinfo.address.delivery.postcode} {$orderinfo.address.delivery.city}
                        </td>
                        <td width="">{$orderinfo.customer.lastname} {$orderinfo.customer.firstname}<br>
                            {$orderinfo.address.invoice.address1}<br>
                            {$orderinfo.address.invoice.address2}<br>
                            {$orderinfo.address.invoice.postcode} {$orderinfo.address.invoice.city}
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
                                    <td colspan="3" bgcolor="#f5f5f5" style="padding:5px; border:1px solid #000;">Bon de livraison n°{$orderinfo.reference}</td>
                                </tr>
                                <tr>
                                    <td style="border:1px solid #000;padding:2px; ">Commande n° {$orderinfo.reference}</td>
                                    <td style="border:1px solid #000;padding:2px; ">LIVRAISON A DOMICILE ALLOVITRES</td>
                                    <td style="border:1px solid #000;padding:2px; ">Méthode de paiement :<br>{$orderinfo.payment}</td>
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
                                    <td width="10%" bgcolor="#f5f5f5">Prix TTC</td>
                                </tr>

                                {foreach key=key item=detail from=$orderinfo.details}
                                    <tr>
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">
                                            {$detail.product_name}<br>
                                            {if $detail.attributes}
                                                {foreach key=key item=attribute from=$detail.attributes}
                                                    {$attribute.attribute_name}: {$attribute.attribute_value} <br>
                                                {/foreach}
                                            {/if}
                                        </td>
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">
                                            {if $detail.product_width}
                                                {$detail.product_width} x {$detail.product_height} 
                                            {/if}
                                        </td>
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">{$detail.product_quantity}</td>
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">{$detail.total_price_tax_incl}</td>
                                    </tr>
                                {/foreach}
                            </table>
                            <br>
                            <br>
                            <br>

                            {if $orderinfo.alert_sms}
                                Option Alerte SMS : 1€ <br><br>
                            {/if}
                            Total produits HT: {(($orderinfo.total_paid-25)/(1+$orderinfo.vat_rate/100))|number_format:2} €<br>
                            Total Tva ( {$orderinfo.vat_rate}% ): {($orderinfo.total_paid - ($orderinfo.total_paid-25)/(1+$orderinfo.vat_rate/100) - 25)|number_format:2} €<br>
                            Total produits TTC: {$orderinfo.total_paid - 25} €<br>
                            Frais de transport: 25€ <br>
                            Total TTC :{$orderinfo.total_paid} €<br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
