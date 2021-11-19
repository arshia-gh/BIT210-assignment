<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'database/patient_queries.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'includes/alert_messages.inc.php';
	include_once 'patient/partials.php';
	include_once 'includes/table_generator.php';

	$current_patient = authenticate(FALSE);

	reset_vaccination_details();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?= APP_NAME ?> - Patient dashboard</title>

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
		display_flash_message('vaccination created');
		display_flash_message('database error');
	?>
	<!-- navbar -->
	<?php
		$nav_links = [
					'Home' => ['index.php', false],
		];
		include 'includes/navbar.inc.php'
	?>

	<main class="container-md flex-grow-1">
		<div class="row mb-4">
			<aside class="col-12 col-lg-3 p-3 bg-light rounded">
				<aside class="border rounded p-3 bg-white">
					<p class="text-muted h6">No operation in progress</p>
				</aside>

				<!-- user_info -->
				<?php include 'includes/user_info.inc.php'; ?>

			</aside>
			<div class="col-12 col-lg-9 min-vh-50 mt-lg-0 mt-2">
				<section class="p-4 rounded-3 shadow-sm h-75 background-1 bg-filter-darken">
					<h1 class="h2 text-white">Dashboard</h1>
					<article class="rounded shadow bg-white p-4 mt-5 d-flex flex-column justify-content-center">
						<?php
							if (!is_null($current_patient)) {
								// retrieve current user vaccination appointments
								$vaccinations_result = $patient_queries->get_user_vaccinations($current_patient['username']);

								if (!$vaccinations_result instanceof Exception) {
									// define the table headers
									$table_headers = ['Status', 'Appointment Date', 'Vaccine', 'Remarks'];
									// map the query result value to proper format
									$vaccinations = array_map(fn ($vaccination) => [
												'vaccination_id' => $vaccination['vaccinationID'],
												'status' => sprintf('<span class="badge bg-%s">%s</span>',
															statusToColor($vaccination['status']), strtoupper($vaccination['status'])
												),
												'appointment_date' => date('jS F Y', strtotime($vaccination['appointmentDate'])),
												'vaccine_name' => $vaccination['vaccineName'],
												'remarks' => $vaccination['remarks'] ?? '<span class="small text-secondary">N/A</span>'
									], $vaccinations_result);
									// generate and display the tables
									generate_table($vaccinations, 'vaccination_id', $table_headers, FALSE,);
								} else {
									display_fatal_error($vaccinations_result->getCode());
								}
							} else {
								display_alert("Please login to view your current appointments' status", ALERT::INFO, ALERT::INFO_ICON);
							}
						?>
						<a class="btn btn-primary align-self-lg-start" href="select-vaccine.php">
							<i class="far fa-calendar-plus me-1"></i>
							Request vaccination
						</a>
					</article>
				</section>
			</div>
		</div>
	</main>

	<!-- footer -->
	<?php include 'includes/footer.inc.php' ?>

	<!-- scripts -->
	<script src="../asset/js/bootstrap.bundle.min.js"></script>
</body>
</html>
