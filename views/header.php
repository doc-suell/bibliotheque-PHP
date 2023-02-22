<?php
require dirname(__DIR__) . '/functions.php';
// session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="<?php echo HOME_URL . 'assets/css/dist/main.min.css'; ?>">
</head>
<body>
	<header>
		<nav>
			<ul class="navbar">
				<li><a href="<?php echo HOME_URL; ?>">Home</a></li>
				<?php if(isset($_SESSION['id_user'])) : ?>
					<li><a href="<?php echo HOME_URL . 'requests/disconnect.php'; ?>">Déconnexion</a></li>
					<li>
						<ul>
							
						</ul>
					</li>
				<?php else: ?>
					<li>
						<ul>
							<li id="subscribe">S'inscrire</li>
							<li class="connect cursor_pointer">
								<div class="to_connect">Se connecter</div>
								<div class="modal_connect">
									<form action="<?php echo HOME_URL . 'ajax/login_post.php'; ?>" method="POST" id="connect_user" enctype="multipart/form-data">
										<p>Se connecter</p>
										<div>
											<label for="email">Email</label>
											<input type="text" name="email" id="email">
										</div>
										<div>
											<label for="password">Mot de passe</label>
											<input type="password" name="password" id="password">
										</div>
										<button type="submit">Envoyer</button>
										<!-- div en attente de la réponse AJAX -->
										<div class="msg_connect"></div>
									</form>
								</div>
							</li>
						</ul>
					</li>
				<?php endif; ?>
			</ul>
		</nav>
	</header>