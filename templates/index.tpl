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
                    <p>{$product.id} nom={$product.nom} qte= {$product.qte} prix = {$product.prix}</p> 
                    {$total = $total + $product.prix}
                {/foreach}
                <hr />

                <p>
                    Expédition xx,00 €<br/>
                    Taxes xx,xx €<br/>
                    Total {$total} €<br/>
                    Les prix sont TTC <br/>
                </p>
                <a class="bouton" href="?cart">Panier</a><a class="bouton" href="">Commander</a>
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