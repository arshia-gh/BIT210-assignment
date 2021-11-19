<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'database/patient_queries.php';

	// the url to redirect to
	$redirect_url = $_GET['redirectUrl'] ?? null;
	$login_form_url = PROJECT_URL . "users/login-form.php";


	// check if the required parameters exist in the request
	if (isset($_POST['username'], $_POST['password'])) {
		// perform a query for the user details
		try {
			// query the user with the specified username
			$username = htmlspecialchars($_POST['username']);
			$password = htmlspecialchars($_POST['password']);
			$found_user = $patient_queries->login($_POST['username'], $_POST['password']);

			// throw an error if the user wasn't found based on given credentials
			if (is_null($found_user)) {
				throw new Exception('User with the given credentials was not found');
			}

			// greet the user
			create_flash_message(
				"${found_user['username']} login result", // set the flash id to username + ' login result'
				"Welcome <strong>${found_user['fullName']}</strong>, you have logged in as a ${found_user['userType']}",
				FLASH::PRIMARY
			);

			// save the current user in the session
			$_SESSION['current_user'] = $found_user;

			$user_dashboard_url = ($found_user['userType'] === 'administrator' ? 'admin' : 'patient') . '/index.php';
			$final_redirect_url = PROJECT_URL . (is_null($redirect_url) ? $user_dashboard_url : $redirect_url);

			// redirect the user
			header("Location: $final_redirect_url");
		} catch (Exception $e) {
			$final_redirect_url = $login_form_url . (is_null($redirect_url) ? '' : "?redirectUrl=$redirect_url");
			create_flash_message('login result', $e->getMessage(), FLASH::ERROR);
			header("Location: $final_redirect_url");
		}
	} else {
		header("Location: $login_form_url");
	}