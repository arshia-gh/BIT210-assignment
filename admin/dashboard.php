<?php 
    require_once '../includes/table_generator.php';
    require_once '../database/administrator_queries.php';
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="../asset/css/bootstrap.min.css"/>
		<link
			rel="stylesheet"
			href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
			integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p"
			crossorigin="anonymous"
		/>

		<title>List of batches</title>
	</head>

	<body class="d-flex flex-column min-vh-100">
		<nav class="navbar navbar-expand-md navbar-light container-md">
			<a class="navbar-brand d-flex align-items-center" href="/index.html">
				<img src="../asset/svg/logo.svg" alt="navbar logo" class="me-1" />
				<span class="fw-bold">PCVS</span
				><span class="align-self-stretch border-end mx-1"></span
				><span class="fs-6 fw-light text-secondary"
					>Private Covid-19 Vaccination Service</span
				>
			</a>
			<button
				class="navbar-toggler"
				type="button"
				data-bs-toggle="collapse"
				data-bs-target="#main-navbar"
				aria-controls="main-navbar"
				aria-expanded="false"
				aria-label="Toggle main navigation"
			>
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse justify-content-end" id="main-navbar">
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" href="../index.html">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link active" href="./administrator.html" aria-current="page"
							>Dashboard</a
						>
					</li>
				</ul>
			</div>
		</nav>

		<main class="container flex-grow-1">
			<div class="row">
				<div class="col-12 col-lg-3 bg-light py-3">
					<aside class="border rounded p-3 pb-1 mt-3 bg-white">
						<h6 class="text-muted">Location</h6>
						<nav style="--bs-breadcrumb-divider: '➤'" aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item active">Select Batches</li>
							</ol>
						</nav>
					</aside>

				  <!--user info-->
				<aside class="border rounded bg-white p-3 mt-3">
					<header class="d-flex justify-content-between align-items-end">
					<h6 class="text-muted">User Information</h6>
					<div id="logout" class="d-none">
						<button id="logoutBtn" class="btn btn-warning btn-sm">logout</button>
					</div>
					</header>
					<figure class="row mt-3 justify-content-center align-items-center">
					<div class="col-lg-6 col-3">
						<img id="user-avatar" class="img-fluid"
						src="../asset/img/male_man_people_person_avatar_white_tone_icon_159363.png"/>
					</div>
					<footer class="col-9 col-lg-12 mt-3">
						<ul id="userInfo" class="list-group list-group-flush text-break text-center">
						<li class="list-group-item">Not logged in</li>
						</ul>
					</footer>
					</figure>
				</aside>
			</div>

			<div class="col-12 col-lg-9" style="min-height: 50vh">
				<section
					class="p-4 rounded-3 shadow-sm h-75 bg-filter-darken"
					style="background-image: url(https://image.freepik.com/free-vector/flat-hand-drawn-hospital-reception-scene_52683-54613.jpg);">
					<h1 id="healthcareCenterName" class="text-white">Healthcare Centre name</h1>
					<h5 id="healthcareCenterAddress" class="text-white fw-light fst-italic"
						>Healthcare Centre address</h5
					>

					<article class="container bg-white rounded shadow text-dark py-3 my-5">
						<h3>Batches List</h3>
						<p class="text-muted"
							>Select a batch to view
							<button
								type="button"
								class="btn btn-primary btn-sm float-end"
								data-bs-toggle="modal"
								data-bs-target="#addBatchModal"
							>
								<span class="fa fa-plus"></span> Batch
							</button>
							<span
								id="batchAddedBadge"
								class="float-end badge bg-success mx-3 p-2"
							></span>
						</p>
						<div id="tableContainer">
							<?php
							$table_headers = ['Batch Number', 'Expiry Date', 'No of Pending Appointment'];
							$batches = $admin_queries->find_batches_of_centre('Century Medical Centre');

							function onBatchSelected($batchNo) {
								header("Location: /batch/$batchNo");
							}
							$batches = array_map(fn($batch) => [
							'batchNo' => $batch['batchNo'], 
							'expiryDate' => $batch['expiryDate'], 
							'quantityPending' => $batch['quantityPending']], 
							$batches);

							GenerateTable($batches, 'batchNo', $table_headers);
							?>
						</div>
					</article>
				</section>
			</div>
		</main>

		<footer class="bg-dark">
			<section class="container-md py-2 text-white text-center">
				<h2 class="h2 m-0">PCVS</h2>
				<p class="fs-4 fw-light m-0">Private Covid-19 Vaccination Service </p>
				<hr />
				<small class="text-muted">PCVS - copyright&copy; 2021</small>
			</section>
		</footer>


		<!--Modal-->
		<div
			class="modal fade"
			id="addBatchModal"
			tabindex="-1"
			aria-labelledby="addBatchLabel"
			aria-hidden="true"
		>
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="row">
						<div class="col-sm ps-5 d-none d-lg-block">
							<div
								id="carouselCovid"
								class="carousel slide h-100 d-flex align-items-center"
								data-bs-ride="carousel"
							>
								<div class="carousel-inner">
									<div class="carousel-item active">
										<img
											src="https://image.freepik.com/free-vector/organic-flat-vaccination-campaign-illustration_23-2148955324.jpg"
											class="d-block w-100"
											alt="image of encouraging youth vaccination"
										/>
									</div>
									<div class="carousel-item">
										<img
											src="https://image.freepik.com/free-vector/flat-hand-drawn-doctor-injecting-vaccine-patient_23-2148872143.jpg"
											class="d-block w-100"
											alt="image of encouraging adult vaccination"
										/>
									</div>
									<div class="carousel-item">
										<img
											src="https://image.freepik.com/free-vector/vaccine-concept-illustration_114360-5376.jpg"
											class="d-block w-100"
											alt="image of encouraging old forks vaccination"
										/>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm">
							<div class="modal-header">
								<h5 class="modal-title" id="addBatchLabel">Add Batch</h5>
								<button
									type="button"
									class="btn-close"
									data-bs-dismiss="modal"
									aria-label="Close"
								></button>
							</div>
							<div class="modal-body p-4">
								<form id="addBatchForm">
									<select
										class="form-select"
										id="vaccineSelect"
										aria-label="Vaccine ID"
										required
										name="vaccineID"
									>
										<option selected hidden value="">Select a vaccine</option>
                                        <?php 
                                            $vaccines = $admin_queries->get_all_vaccines();
                                            foreach($vaccines as $vaccine) {
                                                echo "<option value=\"{$vaccine['vaccineID']}\" data-manufacturer=\"{$vaccine['manufacturer']}\">{$vaccine['vaccineName']}</option>";
                                            }
                                        ?>
									</select>

									<input
										readonly
										class="form-control mt-3"
										id="manufacturerInput"
										placeholder="Manufacturer"
									/>

									<hr />

									<p class="text-muted text-center">Enter Batch Details</p>

									<div class="form-floating my-3">
										<input
											required
											type="text"
											class="form-control"
											id="batchNoInput"
											placeholder="Batch Number"
											name="batchNo"
										/>
										<label for="floatingInput">Batch Number</label>
									</div>
									<div class="alert alert-danger d-none" id="duplicatedBatchAlert"></div>

									<div class="form-floating my-3">
										<input
											required
											type="number"
											min="1"
											class="form-control"
											id="quantityInput"
											placeholder="Quantity Available"
											name="quantityAvailable"
										/>
										<label for="floatingInput">Quantity Available</label>
									</div>

									<div class="form-floating mb-3">
										<input
											required
											type="date"
											class="form-control"
											id="expiryDateInput"
											placeholder="Expiry Date"
											name="expiryDate"
										/>
										<label for="floatingInput">Expiry Date</label>
									</div>

									<div class="d-flex justify-content-center">
										<button type="submit" class="btn btn-primary w-100" id="submitButton"
											>Add</button
										>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

    <script>
		const vaccineSelect = document.getElementById('vaccineSelect');

		vaccineSelect.onchange = (e) => {
			const option = e.target.selectedOptions[0];
			const manufacturer = option.getAttribute('data-manufacturer');
			document.getElementById('manufacturerInput').value = manufacturer;
		};
		
		const tableContainer = document.getElementById('tableContainer');
        const table = tableContainer.querySelector("table");
		table.addEventListener('click', (e) => {
        const tr = e.target.parentNode;
		const batchNo = tr.getAttribute('data-row-id');
		if (batchNo) window.location = '../batch/' + batchNo;
		})
	</script>

    <script src="../asset/js/bootstrap.bundle.min.js"></script>
	</body>
</html>
