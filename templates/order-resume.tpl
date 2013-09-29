{*duplication de code a revoir*}

<div class="bloc-titre">Panier</div>
<div class="bloc-bas" style="min-height:400px">
    {foreach key=key item=product from=$cart}
        <li style="display: block">{$product.id} name={$product.name} quantity= {$product.quantity} price = {$product.price}
            {if isset($product.options)}
                <ul>
                    {foreach key=key item=option from=$product.options}
                        <li > id_option = {$option.o_id} option_name = {$option.o_name} option_qte={$option.o_quantity} option_price={$option.o_price} </li>
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
    {if isset($smarty.session.is_logged) && $smarty.session.is_logged}
        {$PAYPAL_CHECKOUT_FORM}
        {$PAYPAL_CHECKOUT_FORM_TEST}
    {else}
        {include file="form_user_account.tpl"}
    {/if}

</div>

