
<body style="margin: 0; padding: 0; font-family:Arial, Helvetica, sans-serif; font-size:15px;">
    <table width="645" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="87">&nbsp;</td>
                        <td width=""><h2>Bon de livraison N {$orderinfo.reference}</h2></td>
                    </tr>
                    <tr>
                        <td height="40"><h3>Livraison</h3></td>
                        <td width=""><h3>Facturation</h3></td>
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
                                    <td width="76%" style="padding:3px;" bgcolor="#f5f5f5">Description</td>
                                    {*<td width="14%" bgcolor="#f5f5f5">Référence</td>*}
                                    <td width="10%" bgcolor="#f5f5f5">Qté</td>
                                </tr>

                                {foreach key=key item=detail from=$orderdetails}
                                    <tr>
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">{$detail.product_name}</td>
                                        {*{$detail.product_width} {$detail.product_height} *}
                                        <td style=" border-bottom:1px #000000 solid; padding:3px;">{$detail.product_quantity}</td>
                                    </tr>
                                {/foreach}
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
