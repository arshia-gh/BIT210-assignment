<?php
	include_once('../includes/app_metadata.inc.php');
	include_once('../includes/flash_messages.inc.php');
	include_once('../database/patient_queries.php');

	// get the healthcare centres for datalist used in form
	$healthcare_centres = [];
	try {
		$healthcare_centres = $patient_queries->get_all_healthcare_centres();
	} catch (Exception $e) {
		create_flash_message('RegisterFormQueryFailed', $e->getMessage(), FLASH::ERROR);
	}

	// get the previous form submission
	$prev_form_data = null;
	// convert the json cookie to assoc array
	if (isset($_COOKIE['registrationFormData'])) {
		$prev_form_data = json_decode($_COOKIE['registrationFormData'], true);
	}

	// delete the cookie (in case user cancels the use case)
	setcookie('registrationFormData', null, -1, '/');

	// get the redirect url
	$redirect_url = $_GET['redirectUrl'] ?? NULL;
	// format the redirect url to a query string
	$formatted_redirect_url = !is_null($redirect_url) ? 'redirectUrl=' . $_GET['redirectUrl'] : '';

	// safely retrieve existing keys from $prev_form_data
	// returns an empty string if the key doesn't exist
	function safe_get($key) {
		global $prev_form_data;
		return $prev_form_data[$key] ?? '';
	}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?= APP_NAME ?> - Registration</title>
	<link rel="stylesheet" href="../asset/css/bootstrap.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"
	      integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA=="
	      crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="../asset/css/style.css">
</head>
<body class="min-vh-100 d-flex flex-column justify-content-center background-pattern">
	<?php
		display_flash_message('registration result');
	?>
	<!-- navbar -->
	<header class="container">
		<div class="row justify-content-center">
			<?php
				$nav_links = [
							'Home' => ['index.php', FALSE],
							'Request Vaccination' => ['patient/index.php', FALSE]
				];
				$nav_class = 'col-lg-9 col-12 bg-white rounded shadow-lg bg-opacity-97';
				include 'includes/navbar.inc.php'
			?>
		</div>
	</header>
	<!-- main content -->
	<main class="container">
		<div class="row justify-content-center my-5">
			<section class="col-lg-9 col-12 p-5 rounded shadow-lg bg-opacity-97 bg-white">
				<h1 class="display-6 text-center text-uppercase">Registration Form</h1>
				<hr />
				<div class="container-fluid">
					<form id="registrationForm" class="container-fluid needs-validation" novalidate method="post"
					      action="register.php?<?= $formatted_redirect_url ?>">
						<!-- user type -->
						<fieldset class="row mb-3" role="group" aria-label="user registration type">
							<p class="btn-group col p-0">
								<input type="radio" class="btn-check" name="userType" value="administrator" id="administratorUserType"
								/>
								<label class="btn btn-outline-primary w-50" for="administratorUserType">Administrator</label>

								<input type="radio" class="btn-check" name="userType" value="patient" id="patientUserType" />
								<label class="btn btn-outline-primary w-50" for="patientUserType">
									Patient
								</label>
							</p>
						</fieldset>
						<!-- admin specific input (Healthcare centre) -->
						<fieldset class="row mb-3 border rounded-3 p-3" id="healthcareFieldset">
							<legend class="fs-4 fw-light">Healthcare Center Information</legend>
							<p class="form-floating mb-3 col-12 col-lg-6">
								<input type="text" class="form-control" id="healthcareNameInput" name="healthcareName"
								       placeholder="Healthcare Center" list="healthcareDatalist" value="<?= safe_get('healthcareName') ?>"/>
								<label for="healthcareNameInput" class="ps-4">Healthcare Center Name</label>
							</p>
							<p class="form-floating mb-3 col-12 col-lg-6">
								<input type="text" class="form-control" id="healthcareAddressInput" name="healthcareAddress"
								       placeholder="Healthcare Center" value="<?= safe_get('healthcareAddress') ?>"/>
								<label for="healthcareAddressInput" class="ps-4">Healthcare Center Address</label>
							</p>
							<span class="form-text">
								* If healthcare center doesn't exist in our database, it will be added
								automatically
							</span>
							<datalist id="healthcareDatalist">
								<?php foreach ($healthcare_centres as $centre): ?>
									<option value="<?= $centre['centreName'] ?>"><?= $centre['address'] ?></option>
								<?php endforeach; ?>
							</datalist>
						</fieldset>

						<!-- shared inputs (Account related) -->
						<fieldset class="row mb-3 border rounded-3 p-3">
							<legend class="fs-4 fw-light">User information</legend>
							<p class="form-floating mb-3 col-12 col-lg-6">
								<input type="text" class="form-control" id="usernameInput" placeholder="username" name="username"
								       value="<?= safe_get('username') ?>" />
								<label for="usernameInput" class="ps-4">Username</label>
								<span class="form-text">
									* username must be unique <br />
									* username can only contain letters and underscores <br />
									* username must be between 3 and 25 character long
								</span>
							</p>
							<p class="form-floating mb-3 col-12 col-lg-6">
								<input type="password" class="form-control" id="passwordInput" name="password" placeholder="password"
								       value="<?= safe_get('password') ?>" />
								<label for="passwordInput" class="ps-4">Password</label>
								<span class="form-text">
									* password must contain at least one uppercase letter <br />
									* password must be at least 12 character long
								</span>
							</p>
						</fieldset>

						<!-- Personal inputs (ICPassport, staffID, email, fullName) -->
						<fieldset class="row mb-3 border rounded-3 p-3">
							<legend class="fs-4 fw-light">Personal Information</legend>
							<p class="form-floating mb-3 col-12 col-lg-6">
								<input type="text" class="form-control" id="fullNameInput" name="fullName"
								       placeholder="Michael Jackson" value="<?= safe_get('fullName') ?>"  />
								<label for="fullNameInput" class="ps-4">Name</label>
							</p>
							<p class="form-floating mb-3 col-12 col-lg-6" id="ICPassport_form_control">
								<input type="text" class="form-control" id="ICPassportInput" name="ICPassport" placeholder="H9609867"
								       value="<?= safe_get('ICPassport') ?>" />
								<label for="ICPassportInput" class="ps-4">IC / Passport</label>
							</p>
							<p class="form-floating mb-3 col-12 col-lg-6" id="staff_id_form_control">
								<input type="text" class="form-control" id="staffIDInput" placeholder="ST2000" name="staffID"
								       value="<?= safe_get('staffID') ?>"
								/>
								<label for="staffIDInput" class="ps-4">Staff ID</label>
							</p>
							<p class="form-floating mb-3 col-12">
								<input type="email" class="form-control" id="emailInput" name="emailAddress"
								       placeholder="name@example.com" value="<?= safe_get('emailAddress') ?>"
								/>
								<label for="emailInput" class="ps-4">Email address</label>
							</p>
						</fieldset>

						<!-- controls -->
						<p class="row rounded border mb-3 p-3 gap-2">
							<?php if (!is_null($redirect_url)): ?>
								<a class="btn btn-warning col-md col-12" href="<?= PROJECT_URL . $redirect_url ?>">
									<i class="fas fa-angle-left"></i>
									Back
								</a>
							<?php endif ?>
							<button type="submit" class="btn btn-primary col-md col-12">
								Register
							</button>
						</p>
					</form>
					<span>
					Already registered?
					<a class="link-light badge bg-primary text-decoration-none"
					   href="login-form.php?<?= $formatted_redirect_url ?>">
						login
					</a>
				</span>
				</div>
			</section>
		</div>
	</main>
	<script src="../asset/js/bootstrap.bundle.min.js"></script>
	<script type="module" src="register-form.js"></script>
</body>
</html>