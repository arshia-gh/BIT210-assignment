<?php
	header('Cache-Control: no-store, no-cache, must-revalidate');

	session_start([
		'cookie_lifetime' => 86400,
		'cookie_httponly' => TRUE
	]);

	const PROJECT_DIR = "BIT210-assignment/";
	define('PROJECT_PATH', "{$_SERVER['DOCUMENT_ROOT']}/" . PROJECT_DIR );
	define('PROJECT_URL', "http://{$_SERVER['HTTP_HOST']}/" . PROJECT_DIR);
	const COPYRIGHT_TEXT = 'PCVS - copyright&copy; 2021';
	const APP_SLOGAN = 'Private Covid-19 Vaccination Service ';
	const APP_NAME = 'PCVS';

	set_include_path(PROJECT_PATH);

	/**
	 * Gets the current user or redirect the user to the login page
	 * @return array|null the authenticated user. returns null on failure
	 */
	function authenticate(bool $redirectOnFailure = true): array|null
	{
		if (isset($_SESSION, $_SESSION['current_user'])) {
			return $_SESSION['current_user'];
		} else if ($redirectOnFailure) {
			header('Location: ' . PROJECT_URL . 'users/login-form.php');
			exit();
		}
		return null;
	}

	// parse query string and initialize a constant
	parse_str($_SERVER['QUERY_STRING'], $temp_str_query_assoc);
	define('STRING_QUERY', $temp_str_query_assoc);
	unset($temp_str_query_assoc);

	function first_valid_redirect_url(string $key, ?string ...$rollbacks) : null|string
	{
		parse_str($_SERVER['QUERY_STRING'], $string_query_assoc);
		if (isset($string_query_assoc[$key])) return PROJECT_URL . $string_query_assoc[$key];
		foreach($rollbacks as $url) {
			if (!is_null($url)) {
				return $url;
			}
		}
		return null;
	}