<?php
	include_once('./includes/app_metadata.inc.php');
	include_once('./includes/debug_tools.inc.php');
	include_once('./database/patient_queries.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./asset/css/bootstrap.min.css" />
	<title><?= APP_NAME ?> - Landing</title>
	<link rel="stylesheet" href="asset/css/bootstrap.min.css" />
	<link rel="stylesheet" href="asset/css/style.css">
</head>
<body class="min-vh-100 d-flex flex-column justify-content-start background-pattern">
	<header class="container">
		<div class="row justify-content-center">
			<?php
				$nav_links = [
							'Home' => ['index.php', TRUE],
							'Request Vaccination' => ['patient/index.php', FALSE]
				];
				$nav_class = 'col-lg-6 col-12 bg-white rounded shadow-lg bg-opacity-97';
				include 'includes/navbar.inc.php'
			?>
		</div>
	</header>
	<main class="container mt-5">
		<div class="row justify-content-center">
			<section class="col-lg-6 col-12 bg-white p-5 rounded shadow-lg bg-opacity-97">
				<h1 class="display-6 text-center d-flex flex-column">PCVS
					<span class="fs-4 ms-2">Private Covid-19 Vaccination Service</span>
				</h1>
				<hr />
				<div class="flex-column d-flex gap-1">

				</div>
			</section>
		</div>
	</main>
	<script src="asset/js/bootstrap.bundle.min.js"></script>
</html>