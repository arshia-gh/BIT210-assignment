<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'patient/partials.php';

	$redirect_url = isset($_GET['redirectUrl'])
		? PROJECT_URL . $_GET['redirectUrl']
		: $_SERVER['HTTP_REFERER']
		?? PROJECT_URL . 'index.php';
	if (isset($_SESSION, $_SESSION['current_user'])) {
		create_flash_message('logout result', 'Successfully logged out', FLASH::PRIMARY);
		unset($_SESSION['current_user']);
	}
	header("Location: $redirect_url");
