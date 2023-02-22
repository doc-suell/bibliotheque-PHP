<?php
require dirname(__DIR__) . '/functions.php';
require_once PATH_PROJECT . '/connect.php';
// pour mettre un tableau php en string en spécifiant un délimiteur
// https://www.php.net/manual/fr/function.implode.php
// A partir d'un string, il va séparer des éléments grâce à un caractère indiqué dans l'explode et transformer les valeurs en tableau
// https://www.php.net/manual/fr/function.explode.php

// on vérifie que l'ajax renvoie bien un id sinon on affiche tout
$ids = isset($_POST['genre']) ? $_POST['genre'] : FALSE;

if($ids) {
	$SQLVar = ':id_' . implode(',:id_', $ids);
	$query = "
		SELECT COUNT(id) AS count_books
		FROM livre
		WHERE genre_id IN ($SQLVar)
	";
}
else {
	$query = "
		SELECT COUNT(id) AS count_books
		FROM livre
	";
}

	
$req = $db->prepare($query);

if($ids) {
	foreach($ids as $id) {
		$req->bindValue(":id_$id",$id,PDO::PARAM_INT);
	}
}

$req->execute();

$result = $req->fetchObject();
$count_books = $result->count_books;
$per_page = 6;
$nb_pages = ceil($count_books/$per_page);

// je vérifie si la page est une url ou un nombre
// si url c'est qu'on veut naviguer
// si nombre on veut filtrer
if(is_numeric($_POST['page'])) {
	$page = intval($_POST['page']);
}
else {
	// je recupère la valeur du $_GET dans l'url envoyé en string
	// https://www.php.net/manual/fr/function.parse-str.php
	parse_str($_POST['page'],$output);
	// var_dump($output);
	$page = array_values($output)[0];
}

if(isset($page) && $page > 0 && $page <= $nb_pages) {
	$current_page = $page;
}
else {
	$current_page = $nb_pages;
}
$offset = ($current_page - 1)*$per_page;

if($ids) {
	$query = "
		SELECT i.id id_picture, i.file_name, l.titre, a.prenom, a.nom, l.annee, g.libelle, l.resume, l.id livre_id
		FROM livre l
		LEFT JOIN auteur a 
		ON l.auteur_id = a.id 
		LEFT JOIN genre g 
		ON l.genre_id = g.id
		LEFT JOIN illustration i 
		ON l.illustration_id = i.id
		WHERE l.genre_id IN ($SQLVar)
		LIMIT :offset, :per_page
	";
}
else {
	$query = "
		SELECT i.id id_picture, i.file_name, l.titre, a.prenom, a.nom, l.annee, g.libelle, l.resume, l.id livre_id
		FROM livre l
		LEFT JOIN auteur a 
		ON l.auteur_id = a.id 
		LEFT JOIN genre g 
		ON l.genre_id = g.id
		LEFT JOIN illustration i 
		ON l.illustration_id = i.id
		ORDER BY l.annee DESC
		LIMIT :offset, :per_page
	";
}

$req = $db->prepare($query);
	
$req->bindValue(':offset', $offset, PDO::PARAM_INT);
$req->bindValue(':per_page', $per_page, PDO::PARAM_INT);
if($ids) {
	foreach($ids as $id) {
		$req->bindValue(":id_$id",$id,PDO::PARAM_INT);
	}
}

$req->execute();
$results = $req->fetchAll(PDO::FETCH_OBJ);

ob_start();
require PATH_PROJECT . '/requests/result_books.php';
$books = ob_get_clean();

$datas = array();

$datas['books'] = $books;
$datas['page'] = $current_page;

header('Content-Type: application/json');
echo json_encode($datas);
