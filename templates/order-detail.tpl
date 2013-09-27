<div class="bloc-titre">Commandes</div>
<div class="bloc-bas" style="min-height:400px">


    <b>id_order             </b> : {$order.id_order}
    <b>reference            </b> : {$order.reference}
    <b>id_customer          </b> : {$order.id_customer}
    <b>id_address_delivery  </b> : {$order.id_address_delivery}
    <b>id_address_invoice   </b> : {$order.id_address_invoice}
    <b>current_state        </b> : {$order.current_state}
    <b>total_paid           </b> : {$order.total_paid}
    <b>invoice_date         </b> : {$order.invoice_date}
    <b>delivery_date        </b> : {$order.delivery_date}
    <b>date_add             </b> : {$order.date_add}
    <b>date_upd             </b> : {$order.date_upd}

    <h2> Detail commande </h2>

    {foreach key=key item=detail from=$orderDetails}
        <b>id_order_detail	        </b> : {$detail.id_order_detail}
        <b>id_order	                </b> : {$detail.id_order}
        <b>product_id	            </b> : {$detail.product_id}
        <b>product_name	            </b> : {$detail.product_name}
        <b>product_quantity	        </b> : {$detail.product_quantity}
        <b>product_price	        </b> : {$detail.product_price}
        <b>total_price_tax_incl	    </b> : {$detail.total_price_tax_incl}
        <b>total_price_tax_excl	    </b> : {$detail.total_price_tax_excl}
        <b>unit_price_tax_incl	    </b> : {$detail.unit_price_tax_incl}
    {/foreach}

</div>
