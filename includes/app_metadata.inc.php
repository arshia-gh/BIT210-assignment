<?php
	header('Cache-Control: no-store, no-cache, must-revalidate');

	session_start([
		'cookie_lifetime' => 86400,
		'cookie_httponly' => TRUE
	]);

	const PROJECT_DIR = "BIT210-assignment/";
	define('PROJECT_PATH', "{$_SERVER['DOCUMENT_ROOT']}/" . PROJECT_DIR);
	define('PROJECT_URL', "http://{$_SERVER['HTTP_HOST']}/". PROJECT_DIR);
	const COPYRIGHT_TEXT = 'PCVS - copyright&copy; 2021';
	const APP_SLOGAN = 'Private Covid-19 Vaccination Service ';
	const APP_NAME = 'PCVS';

	set_include_path(PROJECT_PATH);

	function authenticate() {
		if (isset($_SESSION['current_user'])) {
			return $_SESSION['current_user'];
		}
		header("Location: " . PROJECT_URL . 'admin/index.php');
		exit();
	}