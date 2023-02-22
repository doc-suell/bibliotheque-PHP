<?php
require dirname(dirname(__DIR__)) . '/functions.php';
require_once PATH_PROJECT . '/connect.php';

$author = trim($_POST['author']);

$req = $db->prepare("
	SELECT prenom, nom, id
	FROM auteur
	WHERE CONCAT(prenom, nom) LIKE :search
");
$req->bindValue(':search', '%'.$author.'%', PDO::PARAM_STR);

$req->execute();
$results = $req->fetchAll(PDO::FETCH_OBJ);

if($results) {
	foreach($results as $aut) {
		echo "<div class=\"choice_author\" data-author-id='{$aut->id}'>{$aut->prenom} {$aut->nom}</div>";
	}
}
else {
	echo '<div class="red">Pas de résultat</div>';
}