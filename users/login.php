<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'database/patient_queries.php';

	// parse the string to query to assoc array
	parse_str($_SERVER['QUERY_STRING'], $parsed_query);
	// the url to redirect to
	$success_url = $parsed_query['successUrl'] ?? null;
	$login_form_url = PROJECT_URL . "users/login-form.php";


	// check if the required parameters exist in the request
	if (isset($_POST['username'], $_POST['password'])) {
		// perform a query for the user details
		try {
			// query the user with the specified username
			$found_user = $patient_queries->login($_POST['username'], $_POST['password']);

			// throw an error if the user wasn't found based on given credentials
			if (empty($found_user)) {
				throw new Exception('User with the given credentials was not found');
			}

			// greet the user
			create_flash_message('login_result',
				"Welcome <strong>${found_user['fullName']}</strong>, you have logged in as a ${found_user['userType']}",
				FLASH::PRIMARY
			);

			// save the current user in the session
			$_SESSION['current_user'] = $found_user;

			$user_dashboard_url = ($found_user['userType'] === 'administrator' ? 'admin' : 'patient') . '/index.php';
			$redirect_url = PROJECT_URL . first_valid_redirect_url('successUrl', $user_dashboard_url);

			// redirect the user
			header("Location: $redirect_url");
		} catch (Exception $e) {
			$redirect_url = $login_form_url . (is_null($success_url) ? '' : "?successUrl=$success_url");
			create_flash_message('login_result', $e->getMessage(), FLASH::ERROR);
			header("Location: $redirect_url");
		}
	} else {
		header("Location: $login_form_url");
	}