<?php

	/*
	 * login-form redirect logic
	 * - redirectUrl isset -> PROJECT_URL . redirectUrl
	 * - else -> user dashboard
	 */

	include_once '../includes/app_metadata.inc.php';
	include_once '../includes/flash_messages.inc.php';
	
	$redirect_url = $_GET['redirectUrl'] ?? NULL;
	$formatted_redirect_url = !is_null($redirect_url) ? 'redirectUrl=' . $_GET['redirectUrl'] : '';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?= APP_NAME ?> - Login</title>
	<link
				rel="stylesheet"
				href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"
				integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA=="
				crossorigin="anonymous"
				referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="../asset/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../asset/css/style.css">
</head>
<body class="min-vh-100 d-flex flex-column justify-content-start background-pattern">
	<?php
		display_flash_message('login result');
		display_flash_message('registration result');
	?>
	<header class="container">
		<div class="row justify-content-center">
				<?php
					$nav_links = [
								'Home' => ['index.php', false],
								'Request Vaccination' => ['patient/index.php', false]
					];
					$nav_class = 'col-lg-6 col-12 bg-white rounded shadow-lg bg-opacity-97';
					include 'includes/navbar.inc.php'
				?>
		</div>
	</header>
	<main class="container mt-5">
		<div class="row justify-content-center">
			<section class="col-lg-6 col-12 bg-white p-5 rounded shadow-lg bg-opacity-97">
				<h1 class="display-6 text-center text-uppercase">Login Form</h1>
				<hr />
				<div class="container-fluid">
					<form id="loginForm" class="needs-validation" method="post"
					      action="login.php?<?= $formatted_redirect_url ?>" novalidate>
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
								<label for="passwordLoginInput" class="ps-4">Password</label>
							</p>
						</fieldset>
						<p class="row rounded border mb-3 p-3 gap-2">
							<?php if (!is_null($redirect_url)): ?>
								<a class="btn btn-warning col-md col-12" href="<?= PROJECT_URL . $redirect_url ?>">
									<i class="fas fa-angle-left"></i>
									Back
								</a>
							<?php endif ?>
							<button type="submit" class="btn btn-primary col-md col-12">
								Login
							</button>
						</p>
					</form>
				</div>
				<span>
					Don't have an account?
					<a class="link-light badge bg-primary text-decoration-none"
					   href="register-form.php?<?= $formatted_redirect_url ?>">
						register
					</a>
				</span>
			</section>
		</div>
	</main>
	<script src="../asset/js/bootstrap.bundle.min.js"></script>
	<script type="module" src="login-form.js"></script>
</body>
</html>
