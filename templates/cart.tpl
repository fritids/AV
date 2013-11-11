<div id="monpanier">
    {*<p><img src="img/monpanier.png" alt=""></p>*}
    <p><img src="img/panier.png" alt=""></p>
    <p style="font-weight: bold;">
        Bonjour <span class="blue">{$smarty.session.user.firstname} {$smarty.session.user.lastname}</span><br/>
        Votre panier contient <span class="blue">{$cart_nb_items}</span> article(s) :        
    </p>

    {assign var='option_price' value='0'}
    <table class="produits">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th><span>Désignation Produit</span></th>
                <th><span>Dimensions</span></th>
                <th><span>Quantité</span></th>
                <th><span>Prix total ttc</span></th>
                <th>&nbsp;</th>
            </tr>
        </thead>

        <tbody>
            {foreach key=array_key item=product from=$cart name=cart}
                {$option_price=0}
                <tr>
                    <td><img src="img/p/{$product.productinfos.cover.filename}" alt="" width="90"></td>
                    <td class="designation_prd">
                        <a href="?p&id={$product.id} ">{$product.name} 
                            {if isset($product.options)}
                                option : 
                                {foreach key=key item=option from=$product.options}
                                    {$option_price =  $option_price+$option.o_price}
                                    {$option.o_name} 
                                {/foreach}   
                            {/if}
                            {*
                            <li > 
                            id_option = {$option.o_id} 
                            option_name = {$option.o_name} 
                            option_qte={$option.o_quantity} 
                            option_price={$option.o_price} 
                            option_shipping_amount={$option.o_shipping} 
                            option_surface={$option.o_surface} 
                            </li>
                            *}
                        </a>
                    </td>
                    {if $product.dimension}
                        <td class="dimensions">{$product.dimension.width}x{$product.dimension.height} mm</td>
                    {else}
                        <td class="dimensions"><em>N/A</em></td>
                    {/if}                                        
                    <td class="quantite">{$product.quantity}</td>
                    <td class="total">{$product.prixttc} €</td>
                    <td><form action="?cart" method="post">
                            <input type="hidden" name="id_cart_item" value="{$array_key}">
                            <input type="hidden" name="id_product" value="{$product.id}">
                            <input type="hidden" name="quantity" value="{$product.quantity}">
                            <input type="hidden" name="nitem" value="{$smarty.foreach.cart.iteration}">
                            <input type="hidden" name="del">
                            <input type="submit" value="x" >
                        </form> </td>
                </tr>
            {/foreach}               
        </tbody>
    </table>

    <div class="promo clearfix">
        <p class="total">Total de votre commande : <span class="prix">{$smarty.session.cart_summary.total_produits}€</span></p>
    </div>
    {if isset($smarty.session.is_logged) && $smarty.session.is_logged}

        <a href="?delivery"><button id="btn-livraison"></button></a>
    {else}
        <a href="?order-identification"><button id="btn-connexion"></button></a>
    {/if}
	<div class="clearfix"></div>
</div>


