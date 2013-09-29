<div class="bloc-titre">Panier</div>
<div class="bloc-bas" style="min-height:400px">
    {foreach key=key item=product from=$cart}
        <li style="display: block">{$product.id} 
            name={$product.name} 
            quantity= {$product.quantity} 
            price = {$product.price}
            shipping amount = {$product.shipping}
            {if isset($product.options)}
                <ul>
                    {foreach key=key item=option from=$product.options}
                        <li > 
                            id_option = {$option.o_id} 
                            option_name = {$option.o_name} 
                            option_qte={$option.o_quantity} 
                            option_price={$option.o_price} 
                            option_shipping_amount={$option.o_shipping} 
                        </li>
                    {/foreach}   
                </ul>
            {/if}
            <form action="?cart" method="post">
                <input type="hidden" name="id_product" value="{$product.id}">
                <input type="hidden" name="quantity" value="{$product.quantity}">
                <input type="hidden" name="del">
                <input type="submit" value="Retirer" >
            </form> 
        </li> 
    {/foreach}    
</div>
