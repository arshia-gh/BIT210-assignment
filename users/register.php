<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'database/patient_queries.php';

	// required fields (includes both administrator and patient fields)
	// form is required to send all the fields regardless of the user type
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

			// save current form data. if the data is not valid,
			// the form data will rollbacks to the value of this cookie
			setcookie('registrationFormData', json_encode($inputs), time() + 86400, '/');
			$userType = $inputs['userType'];

			// ICPassport or staffID
			$special_field = null;
			$centreName = null;

			// set the special field depending on the userType
			if ($userType === 'administrator') {
				// this cookie is used to set the form user type
				setcookie('userType', 'administrator', time() + 86400, '/');

				// check if the staffID is unique
				if (!$patient_queries->isUniqueStaffID($inputs['staffID'])) {
					$msg = "An account with selected staff id (<strong>${inputs['staffID']}</strong>) is already registered. 
						    Kindly enter a new one";
					throw new Exception($msg);
				}

				$healthcare_centre = $patient_queries->find_healthcare_centre($inputs['healthcareName']);

				// create the healthcare centre if it doesn't exist
				if (is_null($healthcare_centre)) {
					$insert_result = $patient_queries->add_healthcare_centre($inputs['healthcareName'], $inputs['healthcareAddress']);
					$healthcare_centre = $patient_queries->find_healthcare_centre($inputs['healthcareName']);
				}

				$special_field = $inputs['staffID'];
				$centreName = $healthcare_centre['centreName'];

			} else if ($userType === 'patient') {
				$special_field = $inputs['ICPassport'];
				setcookie('userType', 'patient', time() + 86400, '/');

				// check if the ICPassport is unique
				if (!$patient_queries->isUniqueICPassport($inputs['ICPassport'])) {
					$msg = "An account with selected IC/Passport (<strong>${inputs['ICPassport']}</strong>) is already registered. 
						    Kindly enter a new one";
					throw new Exception($msg);
				}
			// redirect if the user type is invalid (the error will be caught and user will be redirected)
			} else {
				throw new Exception('Invalid user type, please try again');
			}

			// check for unique fields
			if (!$patient_queries->isUniqueUsername($inputs['username'])) {
				$msg = "An account with selected username (<strong>${inputs['username']}</strong>) is already registered. 
						Kindly enter a new one";
				throw new Exception($msg);
			}

			// check for unique fields
			if (!$patient_queries->isUniqueEmail($inputs['emailAddress'])) {
				$msg = "An account with selected email address (<strong>${inputs['emailAddress']}</strong>) is already registered. 
						Kindly enter a new one";
				throw new Exception($msg);
			}

			// insert the data into the database
			$isAdded = $patient_queries->register(
				$inputs['username'],
				$inputs['password'],
				$inputs['emailAddress'],
				$inputs['fullName'],
				$special_field,
				$centreName,
				$userType
			);

			// if the query is successful $isAdded will be true
			if ($isAdded) {
				// destroy the userType cookie
				setcookie('userType', null, -1, '/');
				// destroy prev form data cookie
				setcookie('registrationFormData', null, -1, '/');
				// redirect the user with a confirmation flash
				create_flash_message(
					'registration result',
					"<strong class='text-uppercase'>$userType</strong> account was successfully created for 
							<strong class='text-uppercase'>{$inputs['fullName']}</strong>",
					FLASH::SUCCESS
				);
				header("Location: $login_form_url");
				return;
			} else {
				throw new Exception('registration failed');
			}

		} catch (Exception $e) {
			// catch any error and redirect the user to registration form
			$final_redirect_url = $register_form_url . (is_null($redirect_url) ? '' : "?redirectUrl=$redirect_url");
			create_flash_message('registration result', $e->getMessage(), FLASH::ERROR);
			header("Location: $final_redirect_url");
		}
	} else {
		header("Location: $register_form_url");
	}