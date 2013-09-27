<div class="bloc-titre">Panier</div>
<div class="bloc-bas" style="min-height:400px">
    {foreach key=key item=product from=$cart}
        <li>{$product.id} name={$product.name} quantity= {$product.quantity} price = {$product.price}
            <form action="?cart" method="post">
                <input type="hidden" name="id_product" value="{$product.id}">
                <input type="hidden" name="quantity" value="{$product.quantity}">
                <input type="hidden" name="del">
                <input type="submit" value="Retirer" >
            </form> 
        </li> 
    {/foreach}
    {$PAYPAL_CHECKOUT_FORM}
    {$PAYPAL_CHECKOUT_FORM_TEST}
</div>
