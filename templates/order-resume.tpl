{*duplication de code a revoir*}
<div id="recapitulatif">
    <p><img src="/img/recapitulatif.png" alt=""></p>
    <center>
        <div class="infos clearfix">
            <div class="block adr_fact">
                <h3>adresse de facturation</h3>
                <div class="content">
                    {$smarty.session.user.firstname} {$smarty.session.user.lastname} <br>
                    {$smarty.session.user.invoice.address1} <br>
                    {$smarty.session.user.invoice.address2} <br>                    
                    {$smarty.session.user.invoice.postcode} {$smarty.session.user.invoice.city}<br>
                    {$smarty.session.user.invoice.country}
                </div>			
            </div>
            <div class="block adr_liv">
                <h3>adresse de livraison</h3>
                <div class="content">
                    {$smarty.session.user.firstname} {$smarty.session.user.lastname} <br>
                    {$smarty.session.user.delivery.address1} <br>
                    {$smarty.session.user.delivery.address2} <br>                    
                    {$smarty.session.user.delivery.postcode} {$smarty.session.user.delivery.city}<br>
                    {$smarty.session.user.delivery.country}
                </div>
            </div>
            <div class="block mode_trans">
                <h3>mode de transport</h3>
                <div class="content">
                    <br/><br/>
                    <img src="/img/transporteur-allovitres.png" alt="">
                </div>            
            </div>
        </div>
    </center>
    <p class="clearfix">&nbsp;</p>
    {assign var='option_price' value='0'}
    <table class="produits">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th><span>Désignation Produit</span></th>
                <th><span>Dimensions</span></th>
                <th><span>Quantité</span></th>
                <th><span>Prix total ttc</span></th>
            </tr>
        </thead>

        <tbody>
            {foreach key=key item=product from=$cart name=cart}
                {$option_price=0}
                <tr>
                    <td><img src="/img/p/{$product.productinfos.cover.filename}" alt="" width="90"></td>
                    <td class="designation_prd">
                        <a href="/?p&id={$product.id}">{$product.name} 
                            {if isset($product.options)}
                                option : 
                                {foreach key=key item=option from=$product.options}
                                    {$option_price =  $option_price+$option.o_price}
                                    {$option.o_name} 
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
                    <td class="total">{$product.prixttc} €</td>
                </tr>
            {/foreach}               
        </tbody>
    </table>

    {*if isset($smarty.session.cart_summary.order_option)}
        <div class="promo clearfix">
            <p class="total">Option SMS 1€</p>
        </div>
    {/if*}

    <div class="promo clearfix">
        <p class="total">Total produit : <span class="prix">{$smarty.session.cart_summary.total_produits}€</span></p>
    </div>
    <div  class="promo clearfix">
        <p class="total">Total frais de port : <span class="prix">{$smarty.session.cart_summary.total_shipping}€</span></p>
    </div>
    {if $smarty.session.cart_summary.total_discount > 0}
        <div  class="promo clearfix">
            <p class="total">Total Réduction : <span class="prix">{$smarty.session.cart_summary.total_discount}€</span></p>
        </div>    
    {/if}

    <div class="promo clearfix">
        <p class="total">Total de votre commande : <span class="prix">{$smarty.session.cart_summary.total_amount - {$smarty.session.cart_summary.total_discount} + $smarty.session.cart_summary.total_shipping}€</span></p>
    </div>
    {if $smarty.session.cart_summary.total_discount == 0}
        <form action="/?action=add_voucher&order-resume" method="post">
            <div class="promo clearfix">
                <p><span class="code_promo_label">Vous bénéficiez d’un code promotionnel :</span> 
                    <input type="text" name="voucher_code" id="code_promo">
                    <input type="submit" id="ok" value="OK">
                </p>
            </div>
        </form>
    {/if}

    <br/>
    <table width="100%">
        <tr>
            <td align="left"><a href="/?delivery" ><button class="precedent" style="float:left;"></button></a></td>
            <td align="right">
                <span>
                    <form action="/?order-payment" method="post">
                        <input type="checkbox" required="true">J’ai lu et j’accepte <a href="/index.php?cms&id=3">les conditions générales de vente</a>.        
                        <input type="submit" class="valider-porsuivre" value="">
                    </form>
                </span>
            </td>
        </tr>
    </table>




</div>
