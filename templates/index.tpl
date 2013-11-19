{include file="header.tpl"}
{if $page_info != 'home'}
    <div class="bloc_page">
        <p><span id="fil-ariane-page">{$breadcrumb.parent} <span>{$breadcrumb.fils}</span></span></p>

        {if $page_type == 'full'}
            {if $error}
                <div style="background-color: red;margin-bottom: 20px; padding: 10px" >
                    {$error.txt}  
                </div>
            {/if}
            {if $okmsg}
                <div style="background-color: #7aba7b;margin-bottom: 20px; padding: 10px" >
                    {$okmsg.txt}  
                </div>
            {/if}
            {include file="{$page}.tpl"}
        {else}
            <div id="bloc_page_gauche">
                {if $error}
                    <div style="background-color: red;margin-bottom: 20px; padding: 10px" >
                        {$error.txt}  
                    </div>
                {/if}
                {if $okmsg}
                    <div style="background-color: #7aba7b;margin-bottom: 20px; padding: 10px" >
                        {$okmsg.txt}  
                    </div>
                {/if}
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
                        <hr/>
                        <p>
                            Total produit : {$smarty.session.cart_summary.total_produits} € TTC<br/>
                            {*Expédition : {$smarty.session.cart_summary.total_shipping} € TTC<br/>*}
                            Taxes incluses : {$smarty.session.cart_summary.total_taxes} €<br/>                
                            Total : {$smarty.session.cart_summary.total_amount} € TTC<br/>                
                        </p>
                        <a class="bouton" href="/?cart">Panier</a>                        
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
                        <a href="/?contact-devis"><li><img src="/img/b1.png" /><h3 style="font-size: 0.875em;display:inline;">Devis Spécifique</h3></li></a>
                        <a href="/content/16-prise-de-mesure-et-pose-d-un-vitrage-sur-fenetre-pvc"><li><img src="/img/b2.png" /><h3 style="font-size: 0.875em;display:inline;">Prise de mesures</h3></li></a>
                        <a href="/content/13-service-de-pose"><li><img src="/img/b3.png" /><h3 style="font-size: 0.875em;display:inline;">Service de pose</h3></li></a>
                        <a href="/content/1-livraison-dans-toute-la-france-allovitres"><li><img src="/img/b4.png" /><h3 style="font-size: 0.875em;display:inline;">Livraison</h3></li></a>
                        <li><img src="/img/b5.png" /><h3 style="font-size: 0.875em;display:inline;">Nos conseils Videos</h3></li>
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