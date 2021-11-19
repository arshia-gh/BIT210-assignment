<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'database/patient_queries.php';

	$required_fields = [
		'username',
		'password',
		'healthcareAddress',
		'healthcareName',
		'fullName',
		'ICPassport',
		'emailAddress',
		'staffID',
		'userType'
	];

	// the url to redirect to
	$redirect_url = $_GET['redirectUrl'] ?? NULL;

	$register_form_url = PROJECT_URL . "users/register-form.php";
	$login_form_url = PROJECT_URL . "users/login-form.php";

	// to check if the fields exist in the request body
	function required_fields_exists(array $array) : bool
	{
		foreach ($array as $field) {
			if (!isset($_POST[$field])) return FALSE;
		}
		return TRUE;
	}

	// sanitize the inputs
	function sanitize_inputs($array) : array
	{
		$sanitized_array = [];
		foreach ($array as $field) {
			if (isset($_POST[$field])) {
				$sanitized_array[$field] = htmlspecialchars($_POST[$field]);
			}
		}
		return $sanitized_array;
	}

	if (required_fields_exists($required_fields)) {
		try {
			// sanitize the inputs
			$inputs = sanitize_inputs($required_fields);
			$userType = $inputs['userType'];

			// common fields between patient and administrator
			$query_fields = [
					$inputs['username'],
					$inputs['password'],
					$inputs['emailAddress'],
					$inputs['fullName']
			];

			// add the special field depending on the userType
			if ($userType === 'administrator') {
				$query_fields[] = $inputs['staffID'];
			} else if ($userType === 'patient') {
				$query_fields[] = $inputs['ICPassport'];
			// redirect if the user type is invalid (the error will be caught and user will be redirected)
			} else {
				throw new Exception('Invalid user type, please try again');
			}

			// add userType as the last field
			$query_fields[] = $userType;
			// insert the data into the database
			$isAdded = $patient_queries->register(...$query_fields);

			// if the query is successful $isAdded will be true
			if ($isAdded) {
				// redirect the message with a confirmation message
				create_flash_message(
					'registration result',
					"<strong class='text-uppercase'>$userType</strong> account was successfully created for 
							<strong class='text-uppercase'>{$inputs['fullName']}</strong>",
					FLASH::SUCCESS
				);
				header("Location: $login_form_url");
				return;
			}

			// if somehow the user was not added redirect them
			throw new Error ('registration failed');
		} catch (Exception $e) {
			// catch any error and redirect the user to registration form
			$final_redirect_url = $register_form_url . (is_null($redirect_url) ? '' : "?redirectUrl=$redirect_url");
			create_flash_message('registration result', $e->getMessage(), FLASH::ERROR);
			header("Location: $final_redirect_url");
		}
	} else {
		header("Location: $register_form_url");
	}