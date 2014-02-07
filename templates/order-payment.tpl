<div class="bloc_paiement">
    <div class="montant"><p>Le montant &agrave; r&eacute;gler est de <span class="orange">{$smarty.session.cart_summary.total_amount + $smarty.session.cart_summary.total_shipping - $smarty.session.cart_summary.total_discount} € ttc.</span></p></div>
    <div class="texte_paiement">

    </div>
	<table border="0">
<!-- Paiement CB -->	
		<tr>
			<td nowrap valign="middle" align="center"><b>Paiement par CB</b><br/>(cliquez sur le logo de votre CB)</td>
			<td nowrap valign="middle" align="center">
				<div class="type_paiement">
				{$SOCGEN_CHECKOUT_FORM}<br><br>
				<div class="clear"></div>
				<br/><br/>
				</div>
			</td>
		</tr>
<!-- Paiement Paypal -->	
		<tr>
			<td nowrap valign="middle" align="center"><b>Paiement par paypal</b></td>
			<td nowrap valign="middle" align="center">
				<div class="type_paiement">
					<div class="logo_pay"><img src="/img/paypal.png" alt="Paiement par Paypal" /></div>
					<div class="choix_pay">{$PAYPAL_CHECKOUT_FORM}</div>
					<div class="clear"></div>
				</div>
			</td>
		</tr>
<!-- Paiement chèque -->	
		<tr>
			<td valign="middle" align="center"><b>Paiement par chèque</b></td>
			<td nowrap valign="middle" align="center">
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
			</td>

		</tr>
<!-- Paiement virement -->	
		<tr>
			<td valign="middle" align="center"><b>Paiement par virement</b></td>
			<td nowrap valign="middle" align="center">
					<div class="type_paiement">
						<div class="logo_pay"><img src="/img/virement-bancaire.gif" alt="Paiement par virement bancaire" /></div>
						<div class="choix_pay">
							<p>
								<label>
									<form action="/?action=order_validate" method="post">
										<input type='hidden' name="payment" value='Virement bancaire' />
										<input type='submit' value='Payer par virement bancaire' class="pay_cheque" />
									</form>
								</label>
							</p>
						</div>
						<div class="clear"></div>
					</div>
			</td>
		</tr>		
	</table>

    

</form>
<div class="back_to">
    <a href="/?order-resume" title="Revenir au r&eacute;capitulatif"><img src="/img/btn_retour.png" alt="&lt; Revenir au r&eacute;capitulatif" width="239" height="48" /></a>
</div>

</div>
							<!-- Code de tracking RetailMeNot Partenaires -->
							<script type="text/javascript">
								var id_m = "ZWdlmmtt"; /* Identifiant de votre boutique sur votre Espace RetailMeNot Partenaires */
								var mc_ht = "{(($smarty.session.cart_summary.total_amount + $smarty.session.cart_summary.total_shipping - $smarty.session.cart_summary.total_discount)/$config.vat_rate)|round:"2"}"; /* Montant HT de la commande */
								var no_com = "{$smarty.session.id_order}"; /* Numéro de la commande */
								var no_cl = ""; /* Numéro du client */
								var com = ""; /* Commentaire */
							</script>

							<!-- Tracker RetailMeNot Partenaires -->
							<div id="innerScript"></div>
							<script type="text/javascript">
							(function() {
								var tc = document.createElement('script'); tc.type = 'text/javascript'; tc.async = true;
								tc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'partenaires.retailmenot.fr/account/tracker.js';
								var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(tc, s);
							})();
							</script>

