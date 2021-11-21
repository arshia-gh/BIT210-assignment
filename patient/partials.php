<?php
	/**
	 * resets the vaccination selection cookies
	 * by setting them to an expired date.
	 */
	function reset_vaccination_details()
	{
		setcookie('vaccineID', NULL, -1, '/');
		setcookie('centreName', NULL, -1, '/');
		setcookie('batchNo', NULL, -1, '/');
	}

	/**
	 * displays a progress bar
	 * @param int $now the current progress percentage
	 */
	function display_progress_bar(int $now)
	{
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

	/**
	 * displays two buttons, used to control the vaccination process
	 * @param string $back_btn_url the back button url
	 * @param string $tool_tip_title the back btn tool tip text
	 * @param bool   $submit_btn_disabled indicates if the confirmation (submit) btn should be disabled
	 * @param string $submit_btn_text sets the text in between submit button
	 */
	function display_controls(string $back_btn_url, string $tool_tip_title, bool $submit_btn_disabled, string $submit_btn_text = 'Confirm')
	{
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

	/**
	 * Invokes the given query supplier and saves the retrieved PK in a cookie
	 *
	 * @param mixed  $query_supplier
	 * @param string $key            used as an identifier when saving the data
	 * @param string ...$args        used when invoking the query supplier
	 *
	 * @return bool true on success and false on failure
	 * @throws \Exception
	 */
	function save_pk_in_cookie(mixed $query_supplier, string $key, string...$args) : bool
	{
		$query_result = $query_supplier(...$args);

		if ($query_result instanceof Exception) {
			throw new $query_result;
		}
		if (is_null($query_result) || (is_array($query_result) && count($query_result) < 1)) {
			return FALSE;
		}

		setcookie($key, $query_result[$key], time() + 86400, '/');
		$_COOKIE[$key] = $query_result[$key];
		return TRUE;
	}

	/**
	 * Gets the cookie value using the given key <br>
	 *
	 * @param string    $key              used to search for cookie
	 * @param ?callable $failure_callback invoked on failure (ex: result is null)
	 *
	 * @return string|null found cookie value
	 */
	function get_pk_from_cookie(string $key, ?callable $failure_callback = NULL) : ?string
	{
		$found_value = $_COOKIE[$key] ?? NULL;

		if (is_null($found_value) && !is_null($failure_callback)) {
			$failure_callback();
		}

		return $found_value;
	}

	/**
	 * Saves the retrieved value from query string using the given key <br>
	 * - redirect the user on failure <br>
	 *
	 * @param string   $key            used to search for cookie
	 * @param string   $redirect_label label used to generate the error and the redirect url
	 * @param callable $query_supplier used to retrieve the PK
	 * @param string   ...$args
	 *
	 * @return string|null retrieved cookie value
	 * @throws \Exception
	 */
	function save_or_get_pk_from_cookie(string $key, string $redirect_label, callable $query_supplier, string ...$args) : ?string
	{
		// if string query contains the key, save the key
		if (isset($_GET[$key])) {
			array_push($args, $_GET[$key]);
			save_pk_in_cookie($query_supplier, $key, ...$args);
		}

		$script_name = label_to_script_name($redirect_label);
		// redirect and throw an error to user if the selection is not set
		// otherwise, return selection
		return get_pk_from_cookie($key, fn () => redirect_with_selection_error($redirect_label, $script_name));
	}

	/**
	 * Converts label to script name (internal usage)
	 * @param string $label
	 * @return string script name
	 */
	function label_to_script_name(string $label) : string
	{
		return 'select-' .preg_replace('/\s/', '-', $label) . '.php';
	}

	/**
	 * redirect the user to a script under patient directory with a flash error
	 *
	 * @param $id string flash id
	 * @param $script_name string the script name to redirect to
	 * @param $msg string a custom msg
	 */
	function redirect_with_error(string $id, string $script_name, $msg) {
		create_flash_message($id, $msg, FLASH::ERROR);
		header('Location: ' . PROJECT_URL . "patient/$script_name");
		exit();
	}


	/**
	 * redirect the user to a script under patient directory with a flash selection error
	 * @param string $label used to construct the flash id and msg
	 * @param string $script_name the script name to redirect to
	 */
	function redirect_with_selection_error(string $label, string $script_name)
	{
		redirect_with_error("$label not selected", $script_name,
			"Please select a valid <strong class=\"text-uppercase\">$label</strong> before proceeding"
		);
	}

	/**
	 * redirect the user to an index.php under patient directory with a flash database error
	 * @param int $err_code
	 */
	function redirect_with_database_error(int $err_code) {
		redirect_with_error('database error', 'index.php',
			"An error occurred!\nError code: <strong>ERR_$err_code</strong>"
		);
	}

	function str_eval($data) : string
	{
		return $data;
	}

	$str_eval = 'str_eval';