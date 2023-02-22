	<?php
	$jsVAR = array(
		'homeUrl' => HOME_URL
	);
	// var_dump(json_encode($jsVAR));
	?>

	<script>
		const jsVAR = <?php echo json_encode($jsVAR); ?>
	</script>
	<script type="text/javascript" src="<?php echo HOME_URL . 'assets/js/dist/scripts.min.js'; ?>"></script>
</body>
</html>