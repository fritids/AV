{*
<div class="bloc-titre">Commandes</div>
<div class="bloc-bas" style="min-height:400px">

    {foreach key=key item=order from=$orders}

        <h2> order : {$order.id_order} </h2>
        <b>reference            </b> : {$order.reference} <br>
        <b>id_customer          </b> : {$order.id_customer}<br>
        <b>id_address_delivery  </b> : {$order.id_address_delivery}<br>
        <b>id_address_invoice   </b> : {$order.id_address_invoice}<br>
        <b>current_state        </b> : {$order.current_state}<br>
        <b>total_paid           </b> : {$order.total_paid}<br>
        <b>invoice_date         </b> : {$order.invoice_date}<br>
        <b>delivery_date        </b> : {$order.delivery_date}<br>
        <b>date_add             </b> : {$order.date_add}<br>
        <b>date_upd             </b> : {$order.date_upd}<br>

        <h2> Detail commande </h2>

        {assign var="total_amount" value=0}
        {foreach key=key item=detail from=$order.details}
            <b>product_id	            </b> : {$detail.id_product}<br>
            <b>product_name	            </b> : {$detail.product_name}<br>
            <b>product_quantity	        </b> : {$detail.product_quantity}<br>
            <b>product_price	        </b> : {$detail.product_price}<br>
            <b>attribut_name            </b> : {$detail.attribut_name}<br>
            <b>total_price_tax_incl            </b> : {$detail.total_price_tax_incl}<br>
            <br>
            {$total_amount = $total_amount+ $detail.total_price_tax_incl}

        {/foreach}
        TOTAL COMMANDE = {$total_amount} € TTC<br>

        <hr>
    {/foreach}

</div>
*}


<div id="mescommandes" class="clear-it">
    <h3 id="titre">Mes commandes</h3>
    {if $orders}
        <table>
            <thead>
                <tr>
                    <th class="first">N° DE CMD</th>
                    <th>DATE DE COMMANDE</th>
                    <th>MODE DE PAIEMENT</th>
                    <th>PRIX TTC</th>
                    <th class="last">STATUT</th>
                </tr>
            </thead>
            <tbody>
				{foreach key=key item=order from=$orders}
					{if $order.current_state!=null}
                    <tr>
                        <td class="numero">N° <span>{$order.id_order}</span></td>
                        <td class="date">{$order.date_add|date_format:"%d/%m/%y"}</td>
                        <td class="client">{$order.payment}</td>
                        <td class="prix">{$order.total_paid} € TTC</td>
                        <td class="action">{$order.statut_label}</td>
                    </tr>
					{/if}
                {/foreach}

            </tbody>
        </table>

    {else}
        <p>Aucune commande n'a été trouvée</p>
    {/if}
</div>

<div class="clearfix"></div>