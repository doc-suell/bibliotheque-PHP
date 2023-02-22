<?php
require dirname(dirname(__DIR__)) . '/functions.php';
require_once PATH_PROJECT . '/connect.php';
?>
<div class="popup_ajax_content">
	<div class="close_popup cursor_pointer"><?php echo file_get_contents(PATH_PROJECT . '/assets/img/close.svg'); ?></div>
	<h1>Formulaire ajouter un livre</h1>

	<form id="add_book_form" action="<?php echo HOME_URL . 'ajax/forms/add_form_post.php'; ?>" method="POST" enctype="multipart/form-data" >
		<div>
			<label for="isbn">ISBN</label>
			<input type="text" id="isbn" name="isbn">
		</div>
		<div>
			<label for="titre">Titre<span class="red">*</span></label>
			<input type="text" id="titre" name="titre">
		</div>
		<div>
			<label for="resume">Résumé<span class="red">*</span></label>
			<textarea id="resume" name="resume"></textarea>
		</div>
		<div>
			<label for="annee">Année d'édition</label>
			<input type="text" id="annee" name="annee">
		</div>
		<div>
			<label for="choice_author">Choisir un auteur<span class="red">*</span></label>
			<!-- // pas de name, servira que pour l'autocomplete -->
			<input type="text" id="choice_author" autocomplete="off">
			<!-- pour l'affichage des résultats -->
			<div id="result_author"></div>
			<!-- // pour envoyer l'id de l'auteur au formulaire -->
			<input type="hidden" name="author" id="author">
			<div class="btn_format add_author">Ajouter un auteur</div>
		</div>
		<div>
			<label for="genre">Genre</label>
			<select id="genre" name="genre">
				<?php
				$req = $db->query("SELECT * FROM genre");
				// var_dump($req->fetchAll(PDO::FETCH_OBJ))
				while($genre = $req->fetch(PDO::FETCH_OBJ)) :
					echo "<option value='{$genre->id}'>{$genre->libelle}</option>";
				endwhile;
				?>
			</select>
		</div>
		<div>
			<label for="picture">Ajouter la couverture</label>
			<input type="hidden" name="MAX_FILE_UPLOAD" value="1048576">
			<input type="file" id="picture" name="picture">
		</div>
		<div class="msg_error"></div>
		<button type="submit">Créer Livre</button>
	</form>
</div>