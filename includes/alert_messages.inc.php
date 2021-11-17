<?php

	abstract class ALERT
	{
		const INFO = 'info';
		const ERROR = 'danger';
		const PRIMARY = 'primary';
		const WARNING = 'warning';
		const SUCCESS = 'success';

		const INFO_ICON = 'info-circle';
		const ERROR_ICON = 'bug';
		const WARNING_ICON = 'exclamation-triangle';
		const SUCCESS_ICON = 'check-circle';

		const DISMISSIBLE_BTN = "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>";
		const DISMISSIBLE_CLASS = 'alert-dismissible fade show';
	}

	function get_dismissible_btn(bool $cond) : string
	{
		return $cond ? ALERT::DISMISSIBLE_BTN : '';
	}

	function get_alert_class(bool $dismissible, $type) : string
	{
		$dismissible_class = $dismissible ? ALERT::DISMISSIBLE_CLASS : '';
		return "alert alert-$type border border-5 border-top-0 border-bottom-0 border-end-0 border-$type $dismissible_class";
	}

	function get_alert_icon(?string $icon_type) : string
	{
		return !is_null($icon_type) ? "<i class=\"fas fa-$icon_type d-md-inline d-none fa-lg\"></i>" : '';
	}

	function display_alert(string $msg, string $type, ?string $icon_type = NULL,
	                       bool   $dismissible = FALSE
	)
	{
		$alert_class = get_alert_class($dismissible, $type);
		$dismissible_btn = get_dismissible_btn($dismissible);
		$icon = get_alert_icon($icon_type);
		echo <<<ALERT
			<div class="$alert_class">
				$icon
				<span class="ms-md-2">$msg</span>
				$dismissible_btn
			</div>
		ALERT;
	}

	function display_fatal_error($error_code)
	{
		display_alert_with_body(
			"An <strong>ERROR</strong> occurred, please contact an administrator",
			"Please report the following error code: <strong>ERR_$error_code</strong>",
			ALERT::ERROR,
			ALERT::ERROR_ICON
		);
	}

	function display_alert_with_body(string  $title, string $msg, string $type,
	                                 ?string $icon_type = NULL, bool $dismissible = FALSE
	)
	{
		$alert_class = get_alert_class($dismissible, $type);
		$dismissible_btn = get_dismissible_btn($dismissible);
		$icon = get_alert_icon($icon_type);
		echo <<<ALERT
			<div class="$alert_class">
				<div class="alert-heading">
					$icon
					<span class="ms-md-2">$title</span>
					$dismissible_btn
				</div>
				<hr>
				<p class="mb-0">$msg</p>
			</div>
		ALERT;
	}