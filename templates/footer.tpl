<footer>
    <div id="footer1">
        <div id="footer1-left">
            <ul>
                <li><img src="/img/FOOTER_choisir_verre.png"/></li>
                <li><h2 style="font-family: Arial;font-size: 0.875em; font-weight: bold;display:inline;">Choisir son verre</h2></li>
            </ul>
            <a href="/content/1-livraison-dans-toute-la-france-allovitres">			
                <ul>
                    <li><img src="/img/FOOTER_transport.png"/></li>
                    <li><h2 style="font-family: Arial;font-size: 0.875em; font-weight: bold;display:inline;">Livraison</h2></li>
                </ul>
            </a>
            <a href="/?contact-devis">				
                <ul>
                    <li><img src="/img/FOOTER_devis.png"/></li>
                    <li><h2 style="font-family: Arial;font-size: 0.875em; font-weight: bold;display:inline;">Devis personnalisé</h2></li>
                </ul>
            </a>			
            <a href="/?contactez-nous"><ul>
                    <li><img src="/img/FOOTER_nous_contacter.png"/></li>
                    <li><h2 style="font-family: Arial;font-size: 0.875em; font-weight: bold;display:inline;">Nous contacter</h2></li>
                </ul>
            </a>
        </div>
        <div id="footer1-right">
            <p id="footer1-right-titre"><img src="/img/contact.png"/ style="float:left;">
            <h2 style="color: #367FBD;font-family: Arial;font-size: 1.125em;font-weight: bold;margin: 10px 0 0 2px; vertical-align: text-bottom;">NEWSLETTER</h2></p>
            <p id="footer1-right-texte">
                Pour recevoir toute l’actualité, les nouveautés et les promotions allovitres,
                inscrivez-vous.
            </p>
            <form action="index.php?newsletter" method="post">
                votre mail : <input type="text" name="email"><input id="footer-submit" type="submit" value="OK"><br>
            </form>
        </div>
    </div>
    <div id="footer2-bg">
        <div id="footer2">
            <ul>
                <li><span class="caret"></span></li>
                <li class="titre">INFORMATIONS</li>
                    {*foreach key=key item=content from=$AllCMS}
                    <li><a href="/?cms&id={$content.id_cms}"> {$content.meta_title}</a></li>
                    {/foreach*}
                <li><a href="/?contactez-nous" class="lien-footer">Nous contacter</a></li>
                <li><a href="/?cms&id=2" class="lien-footer">Mentions légales</a></li>
                <li><a href="/?cms&id=3" class="lien-footer">CGU</a></li>
                <li><a href="/?cms&id=20" class="lien-footer">FAQ</a></li>
            </ul>
            <ul>
                <li><span class="caret"></span></li>
                <li class="titre"><a href="/?my-account" class="lien-titre">MON COMPTE</a></li>
                <li>Informations personnelles</li>
                <li>Mes factures</li>
                <li>Mes avoirs</li>
                <li>Parainage</li>
            </ul>
            <ul>
                <li><span class="caret"></span></li>
                <li class="titre">SUIVEZ-NOUS</li>
                <li><a href="https://plus.google.com/b/117282875950258035705/+Allovitres/posts"><img src="/img/google-plus.png"/></a><a href="https://www.facebook.com/Allovitres"><img  class="logo_footer" src="/img/facebook.png"/><a></li>
                            </ul>
                            <ul>
                                <li><span class="caret"></span></li>
                                <li class="titre">PAIEMENT 100% SECURISE</li>
                                <li><img src="/img/cb.png"/></li>
                            </ul>			

                            </div>
                            </div>
                            </footer>
                            </div>
                            <div id="test123" style="position:absolute;top:0px;z-index:-100;	background: #539cf1;
                                 background-image:url(/bg/1.png);
                                 background-position:center top;
                                 background-repeat: no-repeat;
                                 width:100%;height:100%;">
                            </div>

                            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.js"></script>
                            <script>window.jQuery || document.write('<script src="/js/vendor/jquery-1.10.1.js"><\/script>')</script>

                            <script src="/js/plugins.js"></script>
                            <script src="/js/main.js"></script>

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