<?php 

include("mysqlmdp.php");
mysql_connect($bdserv,$bduser,$bdpass);
mysql_select_db($bdname);

 if (isset($_POST['email']) && isset($_POST['mdp'])) { 
     
          // on v�rifie les informations du formulaire, � savoir si le pseudo saisi est bien un pseudo autoris�, de m�me pour le mot de passe
				$email=$_POST['email'];
				$mdp=$_POST['mdp'];
				$sql = "SELECT * FROM admin_user WHERE email LIKE '$email' AND mdp='$mdp'";
				$result = mysql_query($sql);
				if (mysql_num_rows($result) == 1) {


				// dans ce cas, tout est ok, on peut d�marrer notre session
				session_start ();
                
                // on enregistre les param�tres de notre visiteur comme variables de session ($login et $pwd) (notez bien que l'on utilise pas le $ pour enregistrer ces variables)
                $_SESSION['email'] = $_POST['email']; 
                $_SESSION['mdp'] = $_POST['mdp']; 
     
                // on redirige notre visiteur vers une page de notre section membre
                header ('location: index.php?ok'); 
          } 
          else { 
                header ('location: identification.php?pass_invalide'); 
          }  
    }  
    else { 
                header ('location: identification.php');
    } 
?>