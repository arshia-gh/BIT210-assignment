<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'database/patient_queries.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'patient/partials.php';

	$current_user = authenticate();
	try {
		$selected_vaccine = get_selection('vaccineID',
			'vaccine',
			'select-vaccine',
			[$patient_queries, 'get_vaccine']);

		$selected_hc = get_selection('centreName',
			'healthcare centre',
			'select-healthcare-centre',
			[$patient_queries, 'get_healthcare_centre'], $selected_vaccine);

		$selected_batch = get_selection('batchNo',
			'batch',
			'select-batch',
			[$patient_queries, 'get_batch'], $selected_vaccine, $selected_hc);

		if (isset($_POST['appointment_date'])) {
			$appointment_date = htmlspecialchars($_POST['appointment_date']);
			if ($converted_date = strtotime($appointment_date)) {
				$insert_result = $patient_queries->save_vaccination(
					mt_rand(10_000_000, 99_999_999),
					date('Y-m-d', $converted_date),
					$current_user['username'], $selected_batch);
				flash('vaccination created',
					"Vaccination appointment was successfully booked for <strong class='text-uppercase'>{$current_user['fullName']}</strong>",
					FLASH::SUCCESS
				);
				header('Location: ' . PROJECT_URL . 'patient/index.php');
				return;

			}
		}
		flash('date not selected',
			"Please select a valid <strong class='text-uppercase'>appointment date</strong> before proceeding",
			FLASH::ERROR
		);
		header('Location: ' . PROJECT_URL . 'patient/select-date.php');
	} catch (Exception $e) {
		$error_code = $e->getCode();
		flash('database error',
			"An error occurred, <strong class='text-uppercase'>ERR_${$error_code}</strong>",
			FLASH::ERROR
		);
		header('Location: ' . PROJECT_URL . 'patient/index.php');
	}