<?php
require __DIR__ . '/header.php';
require_once PATH_PROJECT . '/connect.php';

/*
afficher tous les livres avec
- couverture du livre
- titre du livre
- prénom et nom de l'auteur
- année d'édition
- genre du livre
- résumé du livre (environ 200 caractères et ne pas couper au milieu d'un mot)
- lien vers page du livre
- pagination avec 6 livres par page

tout l'affichage des livres devra être dans un container
*/
$req = $db->query("
	SELECT COUNT(id) AS count_books
	FROM livre
");
$result = $req->fetchObject();

$count_books = $result->count_books;
$per_page = 6;
$nb_pages = ceil($count_books/$per_page);

// on détermine si l'utilisateur a déjà navigué :
// si oui, on récupére la page courante
// si non, on remet à la première page
if(isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $nb_pages) {
	$current_page = $_GET['page'];
}
else {
	$current_page = 1;
}

$req = $db->prepare("
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
");
$offset = ($current_page - 1) * $per_page;
$req->bindValue(':offset', $offset, PDO::PARAM_INT);
$req->bindValue(':per_page', $per_page, PDO::PARAM_INT);
$req->execute();
$results = $req->fetchAll(PDO::FETCH_OBJ); ?>
<h1 class="text_center">Ma bibliothèque</h1>
<?php 
// on ne prend que les genres ayant au moins 1 livre
$req = $db->query("
	SELECT DISTINCT g.*
	FROM genre g
	INNER JOIN livre l
	ON g.id = l.genre_id
	ORDER BY g.libelle
");
?>
<!-- on affiche le bouton ajouter un livre si connecté en administrateur -->
<?php if(isset($_SESSION['id_user']) && $_SESSION['role_slug'] == 'administrator') : ?>
	<div id="add_book" class="cursor_pointer text_center"><i class="fa-solid fa-circle-plus fa-2x"></i>Ajouter un livre</div>
<?php endif; ?>

<!-- filtre des genres -->
<div id="select_genre">
	<div id="display_genre">Choisir un genre</div>
	<div id="genre_items">
		<?php while($genre = $req->fetch(PDO::FETCH_OBJ)) : ?>
			<div class="genre_choice" data-genre-id="<?php echo $genre->id ?>"><?php echo $genre->libelle ?></div>
		<?php endwhile; ?>
	</div>
</div>

<!-- contenu de la page -->
<main id="books">

	<?php require PATH_PROJECT . '/requests/result_books.php'; ?>

</main>

<div id="display_popup"></div>
<?php
require PATH_PROJECT . '/views/footer.php';

