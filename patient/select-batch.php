<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'database/patient_queries.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'includes/alert_messages.inc.php';
	include_once 'patient/partials.php';

	// retrieve required information from previous selections
	try {
		$selected_vaccine = save_or_get_pk_from_cookie('vaccineID', 'vaccine', [$patient_queries, 'get_vaccine']);
		$selected_hc = save_or_get_pk_from_cookie('centreName', 'healthcare centre',
					[$patient_queries, 'get_healthcare_centre'], $selected_vaccine
		);
	} catch (Exception $e) {
		redirect_with_database_error($e->getCode());
	}

	// get the current selected batchNo through query string
	$selected_batch = $_COOKIE['batchNo'] ?? NULL;
	// get the current patient
	$current_patient = authenticate(FALSE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?= APP_NAME ?> - Request vaccination</title>

	<!-- stylesheets -->
	<link rel="stylesheet" href="../asset/css/bootstrap.min.css" />
	<link
				rel="stylesheet"
				href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"
				integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA=="
				crossorigin="anonymous"
				referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="../asset/css/style.css" />
</head>
<body class="d-flex flex-column min-vh-100">
	<!-- display login flash -->
	<?php
		// display flash message of user login results
		!is_null($current_patient) && display_flash_message("${current_patient['username']} login result");
		display_flash_message('batch not selected');
	?>

	<!-- navbar -->
	<?php include 'includes/navbar.inc.php' ?>

	<main class="container-md flex-grow-1">
		<div class="row mb-4">
			<aside class="col-12 col-lg-3 p-3 bg-light rounded">
				<!-- progress bar -->
				<aside class="border rounded p-3 bg-white">
					<?php display_progress_bar(50); ?>
				</aside>

				<!-- user_info -->
				<?php include 'includes/user_info.inc.php'; ?>

			</aside>
			<div class="col-12 col-lg-9 min-vh-50 mt-lg-0 mt-2">
				<section class="p-4 rounded-3 shadow-sm h-75 background-1 bg-filter-darken">
					<h1 class="h2 text-white">Request Vaccination</h1>
					<article class="rounded shadow bg-white p-4 mt-5">

						<?php
							if (is_null($current_patient)) {
							display_alert('Please login before selecting a batch', ALERT::WARNING, ALERT::WARNING_ICON);
						} else {
							display_alert('Please select a batch to continue', ALERT::INFO, ALERT::INFO_ICON);
						}
						?>

						<form action="./select-date.php" id="documentForm">
							<?php
								// query the database for available batches in respect to selected HC and vaccine
								$batch_result = $patient_queries->get_available_batches($selected_hc, $selected_vaccine);
								if (!$batch_result instanceof Exception) {
									echo '<ul class="list-group">';

									// loop through the result of the query
									foreach ($batch_result as $batch) {
										$formatted_date = date_format(date_create($batch['expiryDate']), 'jS F Y');

										$is_selected = !is_null($selected_batch)
										&& $selected_batch === $batch['batchNo'] ? 'checked' : '';
										// if the user is not authenticated print only the batchNo without any inputs
										if (is_null($current_patient)) {
											echo <<<LI
											<li class="list-group-item list-group-item-action">
												<div class="me-auto">
													<p class="fw-bold m-0">{$batch['batchNo']}</p>
												</div>
											</li>
											LI;
										// otherwise, print all the required information and include the input
										} else {
											echo <<<LI
												<li class="list-group-item list-group-item-action d-flex align-items-start">
													<label class="form-check-label me-3">
															<input class="form-check-input" name="batchNo" type="radio"
																$is_selected value="{$batch['batchNo']}" />
													</label>
													<div class="me-auto">
														<p class="fw-bold m-0">{$batch['batchNo']}</p>
														<p class="m-0">
															expires at
															<strong>$formatted_date</strong>
														</p>
													</div>
													<span class="badge bg-primary rounded-pill">{$batch['quantityAvailable']} vial available</span>
												</li>
											LI;
										}
									}
									echo '</ul>';
									$disabled = is_null($current_patient) || is_null($selected_batch) ? 'disabled' : '';
									?>

									 <p class="m-0 mt-3 d-flex justify-content-between p-2 bg-light border border-1 rounded" style="border-color: var(--bs-gray-200)">
										 <a class="btn btn-warning" href="./select-healthcare-centre.php" data-bs-toggle="tooltip"
										    data-bs-placement="bottom"
										    title="Select a different healthcare centre">
											 <i class="fa-solid fa-angle-left"></i>
											 Back
										 </a>

										 <button class="ms-auto btn btn-primary" type="submit" id="submitBtn" <?= $disabled ?>>
											 Confirm
											 <i class="fa-solid fa-angle-right"></i>
										 </button>
									 </p>
								<?php
								// if query failed due to exception, display a fatal error
								} else {
									display_fatal_error($batch_result->getCode());
								}
							?>
						</form>
					</article>
				</section>
			</div>
		</div>
	</main>

	<!-- footer -->
	<?php include 'includes/footer.inc.php' ?>

	<!-- scripts -->
	<script src="../asset/js/bootstrap.bundle.min.js"></script>
	<script src="./patient.js"></script>
</body>
</html>
