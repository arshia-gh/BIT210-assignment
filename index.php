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
				<div class="flex-column d-flex">
					<div id="carouselCovid" class="carousel slide h-100 mx-3 d-flex align-items-center" data-bs-ride="carousel"
					     data-bs-interval="2000">
						<div class="carousel-inner">
							<div class="carousel-item active">
								<img src="https://image.freepik.com/free-vector/organic-flat-vaccination-campaign-illustration_23-2148955324.jpg"
								     class="d-block w-100" alt="image of encouraging youth vaccination" />
							</div>
							<div class="carousel-item">
								<img src="https://image.freepik.com/free-vector/flat-hand-drawn-doctor-injecting-vaccine-patient_23-2148872143.jpg"
								     class="d-block w-100" alt="image of encouraging adult vaccination" />
							</div>
							<div class="carousel-item">
								<img src="https://image.freepik.com/free-vector/vaccine-concept-illustration_114360-5376.jpg"
								     class="d-block w-100" alt="image of encouraging old forks vaccination" />
							</div>
						</div>
					</div>
					<div class="d-flex flex-column m-3 gap-2">
						<a href="users/login-form.php" class="btn btn-primary">Login</a>
						<a href="patient/index.php" class="btn btn-success ">Request Vaccination</a>
					</div>
				</div>
			</section>
		</div>
	</main>
	<script src="asset/js/bootstrap.bundle.min.js"></script>
</html>