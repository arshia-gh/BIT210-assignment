<?php
require_once '../includes/table_generator.php';
require_once '../database/administrator_queries.php';
require_once '../includes/app_metadata.inc.php';
require_once '../includes/flash_messages.inc.php';

$current_admin = authenticate();
$healthcare_centre = $admin_queries->find_centre($current_admin['centreName']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="../asset/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../asset/css/style.css" />
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

	<title>List of batches</title>
</head>

<body class="d-flex flex-column min-vh-100">
	<?php
	display_flash_message('AddBatchMessage');
	display_flash_message('login_result');
	display_flash_message('database error');

	$nav_links = [
		'Home' => ['../index.php', false],
		'Dashboard' => ['../admin/index.php', true]
	];
	require_once('../includes/navbar.inc.php');
	?>

	<main class="container flex-grow-1">
		<div class="row">
			<div class="col-12 col-lg-3 bg-light py-3">
				<!--locations links-->
				<?php require_once '../includes/location_breadcrumb.php' ?>

				<!--user info-->
				<?php require_once '../includes/user_info.inc.php' ?>
			</div>

			<div class="col-12 col-lg-9" style="min-height: 50vh">
				<section class="p-4 rounded-3 shadow-sm h-75 bg-filter-darken" style="background-image: url(https://image.freepik.com/free-vector/flat-hand-drawn-hospital-reception-scene_52683-54613.jpg);">
					<h1 id="healthcareCenterName" class="text-white"><?= $healthcare_centre['centreName'] ?></h1>
					<h5 id="healthcareCenterAddress" class="text-white fw-light fst-italic"><?= $healthcare_centre['address'] ?></h5>

					<article class="container bg-white rounded shadow text-dark py-3 my-5">
						<h3>Batches List</h3>
						<div class="row">
							<div class=" col-12 col-lg-9">
								<p class="text-muted">Select a batch to view </p>
							</div>
							<div class="col-12 col-lg-3 mb-2 mb-lg-0">
								<a href="add-batch.php" class="btn btn-primary btn-sm w-100">
									<span class="fa fa-plus"></span> Add Batch
								</a>
							</div>
						</div>
						<div id="tableContainer">
							<?php
							$table_headers = ['Batch Number', 'Vaccine Name', 'No of Pending Appointment'];
							$batches = $admin_queries->find_batches_of_centre($current_admin['centreName']);
							
							//map the batches into the intended format to show
							$batches = array_map(
								fn ($batch) => [
									'batchNo' => $batch['batchNo'],
									'vaccineName' => $batch['vaccineName'],
									'quantityPending' => $batch['quantityPending']
								],
								$batches
							);

							generate_table($batches, 'batchNo', $table_headers);
							?>
						</div>
					</article>
				</section>
			</div>
	</main>

	<!--footer-->
	<?php require_once('../includes/footer.inc.php'); ?>

	<script type="text/javascript" src="../asset/js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript" src="index.js"></script>
</body>

</html>