(function($) { // fonction anonyme qui déclare jQuery par un $

	// oblige la déclaration de variable
	"use strict";

	/*
	va attendre que la page soit chargée pour executer
	les scripts qu'il contient
	*/
	$(document).ready(function() {  
		$(".to_connect").on('click', function() {
			$(this).next().slideToggle();
		});
	});
})(jQuery);