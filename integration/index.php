<?php include('header.php');


if( isset($_GET['page']) ) 
    $page=$_GET['page'];
else 
 $page='accueil';

switch ($page) {
    case "accueil":
        $titre = 'Allovitre';
		$meta_description = 'description meta';
        $fil_ariane = '<a href="/">ACCUEIL</a> >';
        break;
    case "creer_compte":
        $titre = 'Allovitre';
        $meta_description = 'description meta';
        $fil_ariane = '<a href="/">ACCUEIL</a> >';
        break;
    case "categorie":
        $titre = 'Allovitre';
        $meta_description = 'description meta';
        $fil_ariane = '<a href="/">ACCUEIL</a> >';
        break;
    case "identification":
        $titre = 'Allovitre';
        $meta_description = 'description meta';
        $fil_ariane = '<a href="/">ACCUEIL</a> >';
        break;
    case "produit":
        $titre = 'Allovitre';
		$meta_description = 'description meta';
        $fil_ariane = '<a href="/">ACCUEIL</a> >';
        break;
}
if(file_exists('pages/'.$page.'.php'))
    include('pages/'.$page.'.php');
include('footer.php');
?>