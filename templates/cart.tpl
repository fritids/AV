

<div class="bloc-titre">Panier</div>

<div id="monpanier">
    <p><img src="img/monpanier.png" alt=""></p>
    <p><img src="img/panier.png" alt=""></p>
    <p style="font-weight: bold;">
        Bonjour <span class="blue">{$smarty.session.user.firstname} {$smarty.session.user.lastname}</span><br/>
        Votre panier contient <span class="blue">{$smarty.session.cart|count}</span> article(s) :        
    </p>

    {assign var='option_price' value='0'}
    <table class="produits">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th><span>Désignation Produit</span></th>
                <th><span>Dimensions</span></th>
                <th><span>Prix unitaire ttc</span></th>
                <th><span>Quantité</span></th>
                <th><span>Prix total ttc</span></th>
            </tr>
        </thead>

        <tbody>
            {foreach key=key item=product from=$cart name=cart}
                {$option_price=0}
                <tr>
                    <td><img src="img/recap-prod.png" alt=""></td>
                    <td class="designation_prd">
                        <a href="">{$product.name} 
                            {if isset($product.options)}
                                option : 
                                {foreach key=key item=option from=$product.options}
                                    {$option_price =  $option_price+$option.o_price}
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
                    <td class="dimensions">{$product.dimension.width}x{$product.dimension.height} mm</td>
                    <td class="prix_unit">{$product.price} €</td>
                    <td class="quantite">
                        <a href="#" class="moins"></a>
                        <input type="text" name="" id="" value="{$product.quantity}" class="qte">
                        <a href="#" class="plus"></a>
                    </td>
                    <td class="total">
                        {if $option_price !=0}
                            {$product.quantity*$product.price*$product.surface+$option_price} 
                        {else}
                            {$product.quantity*$product.price*$product.surface} 
                        {/if}
                        €</td>
                </tr>
            {/foreach}               
        </tbody>
    </table>

    <div class="promo clearfix">
        <p class="total">Total de votre commande : <span class="prix">{$smarty.session.cart_summary.total_produits}€</span></p>
    </div>
    <a href="?delivery">livraison</a>
</div>


