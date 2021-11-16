<?php
	include_once '../includes/app_metadata.inc.php';
	include_once '../includes/flash_messages.inc.php';
	parse_str($_SERVER['QUERY_STRING'], $query_string);
	$success_url = first_valid_redirect_url('successUrl');
	$success_url = !is_null($success_url) ? 'successUrl' . PROJECT_URL . $success_url : '';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?= APP_NAME ?> - Login</title>
	<link rel="stylesheet" href="../asset/css/bootstrap.min.css" />
</head>
<body class="min-vh-100 d-flex flex-column justify-content-center">
	<?php display_flash_message('login_result'); ?>
	<main class="container">
		<div class="row justify-content-center">
			<section class="col-lg-6 col-12">
				<h1 class="display-6 text-center text-uppercase">Login Form</h1>
				<hr />
				<form id="loginForm" class="needs-validation" method="post"
				      action="login.php?<?= $success_url ?>" novalidate>
					<fieldset class="row mb-3 border rounded-3 p-3">
						<legend>Account Information</legend>
						<p class="form-floating mb-3 col-12">
							<input
								  type="text"
								  class="form-control"
								  id="usernameLoginInput"
								  placeholder="username"
								  name="username"
								  required
							/>
							<span class="invalid-feedback"> Username is required </span>
							<label for="usernameLoginInput" class="ps-4">Username</label>
						</p>
						<p class="form-floating mb-3 col-12">
							<input
								  type="password"
								  class="form-control"
								  id="passwordLoginInput"
								  placeholder="password"
								  name="password"
								  required
							/>
							<span class="invalid-feedback"> Password is required </span>
							<label for="passwordLoginInput" class="ps-4">Password</label>
						</p>
					</fieldset>
					<p class="row mb-3 border rounded-3 p-3">
						<button type="submit" class="btn btn-primary" form="loginForm">Login</button>
					</p>
				</form>
				<span>
					Don't have an account?
					<a class="text-start"
					   href="register-form.php">
						register
					</a>
				</span>
			</section>
		</div>
	</main>
	<script src="../asset/js/bootstrap.bundle.min.js"></script>
</body>
</html>
