<div class="bloc_id">
    <div class="page_titre">
        <span class="titre">Identification</span>
    </div>
    <p class="intro">Cr&eacute;er votre compte vous permet de vous enregistrer comme client Allovitres. Vous n'aurez plus &agrave; renseigner vos informations lors de vos prochaines visites et cela vous permet de suivre l'&eacute;tat de vos commandes en quelques clics.</p>
    <div class="crea_compte">
        <h3 class="account_title no1">CR&Eacute;ER UN COMPTE</h3>
        <div class="part_account">
            <p>Enregistrement simple et rapide !</p>
            <a href="/?register" class="go_part">compte particulier</a>
        </div>
        <div class="pro_account">
            <p>B&eacute;n&eacute;ficiez d'offres et d’avantages r&eacute;serv&eacute;s aux professionnels.</p>
            <a href="/?register" class="go_pro">compte professionnel</a>
        </div>
        <div class="clear"></div>
    </div>
    <div class="identification">
        <h3 class="account_title no2">J'AI D&Eacute;J&Agrave; UN COMPTE</h3>
        <form action="/index.php?action=login" method="post">
            <div class="box_chmp">
                <label>Identifiant:</label>
                <input type="text" class="txt_account" name="email"  />
                <div class="clear"></div>
            </div>
            <div class="box_chmp">
                <label>Mot de passe:</label>
                <input type="password" class="txt_account" name="passwd" />
                <div class="clear"></div>
            </div>
            <div class="box_chmp">
                <button type="button" class="submit_mdp_oublie" data-toggle="modal" data-target="#LostPwd"></button>
                <input type="submit" class="submit_account" value="&nbsp;" />
                <div class="clear"></div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="LostPwd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Mot de passe oublié</h4>
                </div>
                <div class="modal-body">
                    <form method="post" action="/index.php?action=lost_pwd">
                        <div class="box_chmp">
                            <label>email :</label>
                            <input class="txt_account" type="text" name="email">
                            <div class="clear"></div>
                        </div>
                        <br/>
                        <input class="submit_generer_mdp" type="submit" value=" ">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>                            
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div class="clear"></div>
</div>