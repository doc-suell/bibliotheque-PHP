(function($) {
	// récupération de la variable jsVAR définie dans footer.php
	let homeUrl = jsVAR.homeUrl;
	let displayPopup = $("#display_popup");
	let body = $("body");
	$("#add_book").on('click', function() {
		let url = homeUrl + 'ajax/forms/add_book.php';

		// $post(url, datas(object), callback);
		// displayPopup.load(url);
		$.post(
			url,
			{
			},
			function(response) {
				// affichage du formulaire
				displayPopup.html(response).addClass('popup_ajax_open');
				body.addClass('overflow_hidden');
				let msgDisplay = $('.msg_error');
				$(".close_popup").on('click', function() {
					displayPopup.removeClass('popup_ajax_open');
					body.removeClass('overflow_hidden');
				});
				let displayAuthor = $('#result_author');

				// autocomplete auteur
				$("#choice_author").on('input', function() {
					let author = $(this).val();
					let url = homeUrl + 'ajax/forms/choice_author.php';
					if(author.length > 2) {
						$.post(
							url,
							{
								author: author
							},
							function(data) {
								displayAuthor.html(data)
								displayAuthor.slideDown();
								let addAuthor = $('.add_author');
								if(data.length == 38) {
									addAuthor.fadeIn();
								}
								else {
									addAuthor.fadeOut();
								}
								$('.choice_author').on('click', function() {
									let authorId = $(this).data('author-id');
									let authorName = $(this).text();
									$('#author').val(authorId);
									$('#choice_author').val(authorName);
									displayAuthor.slideUp();
								});
							}
						);
					}
					else {
						displayAuthor.slideUp();
					}
				});
				$("#add_book_form").on('input', function() {
					$('input, textarea').removeClass('input_red');
					msgDisplay.empty();
				});
				// soumission formulaire
				$("#add_book_form").on('submit', function(e) {
					e.preventDefault();
					$('input, textarea').removeClass('input_red');
					msgDisplay.empty();
					let form = $(this);
					// on récupére l'url où va être soumis le formulaire
					let url = form.attr('action');
					let method = form.attr('method');
					// je récupére toutes les données du formulaire avec le FormData sous form d'objet
					let formdata 	= new FormData(form[0]);
					let data 		= (formdata !== null) ? formdata : form.serialize();
					// je désactive le bouton de soumission du formulaire
					let submitBtn = $(this).children('button');
					// submitBtn.prop('disabled', true);
					$.ajax({
						url: url,
						method: method,
						// les 2 paramètres suivant sont impératifs pour le FormData
						// et surtout avec envoi de fichier
						contentType: false,
						processData: false,
						dataType: 'JSON',
						data: data,
						success: function(data) {
							msgDisplay.html(data.msg);
							if(data.result) {
								setTimeout(function() { window.location.reload; }, 2000);
							}
							else {
								submitBtn.removeAttr('disabled');
								if(data.empty_field) {
									$('span.red').each(function() {
										let fieldForm = $(this).parent().next();
										let fieldValue = fieldForm.val();
										if(fieldValue.length == 0) {
											fieldForm.addClass('input_red');
										}
									})
								}
							}

						},
						// error: function() {

						// },
						// progress: function() {

						// }

					})
				});
			}
		);
	});
	
	$(function() {
		// je mets dans des variables des éléments
		// qui vont être utilisés plus d'une fois
		let displayGenre = $("#display_genre");
		let books = $("#books");

		// slide pour le select des genres
		displayGenre.on('click', function() {
			$(this).toggleClass('genre_open').next().slideToggle();
		});
		// pour refermer la popup si on clique à côté d'elle
		$(document).on('click', function(e) {
			// j'analyse le click et je vérifie qu'il ne correspond pas à la corbeille ni à la popup
			// Sinon il refermerait tout de suite la popup
			if(!$(e.target).closest('#display_genre,#genre_items').length) {
				if(displayGenre.hasClass('genre_open')) {
					displayGenre.trigger('click');
				}
			}
		});
		// tableaux pour stocker les genres choisis
		let genre = []; // ids
		let genreName = []; //  noms
		// init de la navigation de page
		let page = 1;

		// je stocke le contenu "choisisser un genre"
		let initSelect = displayGenre.text();
		// script pour ajouter les genres choisis et l'envoie
		// en ajax au serveur et réception des résultats
		$(".genre_choice").on('click', function() {
			$(this).toggleClass('red');
			let choice = $(this).data('genre-id'); // id
			let choiceName = $(this).text(); // nom du genre
			// je regarde si le choix existe dans mon tableau
			// et si oui à quelle position
			// si résultat -1 c'est qu'il n'est pas présent
			let posChoice = genre.indexOf(choice);
			let posName = genreName.indexOf(choiceName);
			// si le choix n'existe pas je le rajoute
			if(posChoice < 0) {
				genre.push(choice);
			}
			// s'il existe, je l'enlève
			else {
				genre.splice(posChoice, 1);
			}
			if(posName < 0) {
				genreName.push(choiceName);
			}
			else {
				genreName.splice(posName, 1);
			}
			// j'ajoute les choix dans la une variable
			let genreChoice = genreName.join(' | ');
			// si il y a des choix
			if(genreChoice.length > 0) {
				displayGenre.text(genreChoice);
			}
			// si pas de choix, je remets la phrase du départ
			else {
				displayGenre.text(initSelect);
			}
			let url = homeUrl+'ajax/filter_genre.php';
			$.post(
				url,
				{
					genre: genre,
					page: page
				},
				function(data) {
					books.html(data.books);
					page = data.page;
				},'JSON'
			);
		});

		// mise en AJAX de la pagination
		$(document, ".navigation a").on('click', ".navigation a", function(e) {
			e.preventDefault();
			let currentChoiceIds = genre;
			let urlPage = $(this).attr('href'); 
			let url = homeUrl+'ajax/filter_genre.php';
			$.post(
				url,
				{
					genre: genre,
					page: urlPage
				},
				function(data) {
					books.html(data.books);
					page = data.page;
				},'JSON'
			);
		})

		$("#connect_user").on('submit', function(e) {
			// pour empêcher la soumission du formulaire par l'html
			e.preventDefault();
			// je récupère l'url en récupérant la valeur de l'attribut action
			let form = $(this);
			let url = form.attr('action');
			let email = $("#email").val();
			let password = $("#password").val();
			let msgConnect = $(".msg_connect");
			// $.post(url, datas, callback);
			// départ le la fonction AJAX
			$.post(
				url,
				{
					// key: value
					email: email,
					password: password
				},
				// Réponse du serveur
				function(data) {
					if(data.connect) { // l'user est connecté
						msgConnect.html(data.msg);
						// on attend 3 secondes puis on recharge la page
						setTimeout(function() {
							window.location.reload();
						}, 3000);
					}
					else {
						// erreur pn affiche le message correspondant
						msgConnect.html(data.msg);
					}
				},'JSON' // on spécifie qu'on attend une réponse au format JSON
			);
		});
	});
})(jQuery);