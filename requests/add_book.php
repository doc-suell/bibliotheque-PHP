<?php 
require dirname(__DIR__) . '/functions.php';
require_once PATH_PROJECT . '/connect.php';


// $fail_upload 		= array(3,6,7,8);
// $oversize_file 		= array(1,2);
// $default_picture 	= 'no-image.jpg';
// $extension 			= array('png', 'jpg', 'jpeg', 'gif');
// $size_max 			= 1048576;

if(in_array('', $_POST)) :
	$msg_error = 'Merci de remplir le titre et le contenu de ;
	// header('Location:' . HOME_URL . 'ajax/forms/add_book.php?msg=' . $msg_error);
else :
	
	$titre 		= trim($_POST['titre']);
	$prenom 		= trim($_POST['prenom']);
	$nom 	= trim($_POST['nom']);
	$annee 		= trim($_POST['annee']);
	$libelle 		= trim($_POST['libelle']);
	$resume 		= trim($_POST['resumeq']);
	


		if($set_request) :
			$req = $db->prepare("
				INSERT INTO articles(id_user, title, content, picture, created_at)
				VALUES (:id_user, :title, :content, :picture, NOW())
			");

			// https://www.php.net/manual/fr/function.intval.php
			$req->bindValue(':id_user', intval($_SESSION['id_user']), PDO::PARAM_INT); // integer
			$req->bindValue(':title', $title, PDO::PARAM_STR); // string
			$req->bindValue(':content', $text, PDO::PARAM_STR); // string
			$req->bindValue(':picture', $img_name, PDO::PARAM_STR); // string

			// $result va stocker le résultat de ma requete INSERT INTO
			// si TRUE l'insertion s'est bien déroulé
			// si FALSE une erreur s'est produite
			$result = $req->execute();
			if($result) :
				$msg_success = 'Article correctement créé';
			else:
				$msg_error = 'Erreur lors de la soumission du formulaire, merci de retenter dans quelques instants';
			endif;

		else :
			$msg_error = 'Erreur lors du transfert, merci de retenter dans quelques instants';
		endif;
		
	endif;
	

endif;

if(isset($msg_error)) {
	header('Location:' . HOME_URL . 'views/add_article.php?msg=' . $msg_error . '&title=' . $title . '&content=' . $text);
}
else {
	header('Location:' . HOME_URL . '?msg=<div class="green">' . $msg_success . '</div>');
}

