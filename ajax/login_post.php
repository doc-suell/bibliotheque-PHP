<?php
require dirname(__DIR__) . '/functions.php';
require_once PATH_PROJECT . '/connect.php';

// https://www.php.net/manual/fr/function.filter-var.php
$email = filter_var(mb_strtolower(trim($_POST['email'])), FILTER_VALIDATE_EMAIL);
$password = trim($_POST['password']);

if(!$email) {
	$msg_error = '<div class="green">Merci de renseigner un email valide</div>';
}
else {
	$req = $db->prepare("
		SELECT u.*, l.prenom, l.nom, r.titre, r.label
		FROM user u
		LEFT JOIN lecteur l
		ON l.id = u.lecteur_id
		INNER JOIN role r
		ON r.id = u.role_id
		WHERE u.email = :email
	");

	$req->bindValue(':email', $email, PDO::PARAM_STR);
	$req->execute();

	$user = $req->fetch(PDO::FETCH_OBJ);

	if(!$user) { // pas de réponse de la base de données donc email inconnu
		$msg_error = '<div class="green">L\'email ou le mot de passe n\'existe pas</div>';
	}
	elseif(!password_verify($password, $user->password)) { // password faux
		$msg_error = '<div class="green">L\'email ou le mot de passe n\'existe pas</div>';
	}
	else {
		// email et password OK
		$_SESSION['id_user'] 	= $user->id;
		$_SESSION['first_name'] = $user->prenom;
		$_SESSION['last_name'] 	= $user->nom;
		$_SESSION['email'] 		= $user->email;
		$_SESSION['id_role'] 	= $user->role_id;
		$_SESSION['role_name'] 	= $user->titre;
		$_SESSION['role_slug'] 	= $user->label;

		$msg_success = "<div class='green'>Bonjour {$user->prenom}, vous êtes bien connecté</div>";
	}
}
/*
je crée un tableau c'est lui qui va stocker les infos à renvoyer à la requête ajax (sera mis en JSON)
*/
$results = array(); 
if(isset($msg_error)) {
	$results['msg'] = $msg_error;
	$results['connect'] = FALSE; // donc j'affiche uniquement le message d'échec
	// $results = array(
	// 	'msg' => $msg_error,
	// 	'connect' => FALSE
	// );
}
elseif(isset($msg_success)){
	$results['msg'] = $msg_success;
	$results['connect'] = TRUE; // donc j'affiche le message de succès pendant 3 secondes et je rafraichis la page pour charger la nouvelle SESSION
}
else {
	$results['msg'] = 'Erreur inattendue';
	$results['connect'] = FALSE;
}

// je précise que le contenu est en format JSON en spécifiant son type MIME
header('Content-Type: application/json');
// encode le tableau results en JSON
echo json_encode($results);