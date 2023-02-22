<?php
session_start(); // obligatoire pour utiliser les sessions

// var_dump($_SERVER);
// on définit une constante de cette façon
// define('NOM_DE_LA_CONSTANTE_EN_MAJUSCULE', 'VALEUR_DE_LA_CONSTANTE');
define('HOME_URL', 'http://bibliotheque/'); // à chaque migration, cet élément sura surement à changer
define('PATH_PROJECT', __DIR__);

// function pour rediriger vers la homePage si $enable_access existe et n'est pas nul
function enabled_access(Array $enabled_access) {
	if(
		!isset($_SESSION['id_user'])  // si je ne suis pas connecté
		|| // OR
		(
			isset($enable_access)
			&& // ET
			isset($_SESSION['id_user'])
			&&
			// si le rôle n'est pas dans le tableau
			!in_array($_SESSION['role_slug'], $enable_access)
		)
	) :
		header('Location: ' . HOME_URL);
	endif;
}


// pour éliminer la faille XSS
function sanitize_html($string) {
	// https://www.php.net/manual/fr/function.htmlspecialchars.php
	return htmlspecialchars(trim($string));
}

// pour ne mettre que la première lettre en majuscule
function mb_ucfirst($string) {
	// je mets la chaine de caractère en minuscule
	$string = mb_strtolower(trim($string));
	// je récupère la première lettre de la chaîne
    $firstChar = mb_substr($string, 0, 1);
    // je récupère le reste de la chaîne (sans le premier caractère)
    $then = mb_substr($string, 1);
    return mb_strtoupper($firstChar) . $then;
}

// function pour checker le password
function check_password($pass) {
	preg_match('#^(?=(.*[A-Z])+)(?=(.*[a-z])+)(?=(.*[\d])+)(?=.*\W)(?!.*\s).{8,16}$#', $pass, $match);
	if(empty($match)) {
		return FALSE;
	}
	return TRUE;
}

// pour mettre au pluriel
function plural($count) {
	return $count > 1 ? 's' : '';
}

// limite le résumé sans le couper au milieu d'un mot
function resumeString($string, $length = 200) {
	if(strlen($string) > $length) {
		$text_cut = substr($string, 0, $length);
		$text_length = strlen(strrchr($text_cut, ' '));
		$final_text = substr($text_cut, 0, $length - $text_length);
		return $final_text . ' ...';
	}
	return $string;
}