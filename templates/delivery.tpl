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

            <p class="un"><span class="orange">Suivi de commande par sms</span></p>
            <p>Désirez vous bénéficier du suivi de commande par SMS ?<br/>
                Ce service vous permet de recevoir un sms sur le numero de téléphone portable de votre choix à chaque étape de votre commande.<br/>Il vous permet notamment d'etre informé de la date et du créneau horaire de votre livraison dès que celle-ci est programmée.</p>
            <p><input type="checkbox" name="alert_sms" class="alert_sms" value="1" checked="checked"> SMS (1€) 
                
                    Tél: <input type="tel" name="alert_sms_phone" class="alert_sms_phone" required="required" {literal}pattern="^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$" {/literal} > ( Format: 0612345678 )
                

            </p>
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

<script>
    $(".alert_sms").click(function() {
        if ($(this).is(':checked')) {
            $(".alert_sms_phone").attr("required", "required");
        } else {
            $(".alert_sms_phone").removeAttr("required");
        }
    })
</script>
