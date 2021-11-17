<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'database/patient_queries.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'includes/alert_messages.inc.php';
	include_once 'patient/partials.php';

	$current_patient = authenticate(false);

	$selected_vaccine = get_selection('vaccineID',
				'vaccine',
				'select-vaccine',
				[$patient_queries, 'get_vaccine']);

	// retrieve the selected vaccine and healthcare centre (null if no vaccine is selected)
	$selected_hc = $_COOKIE['centreName'] ?? null;
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
		display_flash_message('login_result');
		display_flash_message('healthcare_centre_not_selected');
	?>

	<!-- navbar -->
	<?php include 'includes/navbar.inc.php' ?>

	<main class="container-md flex-grow-1">
		<div class="row mb-4">
			<aside class="col-12 col-lg-3 p-3 bg-light rounded">
				<aside class="border rounded p-3 bg-white">
					<?php display_progress_bar(25); ?>
				</aside>

				<!-- user_info -->
				<?php include 'includes/user_info.inc.php'; ?>

			</aside>
			<div class="col-12 col-lg-9 min-vh-50">
				<section class="p-4 rounded-3 shadow-sm h-75 background-1 bg-filter-darken">
					<h1 class="h2 text-white">Request Vaccination</h1>
					<article class="rounded shadow bg-white p-4 mt-5">

						<?php display_alert('Please select a healthcare centre to continue', ALERT::INFO, ALERT::INFO_ICON); ?>

						<form action="./select-batch.php" id="documentForm">
							<?php
								// query the database for available vaccines
								// - query contains an available field indicating whether the vaccine is available
								// for more details please refer to the database/patient-queries.php
								$hc_result = $patient_queries->get_available_healthcare_centres($selected_vaccine);

								if (!$hc_result instanceof Exception) {
									echo '<ul class="list-group">';

									foreach ($hc_result as $hc) {
										$is_selected = !is_null($selected_hc)
										&& $selected_hc === $hc['centreName'] ? 'checked' : '';
										echo <<<LI
											<li class="list-group-item list-group-item-action d-flex align-items-start">
												<label class="form-check-label me-3">
														<input class="form-check-input" name="centreName" type="radio"
															$is_selected value="{$hc['centreName']}" />
												</label>
												<div class="me-auto">
													<p class="fw-bold m-0">{$hc['centreName']}</p>
													<p class="m-0">
														<i class="fas fa-map-marker-alt me-1 text-primary"></i>
														{$hc['address']}
													</p>
												</div>
												<span class="badge bg-primary rounded-pill">{$hc['quantityAvailable']} vial available</span>
											</li>
										LI;
									}
									echo '</ul>';
									display_controls(
												'./select-vaccine.php',
												'Choose another vaccine',
												is_null($selected_hc)
									);
								} else {
									display_fatal_error($hc_result->getCode());
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
