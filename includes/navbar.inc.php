<?php
	// default values
	if (!isset($nav_links)) $nav_links = [];
	if (!isset($nav_links['Home'])) $nav_links['Home'] = [PROJECT_URL . 'index.html', true];
?>

<nav class="navbar navbar-expand-md navbar-light container-md">
	<a class="navbar-brand d-flex align-items-center" href="<?=$nav_links['Home'][0]?>">
		<img src="<?=PROJECT_URL . 'asset/svg/logo.svg'?>" alt="navbar logo" class="me-1" />
		<span class="fw-bold"><?=APP_NAME?></span>
		<span class="align-self-stretch border-end mx-1"></span>
		<span class="fs-6 fw-light text-secondary">
			<?=APP_SLOGAN?>
		</span>
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
			<?php foreach($nav_links as $name => $link): ?>
				<li class="nav-item">
					<a class="nav-link <?=$link[1]? "active" : ""?>" href="<?=$link[0]?>"><?=$name?></a>
				</li>
			<?php endforeach;?>
		</ul>
	</div>
</nav>