{*duplication de code a revoir*}
<div id="recapitulatif">
    <p><img src="img/recapitulatif.png" alt=""></p>

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
                <img src="img/transporteur-allovitres.png" alt="">
            </div>            
        </div>
    </div>
    <p class="clearfix">&nbsp;</p>
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

                        </a>
                    </td>
                    <td class="dimensions">{$product.dimension.width}x{$product.dimension.height} mm</td>
                    <td class="prix_unit">{$product.price} €</td>
                    <td class="quantite">{$product.quantity}</td>
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
        <p class="total">Total produit : <span class="prix">{$smarty.session.cart_summary.total_produits}€</span></p>
    </div>
    <div class="promo clearfix">
        <p class="total">Total frais de port : <span class="prix">{$smarty.session.cart_summary.total_shipping}€</span></p>
    </div>
    <div class="promo clearfix">
        <p class="total">Total de votre commande : <span class="prix">{$smarty.session.cart_summary.total_amount}€</span></p>
    </div>


    <div class="promo clearfix">
        <p><span class="code_promo_label">Vous bénéficiez d’un code promotionnel :</span> <input type="text" name="" id="code_promo"><input type="button" id="ok" value="OK"></p>

    </div>

    <p>
        <a href="?delivery" ><button class="precedent"></button></a>

        <span style="float: right;">
            <form action="?order-payment" method="post">
                <input type="checkbox" required="true">J’ai lu et j’accepte <a href="#">les conditions générales de vente</a>.        
                <input type="submit" class="valider-porsuivre" value="">
            </form>
        </span>
    </p>
</div>
