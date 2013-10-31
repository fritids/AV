{include file="header.tpl"}
{if $page_info != 'home'}
    <div class="bloc_page">
        <p><span id="fil-ariane-page">{$breadcrumb.parent} <span>{$breadcrumb.fils}</span></span></p>

        {if $page_type == 'full'}
            {include file="{$page}.tpl"}
        {else}
            <div id="bloc_page_gauche">
                {include file="{$page}.tpl"}
            </div>

            <div id="bloc_page_droite">
                {if $cart}
                    <div id="panier">
                        <span id="panier-titre">PANIER</span>
                        <hr />	
                        {assign var="total" value="0"}
                        {foreach key=key item=product from=$cart}
                            <p>{$product.quantity} x {$product.name} = {$product.prixttc} €</p> 
                            {if isset($product.options)}
                                option :
                                {foreach key=key item=option from=$product.options}
                                    {$option.o_name}
                                {/foreach}   

                            {/if} 

                        {/foreach}
                        <hr />

                        <p>
                            Total produit : {$smarty.session.cart_summary.total_produits} € TTC<br/>
                            Expédition : {$smarty.session.cart_summary.total_shipping} € TTC<br/>
                            Taxes incluses : {$smarty.session.cart_summary.total_taxes} €<br/>                
                            Total : {$smarty.session.cart_summary.total_amount} € TTC<br/>                
                        </p>
                        <a class="bouton" href="?cart">Panier</a>
                        <a class="bouton" href="?order-resume">Commander</a>
                    </div>
                {else}
                    <div id="panier">
                        <span id="panier-titre">PANIER</span>
                        <hr />	
                        aucun produit dans votre panier actuellement.
                        <hr />
                    </div> 
                {/if}

                <div class="bloc-droite">
                    <h2>NOS SERVICES</h2>
                    <ul>
                        <li><img src="img/b1.png" />Devis Spécifique</li>
                        <li><img src="img/b2.png" />Découpe sur mesure</li>
                        <li><img src="img/b3.png" />Service de pose</li>
                        <li><img src="img/b4.png" />Livraison</li>
                        <li><img src="img/b5.png" />Nos conseils Videos</li>
                    </ul>
                </div>
            </div>
            <div class="clearfix"></div>
        {/if}
    </div>
{else} {* homepage  *}


    {include file="{$page}.tpl"}

{/if}
{include file="footer.tpl"}