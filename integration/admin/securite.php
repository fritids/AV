    <?php

     
    // on teste si nos variables sont définies
	if (isset($_SESSION['email']) && isset($_SESSION['mdp'])) {
				$email=$_SESSION['email'];
				$mdp=$_SESSION['mdp'];
				$sql = "SELECT * FROM admin_user WHERE email LIKE '$email' AND mdp='$mdp'";
				$result = mysql_query($sql);
				if (mysql_num_rows($result) == 1) {
				// ok
				}
				else {
				// HS on détruit la session
				
				// on redirige vers l'identification
                header ('location: identification.php?ERROR'); 
				}
	}
	else {
		                header ('location: identification.php');
	}
	
    ?> 

