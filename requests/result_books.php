<?php
require PATH_PROJECT . '/views/pagination.php';

foreach($results as $result) : ?>
	<article class="book">
		<div class="picture">
			<img src="<?php echo "./couvertures/{$result->file_name}"; ?>" alt="<?php echo $result->titre ?>">
		</div>
		<div class="description">
			<h2>Titre : <?php echo sanitize_html($result->titre); ?></h2>
			<h3>Auteur : <?php echo sanitize_html($result->prenom . ' ' . $result->nom); ?></h3>
			<h3>Année de parution : <?php echo sanitize_html($result->annee); ?></h3>
			<h3>Genre : <?php echo sanitize_html($result->libelle); ?></h3>
			<?php
			
			$text = sanitize_html($result->resume);
			
			?>
			<div class="resume">Resumé : <p><?php echo resumeString($text, 150); ?></p></div>
			<!-- je crée un lien vers status.php pour connaitre les détails du livre cliqué, pour cela je renseigne son id -->
			<a href="<?php echo "./status.php?id={$result->livre_id}"; ?>">Détails du livre : <?php echo sanitize_html($result->titre); ?></a>
		</div>
	</article>
<?php
endforeach;
require PATH_PROJECT . '/views/pagination.php'; ?>