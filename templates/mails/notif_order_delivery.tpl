{include file='mail_header.tpl'}

Bonjour,<br><br>

Nous avons le plaisir de vous informer que votre livraison est prévue pour : {$tournee_livraison}<br><br>

Voici la liste des produits concernés :<br>
{foreach key=key item=detail from=$orderdetails}
    - {$detail.product_quantity} x {$detail.product_name}                                          
    {if $detail.product_width}
        ( {$detail.product_width} x {$detail.product_height} )
    {/if}
    : {$detail.total_price_tax_incl} €
    <br>
{/foreach}
<br>
Pour que votre livraison se passe dans les meilleures conditions, nous nous permettons de vous rappeler deux points importants.<br><br>

1- Toutes les livraisons de verre s’effectuent exclusivement en pied de maison ou d’immeuble.<br>
Pour les commandes où les produits sont de grandes tailles où d’un poids important, merci de prévoir obligatoirement une deuxième personne minimum pour vous aider. (Le livreur n’a pas la possibilité de le faire).<br><br>

2- Pensez à vérifier la conformité de la commande de verre livré en la présence du transporteur avant de signer le Bon de Livraison. (Les vitres ne sont pas emballées pour un souci de transport et de sécurité), vous avez donc la possibilité de vérifier l’état directement à la livraison.<br><br>

En cas d’anomalie ou de non-conformité de ce verre, il faut IMPERATIVEMENT émettre une réserve sur le bordereau de livraison du livreur et prévenir le service client Allovitres à l’adresse mail suivante : livraison@allovitres.com  (Pensez à renseigner votre N° de commande dans votre courrier).<br><br>

En vous remerciant pour la confiance que vous nous avez accordée, nous vous souhaitons une bonne réception.<br><br>

{include file='mail_footer.tpl'}