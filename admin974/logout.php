<?php

// Initialisation de la session.
session_start();

// D�truit toutes les variables de session
$_SESSION = array();

// Si vous voulez d�truire compl�tement la session, effacez �galement
// le cookie de session.
// Note : cela d�truira la session et pas seulement les donn�es de session !
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Finalement, on d�truit la session.
session_destroy();

header('location: index.php');
?>
