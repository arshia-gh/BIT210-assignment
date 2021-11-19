<?php
	include_once '../includes/app_metadata.inc.php';
	include_once 'database/patient_queries.php';
	include_once 'includes/flash_messages.inc.php';
	include_once 'includes/alert_messages.inc.php';
	include_once 'patient/partials.php';

	try {
		$selected_vaccine = save_or_get_pk_from_cookie('vaccineID', 'vaccine', [$patient_queries, 'get_vaccine']);
		$selected_hc = save_or_get_pk_from_cookie('centreName', 'healthcare centre',
					[$patient_queries, 'get_healthcare_centre'], $selected_vaccine
		);
		$selected_batch = save_or_get_pk_from_cookie('batchNo', 'batch',
					[$patient_queries, 'get_batch'], $selected_vaccine, $selected_hc);
	} catch(Exception $e) {
		redirect_with_database_error($e->getCode());
	}

	// get current patient
	$current_patient = authenticate();
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
		display_flash_message('appointment date not selected');
	?>

	<!-- navbar -->
	<?php include 'includes/navbar.inc.php' ?>

	<main class="container-md flex-grow-1">
		<div class="row mb-4">
			<aside class="col-12 col-lg-3 p-3 bg-light rounded">
				<aside class="border rounded p-3 bg-white">
					<?php display_progress_bar(75); ?>
				</aside>

				<!-- user_info -->
				<?php include 'includes/user_info.inc.php'; ?>

			</aside>
			<div class="col-12 col-lg-9 min-vh-50 mt-lg-0 mt-2">
				<section class="p-4 rounded-3 shadow-sm h-75 background-1 bg-filter-darken">
					<h1 class="h2 text-white">Request Vaccination</h1>
					<article class="rounded shadow bg-white p-4 mt-5">

						<?php display_alert('Please select a appointment date to continue', ALERT::INFO, ALERT::INFO_ICON); ?>

						<form method="post" action="./request-vaccination.php" id="documentForm">
							<div class="form-floating">
								<?php $current_date = date('Y-m-d', time()); ?>
								<input type="date" class="form-control" value="<?= $current_date ?>" name="appointment_date"
								       id="appointmentDate" min="<?= $current_date ?>"
								       placeholder="2002-06-20">
								<label for="appointmentDate">Appointment Date</label>
								<input name="vaccineID" value="<?=$selected_vaccine?>" hidden aria-hidden="true">
								<input name="centreName" value="<?=$selected_hc?>" hidden aria-hidden="true">
								<input name="batchNo" value="<?=$selected_batch?>" hidden aria-hidden="true">
							</div>
							<?php
								display_controls(
											'./select-batch.php',
											'Choose another healthcare centre',
											is_null($selected_batch),
											'Submit'
								);
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
