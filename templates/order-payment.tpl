<div class="bloc_paiement">
    <div class="montant"><p>Le montant &agrave; r&eacute;gler est de <span class="orange">{$smarty.session.cart_summary.total_amount + $smarty.session.cart_summary.total_shipping - $smarty.session.cart_summary.total_discount} € ttc.</span></p></div>
    <div class="texte_paiement">

    </div>
    <div class="type_paiement">
        {$SOCGEN_CHECKOUT_FORM}<br><br>
        <div class="clear"></div>
    </div>
    
    <div class="type_paiement">
        <div class="logo_pay"><img src="/img/paypal.png" alt="Paiement par Paypal" /></div>
        <div class="choix_pay">{$PAYPAL_CHECKOUT_FORM}</div>
        <div class="clear"></div>
    </div>
    <div class="type_paiement">
        <div class="logo_pay"><img src="/img/checque.png" alt="Paiement par Ch&egrave;que" /></div>
        <div class="choix_pay">
            <p>
                <label>
                    <form action="/?action=order_validate" method="post">
                        <input type='hidden' name="payment" value='Chèque' />
                        <input type='submit' value='Payer par chèque' class="pay_cheque" />
                    </form>
                </label>
            </p>
        </div>
        <div class="clear"></div>
    </div>
</form>
<div class="back_to">
    <a href="/?order-resume" title="Revenir au r&eacute;capitulatif"><img src="/img/btn_retour.png" alt="&lt; Revenir au r&eacute;capitulatif" width="239" height="48" /></a>
</div>

</div>


