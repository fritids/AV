{include file="header.tpl"}

<div class="bloc_page clear-it">
    <div class="clear-it" style="margin-top:5px;">
        <div id="bloc_page_gauche">
            {include file="{$page}.tpl"}
        </div>
        <div id="bloc_page_droite">
            <div id="panier">
                <span id="panier-titre">PANIER</span>
                <hr />	
                {assign var="total" value="0"}
                {foreach key=key item=product from=$cart}
                    <p>{$product.id} nom={$product.name} qte={$product.quantity} price={$product.price}</p> 
                    {if isset($product.options)}
                        <ul>
                            {foreach key=key item=option from=$product.options}
                                <li > id_option = {$option.o_id} option_name = {$option.o_name} option_qte={$option.o_quantity} option_price={$option.o_price} </li>
                                {/foreach}   
                        </ul>
                    {/if} 
                    
                {/foreach}
                <hr />

                <p>
                    Expédition xx,00 €<br/>
                    Taxes xx,xx €<br/>
                    Total {$smarty.session.cart_summary.total_amount} €<br/>
                    Les price sont TTC <br/>
                </p>
                <a class="bouton" href="?cart">Panier</a>
                <a class="bouton" href="?order-resume">Commander</a>
            </div>
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
    </div>
</div>

{include file="footer.tpl"}