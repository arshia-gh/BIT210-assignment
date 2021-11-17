<?php
	function reset_vaccination_details() {
		setcookie('vaccineID', null, -1, '/');
		setcookie('centreName', null, -1, '/');
		setcookie('batchNo', null, -1, '/');
	}

	function get_formatted_column_names(PDOStatement $result) : array
	{
		$columns = [];
		for ($i = 0; $i < $result->columnCount(); $i++) {
			array_push(
				$columns,
				ucwords(implode(' ', preg_split('/(?=[A-Z])/', $result->getColumnMeta($i)['name'])))
			);
		}
		return $columns;
	}

	function display_progress_bar(int $now) {
		echo <<<PROG
		<p class="text-muted h6">Request Vaccination</p>
		<div class="progress">
			<div
				class="progress-bar progress-bar-striped progress-bar-animated"
				id="progressBar"
				role="progressbar"
				style="width: $now%"
				aria-valuenow="$now"
				aria-valuemin="0"
				aria-valuemax="100">				
			</div>
		</div>
		PROG;
	}

	function display_controls(string $back_btn_url, string $tool_tip_title, bool $submit_btn_disabled, string $submit_btn_text = 'Confirm') {
		$disabled = $submit_btn_disabled ? 'disabled' : '';
		echo <<<CONTROLS
			<p class="m-0 mt-3 d-flex justify-content-between p-2 bg-light border border-1 rounded" style="border-color: var(--bs-gray-200)">
				<a class="btn btn-warning" href="$back_btn_url" data-bs-toggle="tooltip" data-bs-placement="bottom"
				   title="$tool_tip_title">
					<i class="fa-solid fa-angle-left"></i>
					Back
				</a>
				<button class="ms-auto btn btn-primary $disabled" type="submit" id="submitBtn" $disabled>
					$submit_btn_text
					<i class="fa-solid fa-angle-right"></i>
				</button>
			</p>
		CONTROLS;
	}

	function get_selection(string $key, string $label, string $script_name, callable $query_method, string ...$args) {
		// if there string query contains the key
		if (isset(STRING_QUERY[$key])) {
			// push the new key to the args
			array_push($args, STRING_QUERY[$key]);
			// query the database with given key and args
			$query_result = $query_method(...$args);
			// if there is an exception trigger a fatal error
			if ($query_result instanceof Exception) {
				display_fatal_error($query_result->getCode());
				return null;
			}
			// otherwise, save the query result
			if (!is_null($query_result)) {
				setcookie($key, $query_result[$key], time() + 86400, '/');
				$_COOKIE[$key] = $query_result[$key];
			}
		}
		// redirect and throw an error to user if the selection is not set
		if (!isset($_COOKIE[$key])) {
			redirect_with_error($label, $script_name);
		}
		// otherwise, return selection
		return $_COOKIE[$key];
	}

	/**
	 * @param $label string used to construct the flash id and message
	 * @param $script_name string the script name to redirect to
	 */
	function redirect_with_error(string $label, string $script_name)
	{
		create_flash_message(
			"${label} not selected",
			"Please select a valid <strong class=\"text-uppercase\">$label</strong> before proceeding",
			FLASH::ERROR
		);
		header('Location: ' . PROJECT_URL . "patient/$script_name.php");
		exit();
	}

	function str_eval($data): string {
		return $data;
	}
	$str_eval = 'str_eval';