<?php

	header('Cache-Control: no-store, no-cache, must-revalidate');

	session_start([
		'cookie_lifetime' => 86400,
		'cookie_httponly' => TRUE
	]);

	const PROJECT_DIR = "BIT210-assignment/";
	define('PROJECT_PATH', "{$_SERVER['DOCUMENT_ROOT']}/" . PROJECT_DIR);
	define('PROJECT_URL', "http://{$_SERVER['HTTP_HOST']}/" . PROJECT_DIR);
	const COPYRIGHT_TEXT = 'PCVS - copyright&copy; 2021';
	const APP_SLOGAN = 'Private Covid-19 Vaccination Service ';
	const APP_NAME = 'PCVS';

	set_include_path(PROJECT_PATH);


	/**
	 * Gets the current user or redirect the user to the login page
	 *
	 * @return array|null the authenticated user. returns null on failure
	 */
	function authenticate(bool $redirectOnFailure = TRUE) : array|null
	{
		if (isset($_SESSION, $_SESSION['current_user'])) {
			$user = $_SESSION['current_user'];
			$dir = basename(dirname($_SERVER['PHP_SELF']));
			if ($user['userType'] === 'administrator' && $dir == 'admin') {
				return $user;
			} else if ($user['userType'] === 'patient' && $dir == 'patient') {
				return $user;
			}
		}
		if ($redirectOnFailure) {
			redirect_to_login_form();
		}
		return NULL;
	}

	function redirect_to_login_form()
	{
		header('Location: ' . PROJECT_URL . 'users/login-form.php');
		exit();
	}

	// parse query string and initialize a constant
	parse_str($_SERVER['QUERY_STRING'], $temp_str_query_assoc);
	define('STRING_QUERY', $temp_str_query_assoc);
	unset($temp_str_query_assoc);

	function first_valid_redirect_url(string $key, ?string ...$rollbacks) : null|string
	{
		parse_str($_SERVER['QUERY_STRING'], $string_query_assoc);
		if (isset($string_query_assoc[$key])) return PROJECT_URL . $string_query_assoc[$key];
		foreach ($rollbacks as $url) {
			if (!is_null($url)) {
				return $url;
			}
		}
		return NULL;
	}

	function statusToColor($status) {
		switch ($status) {
			case "pending": return "secondary"; break;
			case "confirmed": return "primary"; break;
			case "rejected": return "danger"; break;
			case "administered": return "success"; break;
		}
	}