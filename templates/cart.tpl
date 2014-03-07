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
                                <br>
                            {/if}                            

                            {if isset($product.productinfos.custom_label)}  
                                {foreach key=key item=main_item from=$product.productinfos.custom_label}
                                    {if is_array($main_item)}
                                        {foreach key=key2 item=sub_item from=$main_item}
                                            {if is_array($sub_item)}
                                                {$main_item.main_item_name} : {$sub_item.sub_item_name}<br>
                                                {foreach key=key3 item=values_items from=$sub_item}
                                                    {if $key3=="picture" && $values_items != "" && !is_array($values_items)}                                                        
                                                        <img src="img/f/{$values_items}" alt="" width="90"><br>
                                                    {/if}
                                                    {if is_array($values_items)}                                                        
                                                        {foreach key=key4 item=value_items from=$values_items}
                                                            {if is_array($value_items)}    
                                                                {$value_items.item_value_name} -> {$value_items.custom_value}<br>
                                                            {/if}
                                                        {/foreach}
                                                        <br>
                                                    {/if}
                                                {/foreach}
                                            {/if}
                                        {/foreach} 
                                    {/if}
                                {/foreach}   
                                <br>
                            {/if} 

                            {if isset($product.pose_details)}                                
                                {foreach key=key item=pose from=$product.pose_details}
                                    {$pose.question} {$pose.answer}<br>
                                {/foreach}
                            {/if}
                        </a>
                    </td>
                    {if $product.dimension}
                        <td class="dimensions">{$product.dimension.width}x{$product.dimension.height} mm</td>
                    {else}
                        <td class="dimensions"><em>N/A</em></td>
                    {/if}                                        
                    <td class="quantite">{$product.quantity}</td>
                    <td class="total">{($product.prixttc - $product.discount)|number_format:2} €
                        {if $product.pro_discounted}
                            <em>dont remise pro ({$product.discount} €)</em>
                        {/if}
                    </td>
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
        <p class="total">Total de votre commande : <span class="prix">{($smarty.session.cart_summary.total_produits-$smarty.session.cart_summary.total_discount)|number_format:2}€</span></p>
    </div>
    {if isset($smarty.session.is_logged) && $smarty.session.is_logged}
        {if empty($smarty.session.user.delivery.address1)}
            <p><font color="red">Merci de renseigner votre adresse complète de livraison.</font></p>
            <a href="?register">Mon compte</a>
        {elseif empty($smarty.session.user.delivery.phone) && empty($smarty.session.user.delivery.phone_mobile)}
            <p><font color="red">Merci de renseigner au moins un numéro de téléphone <b>dans l'adresse de livraison</b>.</font></p>
            <a href="?register">Mon compte</a>
        {elseif !$smarty.session.user.delivery.postcode|is_numeric}
            <p><font color="red">[Adresse de livraison] Code postal invalide : Chiffre uniquement</font></p>
            <a href="?register">Mon compte</a>
            {elseif substr($smarty.session.user.delivery.postcode,0,2) eq 20 
            || substr($smarty.session.user.delivery.postcode,0,3) eq 971
            || substr($smarty.session.user.delivery.postcode,0,3) eq 972
            || substr($smarty.session.user.delivery.postcode,0,3) eq 973
            || substr($smarty.session.user.delivery.postcode,0,3) eq 974}
            <p><font color="red">livraison disponible uniquement en France métropolitaine.</font></p>
            <a href="?register">Mon compte</a>
        {else}
            <a href="?delivery"><img src='/img/BTN-livraison.png'></a>
            {/if}
        {else}
        <a href="?order-identification"><img src='/img/BTN-connecter.png'></a>
        {/if}
    <div class="clearfix"></div>
</div>


