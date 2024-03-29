<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'database/patient_queries.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'patient/partials.php';

	function redirect_to(string $label)
	{
		redirect_with_selection_error($label, label_to_script_name($label));
	}

	/**
	 * redirects the user if the passed value is null
	 * @param mixed $value
	 * @param       $label
	 */
	function redirect_if_null(mixed $value, $label)
	{
		if (!is_null($value)) return;
		redirect_to($label);
	}

	try {
		$selected_vaccine = NULL;
		$selected_hc = NULL;
		$selected_batch = NULL;

		// validate the existence of the vaccine
		if (isset($_POST['vaccineID']) || isset($_COOKIE['vaccineID'])) {
			$req_vaccine_id = htmlspecialchars($_POST['vaccineID'] ?? $_COOKIE['vaccineID']);
			$selected_vaccine = $patient_queries->get_vaccine($req_vaccine_id);
		}
		// otherwise, redirect the user to select-vaccine.php
		redirect_if_null($selected_vaccine, 'vaccine');

		// validate the existence of the healthcare centre
		if (isset($_POST['centreName']) || isset($_COOKIE['centreName'])) {
			$req_centre_name = htmlspecialchars($_POST['centreName'] ?? $_COOKIE['centreName']);
			$selected_hc = $patient_queries->get_healthcare_centre($selected_vaccine['vaccineID'], $req_centre_name);
		}
		// otherwise, redirect the user to select-healthcare-centre.php
		redirect_if_null($selected_hc, 'healthcare centre');


		// validate the existence of the batch
		if (isset($_POST['batchNo']) || isset($_COOKIE['batchNo'])) {
			$req_batch_no = htmlspecialchars($_POST['batchNo'] ?? $_COOKIE['batchNo']);
			$selected_batch = $patient_queries->get_batch(
				$selected_vaccine['vaccineID'],
				$selected_hc['centreName'],
				$req_batch_no
			);
		}
		// otherwise, redirect the user to select-batch.php
		redirect_if_null($selected_batch, 'batch');

		$current_user = authenticate();

		if (isset($_POST['appointment_date'])) {
			$appointment_date = htmlspecialchars($_POST['appointment_date']);

			// check if it is a valid date
			if ($converted_date = strtotime($appointment_date)) {
				// create a new vaccination
				$insert_result = $patient_queries->save_vaccination(
					date('Y-m-d', $converted_date),
					$current_user['username'],
					$selected_batch['batchNo']
				);

				flash('vaccination created',
					"Vaccination appointment was successfully booked for <strong class=\"text-uppercase\">{$current_user['fullName']}</strong>",
					FLASH::SUCCESS
				);
				header('Location: ' . PROJECT_URL . 'patient/index.php');
				return;
			}
		}

		redirect_with_selection_error('appointment date', 'select-date.php');
	} catch (Exception $e) {
		redirect_with_database_error($e->getCode());
	}
