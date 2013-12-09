<div id="livraison-pose">
    <p><img src="/img/livraison-pose.png" style="width: 100%"></p>

    <div class="options">
        <div class="option option3">
            <p>
                <input type="radio" name="" id="" checked="">
                <span class="bd">&nbsp;</span>
                <label for="">Livraison SEULE</label>	
            </p>
            <img src="/img/camion.png" alt="">
        </div>
        <div class="option option3">
            <p>
                <input type="radio" name="" id="" disabled="true">
                <span class="bd">&nbsp;</span>
                <label for="">Retrait chez un partenaire</label>
            </p>
            <img src="/img/service-indispo.png" alt="">
        </div>
        <div class="option option3">
            <p>
                <input type="radio" name="" id=""  disabled="true">
                <span class="bd">&nbsp;</span>
                <label for="">Livraison et pose</label>
            </p>
            <img src="/img/service-indispo.png" alt="">
        </div>
    </div>

    <p class="un"><span class="orange">Choisissez votre lieu de livraison</span></p>

    <div class="options2 clearfix">
        <div class="option">
            {*<div class="input">
            <input type="radio" name="" id="" checked="true">
            </div>
            *}
            <div class="text">
                <span class="label">à mon adresse de livraison <a href="/?register">modifier</a></span>
                <span class="info">
                    {$smarty.session.user.firstname} {$smarty.session.user.lastname} <br>
                    {$smarty.session.user.delivery.address1} <br>
                    {$smarty.session.user.delivery.address2} <br>                    
                    {$smarty.session.user.delivery.postcode} {$smarty.session.user.delivery.city}<br>
                    {$smarty.session.user.delivery.country}
                </span>
            </div>			
        </div>
        {*<div class="option ou">OU</div>
        <div class="option">
        <div class="input">
        <input type="radio" name="" id=""  disabled="true" disabled="true">
        </div>

        <div class="text">
        <span class="label">livrer à une autre adresse</span>
        </div>			

        </div>
        *}
    </div>
    <form action="?order-resume" method="post">
        <div class="options2 clearfix">
            <p><input type="checkbox" name="alert_sms" value="1" {if isset($smarty.session.cart_summary.order_option) && $smarty.session.cart_summary.order_option == "SMS"} checked {/if}> SMS (1€)</p>
        </div>
        <div class="clearfix"></div>

        <p class="deu"><span class="orange">commentaires sur le lieu</span> (accès difficile, batiment particulier, code portail, chien méchant etc...)</p>
        <textarea name="order_comment" id="" cols="30" rows="10">{$smarty.session.cart_summary.order_comment}</textarea>
        <p>
            <a href="/?cart" ><button type="button" id="btn-precedent" class="precedent"></button></a>
            <input type="submit" value="" class="submit">
        </p>
    </form>
    <p class="clearfix">&nbsp;</p>
</div>
