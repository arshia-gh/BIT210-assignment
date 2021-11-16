<?php
	include_once '../includes/app_metadata.inc.php';
	parse_str($_SERVER['QUERY_STRING'], $query_string_assoc);
	$redirect_url = first_valid_redirect_url('redirectUrl', $_SERVER['HTTP_REFERER'] ?? PROJECT_URL . 'index.php');
	if (isset($_SESSION, $_SESSION['current_user'])) {
		unset($_SESSION['current_user']);
	}
	header("Location: $redirect_url");
