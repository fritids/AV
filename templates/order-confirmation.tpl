<div id="recapitulatif">
    <h1>Merci</h1>

    <b>reference </b> : {$order.reference} <br>

    {if $payment =='Chèque'}
        {$config.payment.cheque_infos}
    {/if}
    {if $payment =='Virement bancaire'}

        {$config.payment.virement_infos}
        
    {/if}
    {if $payment =='Chèque'}

    {/if}

</div>


