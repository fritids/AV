<footer>
    <div id="footer1">
        <div id="footer1-left">
            <ul>
                <li><img src="img/FOOTER_choisir_verre.png"/></li>
                <li>Choisir son verre</li>
            </ul>
            <ul>
                <li><img src="img/FOOTER_transport.png"/></li>
                <li>Livraison</li>
            </ul>
            <ul>
                <li><img src="img/FOOTER_devis.png"/></li>
                <li>Devis personnalisé</li>
            </ul>
            <ul>
                <li><img src="img/FOOTER_nous_contacter.png"/></li>
                <li>Nous contacter</li>
            </ul>
        </div>
        <div id="footer1-right">
            <p id="footer1-right-titre"><img src="img/contact.png"/>
                NEWSLETTER</p>
            <p id="footer1-right-texte">
                Pour recevoir toute l’actualité, les nouveautés et les promotions allovitres,
                inscrivez-vous.
            </p>
            <form>
                votre mail : <input type="text" name="email"><input id="footer-submit" type="submit" value="OK"><br>
            </form>
        </div>
    </div>
    <div id="footer2-bg">
        <div id="footer2">
            <ul>
                <li><span class="caret"></span></li>
                <li class="titre">INFORMATIONS</li>
                    {foreach key=key item=content from=$AllCMS}
                    <li><a href="?cms&id={$content.id_cms}"> {$content.meta_title}</a></li>
                    {/foreach}
                <li>Nous contacter</li>
                <li>Mentions légales</li>
                <li>CGU</li>
                <li>FAQ</li>
            </ul>
            <ul>
                <li><span class="caret"></span></li>
                <li class="titre">MON COMPTE</li>
                <li>Informations personnelles</li>
                <li>Mes factures</li>
                <li>Mes avoirs</li>
                <li>Parainage</li>
            </ul>
            <ul>
                <li><span class="caret"></span></li>
                <li class="titre">SUIVEZ-NOUS</li>
                <li><img src="img/google-plus.png"/><img  class="logo_footer" src="img/facebook.png"/><img  class="logo_footer" src="img/twitter.png"/></li>
            </ul>
            <ul>
                <li><span class="caret"></span></li>
                <li class="titre">PAIEMENT 100% SECURISE</li>
                <li><img src="img/cb.png"/></li>
            </ul>			

        </div>
    </div>
</footer>
</div>
<div id="test123" style="position:absolute;top:0px;z-index:-100;	background: #539cf1;
     background-image:url(bg/1.png);
     background-position:center top;
     background-repeat: no-repeat;
     width:100%;height:100%;">
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.js"><\/script>')</script>

<script src="js/plugins.js"></script>
<script src="js/main.js"></script>

<script>
    var _gaq = [['_setAccount', 'UA-XXXXX-X'], ['_trackPageview']];
    (function(d, t) {
        var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
        g.src = '//www.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g, s)
    }(document, 'script'));
</script>
</body>
</html>