<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'database/patient_queries.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'includes/alert_messages.inc.php';
	include_once 'patient/partials.php';

	// retrieve the selected vaccine (null if no vaccine is selected)
	$selected_vaccine = $_COOKIE['vaccineID'] ?? NULL;
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
		!is_null($current_patient) && display_flash_message("${current_patient['username']} login result");
		display_flash_message('vaccine not selected');
	?>

	<!-- navbar -->
	<?php include 'includes/navbar.inc.php' ?>

	<main class="container-md flex-grow-1">
		<div class="row mb-4">
			<aside class="col-12 col-lg-3 p-3 bg-light rounded">
				<aside class="border rounded p-3 bg-white">
					<?php display_progress_bar(5); ?>
				</aside>

				<!-- user_info -->
				<?php include 'includes/user_info.inc.php'; ?>

			</aside>
			<div class="col-12 col-lg-9 min-vh-50 mt-lg-0 mt-2">
				<section class="p-4 rounded-3 shadow-sm h-75 background-1 bg-filter-darken">
					<h1 class="h2 text-white">Request Vaccination</h1>
					<article class="rounded shadow bg-white p-4 mt-5">

						<?php
							// query the database for available vaccines
							// - query contains an available field indicating whether the vaccine is available
							// for more details please refer to the database/patient-queries.php
							$vaccines_result = $patient_queries->get_available_vaccines();

							if (!$vaccines_result instanceof Exception):
							if (count($vaccines_result) > 0):
							display_alert('Please select a vaccine to continue', ALERT::INFO, ALERT::INFO_ICON);
						?>

						<form action="./select-healthcare-centre.php" id="documentForm">
							<?php
								echo '<ul class="list-group">';

								foreach ($vaccines_result as $vaccine) {
									$is_available = $vaccine['available'];
									$is_selected = !is_null($selected_vaccine)
									&& $selected_vaccine === $vaccine['vaccineID'] ? 'checked' : '';

									echo <<<LI
											<li class="list-group-item list-group-item-action d-flex align-items-start {$str_eval(!$is_available ? 'disabled bg-light' : '')}">
												<label class="form-check-label me-3">
														<input class="form-check-input" name="vaccineID" type="radio"
															$is_selected value="{$vaccine['vaccineID']}" {$str_eval(!$is_available ? 'disabled' : '')} />
												</label>
												<div class="me-auto">
													<p class="fw-bold m-0">{$vaccine['vaccineName']}</p>
													<p class="m-0">manufactured by <strong>{$vaccine['manufacturer']}</strong></p>
												</div>
												<span class="badge bg-{$str_eval($is_available ? 'primary' : 'secondary')} rounded-pill">
													{$str_eval($is_available ? 'available' : 'unavailable')}
												</span>
											</li>
										LI;
								}
								echo '</ul>';
								else:
									display_alert(
												'No vaccine was found, please consider contacting an administrator',
												ALERT::WARNING,
												ALERT::WARNING_ICON
									);
								endif;
								display_controls(
											'./index.php',
											'View your vaccination(s)',
											is_null($selected_vaccine)
								);
								else:
									display_fatal_error($vaccines_result->getCode());
								endif;
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
