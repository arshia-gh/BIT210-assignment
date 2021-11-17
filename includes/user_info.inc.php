<?php
	$found_user = $_SESSION['current_user'] ?? NULL;
?>
	<aside class="border rounded bg-white p-3 mt-3">
		<header class="d-flex justify-content-between align-items-end">
			<h6 class="text-muted">User Information</h6>
			<?php if (!is_null($found_user)): ?>
				<a class="btn btn-warning btn-sm" href="../users/logout.php">logout</a>
			<?php else: ?>
				<a class="btn btn-primary btn-sm" href="../users/login-form.php">login</a>
			<?php endif; ?>
		</header>
		<figure class="row mt-3 justify-content-center align-items-center">
			<div class="col-lg-6 col-3">
				<img id="user-avatar" class="img-fluid" src="../asset/img/male_man_people_person_avatar_white_tone_icon.png"
				     alt="user avatar" />
			</div>
			<footer class="col-9 col-lg-12 mt-3">
				<ul class="list-group list-group-flush text-break text-center">
					<?php
						if (!is_null($found_user)) {

							$special_info = $found_user['userType'] === 'administrator' ? 'staffID' : 'ICPassport';
							$columns_to_show = ['fullName', $special_info, 'email'];

							foreach ($columns_to_show as $column) {
								echo "<li class=\"list-group-item\">$found_user[$column]</li>";
							}
						} else echo "<li class=\"list-group-item\">Not logged in</li>";
					?>
				</ul>
			</footer>
		</figure>
	</aside>
<?php unset($found_user) ?>