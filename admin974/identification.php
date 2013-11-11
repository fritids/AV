<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
        <title></title>
        <meta name="description" content=""/>
        <link rel="stylesheet" href="css/identification.css"/>
    </head>
    <body>
        <div class="conteneur">

            <h1>Administration ALLOVITRES</h1>
            <form method="POST" action="connexion.php">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" /><br />
                <label for="mdp">Mot de passe</label>
                <input id="mdp" name="mdp" type="password" /><br />
                <input type="submit" name="b1" value="S'identifier" />
            </form>

        </div>
    </body>
</html>