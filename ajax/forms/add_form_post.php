<?php
require dirname(dirname(__DIR__)) . '/functions.php';
require_once PATH_PROJECT . '/connect.php';

$ISBN 			= is_numeric(trim($_POST['isbn'])) ? $_POST['isbn'] : NULL;
$title 			= trim($_POST['titre']);
$resume 		= trim($_POST['resume']);
$year 			= is_numeric(trim($_POST['annee'])) ? strval($_POST['annee']) : NULL;
$author 		= intval($_POST['author']) && !empty($_POST['author']) && $_POST['author'] > 0 ? $_POST['author'] : FALSE;
$genre 			= intval($_POST['genre']);
$picture 		= $_FILES['picture'];

$required_fields = array($title, $resume, $author);

$empty_field 	= FALSE;
$send_request 	= FALSE;

$isbn_length 	= array(10,13);
$error_upload 	= array(3,6,7,8);
$error_size 	= array(1,2);

$enabled_ext 	= array('jpg', 'jpeg', 'png');

$default_img_id = 1;
$size_max 		= 1048576;
$picture 		= $_FILES['picture'];

if(in_array('', $required_fields)) :
	$msg_error = '<div class="red">Vous devez remplir le(s) champ(s) obligatoire(s)</div>';
	$empty_field = TRUE;
else:
	if(!isset($ISBN) || !in_array(strlen($ISBN), $isbn_length)) :
		$msg_error = '<div class="red">l\'ISBN doit être une série 10 ou 13 chiffres</div>';
	elseif(!isset($year) || $year > date('Y')) :
		if(!isset($year)) {
			$msg_error = '<div class="red">Renseigner une année en chiffres</div>';
		}
		else {
			$msg_error = '<div class="red">Renseigner une année inférieure à ' . date('Y') . '</div>';
		}
	elseif(!$author) :
		$msg_error = '<div class="red">Renseigner un auteur</div>';
	else :
		$error = $picture['error'];
		if(in_array($error, $error_upload)) :
			$msg_error = '<div class="red">Erreur au moment de l\'envoi</div>';
		elseif(in_array($error, $error_size)) :
			$msg_error = '<div class="red">Fichier trop volumineux, ne pas dépasser 1Mo</div>';
		else :
			if($error == 4) :
				$send_request = TRUE;
				$img_name = FALSE;
			else :
				$recept_img = $picture['name'];
				$image_size = $picture['size'];
				$tmp_name 	= $picture['tmp_name'];
				// https://www.php.net/manual/fr/function.pathinfo.php
				$ext_img = strtolower(pathinfo($recept_img, PATHINFO_EXTENSION));

				// on vérifie si l'extension est bien dans le tableau, sinon ce n'est pas une image
				if(!in_array($ext_img, $enabled_ext)) :
					$msg_error = '<div class="red">Votre fichier n\'est pas une image png, jpg ou jpeg</div>';
				elseif($image_size > $size_max) :
					$msg_error = '<div class="red">Fichier trop volumineux, ne pas dépasser 1Mo</div>';
				else :
					// on créé un nom de fichier unique et aléatoire pour éviter les doublons dans le FTP (sur le serveur dans le dossier assets/img)

					// https://www.php.net/manual/fr/function.uniqid.php
					$img_name = uniqid() . '_' . $recept_img;

					// facultatif :
					// on crée le dossier img s'il n'existe pas
					// https://www.php.net/manual/fr/function.mkdir.php
					// https://www.php.net/manual/fr/function.chmod.php

					// le @ n'affichera pas l'erreur (notice ou warning) si la fonction en retourne une
					@mkdir(PATH_PROJECT . '/couvertures/', 0755);

					// je crée une variable pour spécifier l'endroit où je vais stocker mon image
					$img_folder = PATH_PROJECT . '/couvertures/';
					// var_dump($img_folder);
					$dir = $img_folder . $img_name;
					// var_dump($dir);

					// https://www.php.net/manual/fr/function.move-uploaded-file.php
					$move_file = move_uploaded_file($tmp_name, $dir);

					if($move_file) :
						$send_request = TRUE;
					else :
						$send_request = FALSE;
					endif;
				endif;
			endif;
			if($send_request) :
				if($img_name) :
					$request = "
						INSERT INTO illustration(file_name)
						VALUES (:file_name);
						INSERT INTO livre(isbn, titre, resume, annee, auteur_id, genre_id, illustration_id)
						VALUES (:isbn, :titre, :resume, :annee, :auteur_id, :genre_id, LAST_INSERT_ID())
					";
				else :
					$request = "
						INSERT INTO livre(isbn, titre, resume, annee, auteur_id, genre_id, illustration_id)
						VALUES (:isbn, :titre, :resume, :annee, :auteur_id, :genre_id, :illustration_id)
					";
				endif;
				$req = $db->prepare($request);

				// https://www.php.net/manual/fr/function.intval.php
				$req->bindValue(':isbn', isset($ISBN) ? $ISBN : '0', PDO::PARAM_STR); // string
				$req->bindValue(':titre', $title, PDO::PARAM_STR); // string
				$req->bindValue(':resume', $resume, PDO::PARAM_STR); // string
				$req->bindValue(':annee', isset($year) ? $year : '0000', PDO::PARAM_STR); // string
				$req->bindValue(':auteur_id', $author, PDO::PARAM_INT); // string
				$req->bindValue(':genre_id', $genre, PDO::PARAM_INT); // string
				if($img_name) :
					$req->bindValue(':file_name', $img_name, PDO::PARAM_STR); // string
				else :
					$req->bindValue(':illustration_id', $default_img_id, PDO::PARAM_INT); // string
				endif;
				// $result va stocker le résultat de ma requete INSERT INTO
				// si TRUE l'insertion s'est bien déroulé
				// si FALSE une erreur s'est produite
				$result = $req->execute();
				if($result) :
					$msg_success = '<div class="green">Livre correctement créé</div>';
				else:
					$msg_error = '<div class="red">Erreur lors de la soumission du formulaire, merci de retenter dans quelques instants</div>';
				endif;
			else :
				$msg_error = '<div class="red">Erreur lors du transfert, merci de retenter dans quelques instants</div>';
			endif;
		endif;
	endif;
endif;

$datas = array();

if(isset($msg_error)) :
	$datas['result'] = FALSE;
	$datas['msg'] = $msg_error;
	$datas['empty_field'] = $empty_field;
elseif(isset($msg_success)) :
	$datas['result'] = TRUE;
	$datas['msg'] = $msg_success;
else :
	$datas['result'] = FALSE;
	$datas['msg'] = '<div class="red">Erreur inattendue, merci de renouveler votre création de livre</div>';
	$datas['empty_field'] = $empty_field;
endif;

header('Content-Type: application/json');
echo json_encode($datas);
