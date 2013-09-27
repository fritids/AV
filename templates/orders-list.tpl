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

        {foreach key=key item=detail from=$order.details}
            <b>product_id	            </b> : {$detail.product_id}<br>
            <b>product_name	            </b> : {$detail.product_name}<br>
            <b>product_quantity	        </b> : {$detail.product_quantity}<br>
            <b>product_price	        </b> : {$detail.product_price}<br>
        {/foreach}
    {/foreach}

</div>
