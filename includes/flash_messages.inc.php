<?php
	abstract class FLASH {
		const MESSAGES = "FLASH_MESSAGES";
		const ERROR = 'danger';
		const WARNING = 'warning';
		const INFO = 'info';
		const SUCCESS = 'success';
	}

	/**
	 * Create a flash message
	 *
	 * @param string $identifier
	 * @param string $message
	 * @param string $type
	 * @return void
	 */
	function create_flash_message(string $identifier, string $message, string $type): void
	{
		// remove existing message with the name
		if (isset($_SESSION[FLASH::MESSAGES][$identifier])) {
			unset($_SESSION[FLASH::MESSAGES][$identifier]);
		}
		// add the message to the session
		$_SESSION[FLASH::MESSAGES][$identifier] = ['message' => $message, 'type' => $type];
	}

	/**
	 * Format a flash message
	 *
	 * @param array $flash_message
	 * @return string
	 */
	function format_flash_message(array $flash_message): string
	{
		return sprintf('<div class="alert alert-%s alert-dismissible fade show" role="alert">%s
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>',
			$flash_message['type'],
			$flash_message['message']
		);
	}

	/**
	 * Display a flash message
	 *
	 * @param string $name
	 * @return void
	 */
	function display_flash_message(string $name): void
	{
		if (!isset($_SESSION[FLASH::MESSAGES][$name])) return;

		// get message from the session
		$flash_message = $_SESSION[FLASH::MESSAGES][$name];

		// delete the flash message
		unset($_SESSION[FLASH::MESSAGES][$name]);

		// display the flash message
		echo format_flash_message($flash_message);
	}

	/**
	 * Display all flash messages
	 *
	 * @return void
	 */
	function display_all_flash_messages(): void
	{
		if (!isset($_SESSION[FLASH::MESSAGES])) return;

		// get flash messages
		$flash_messages = $_SESSION[FLASH::MESSAGES];

		// remove all the flash messages
		unset($_SESSION[FLASH::MESSAGES]);

		// show all flash messages
		foreach ($flash_messages as $flash_message) {
			echo format_flash_message($flash_message);
		}
	}

	/**
	 * Flash a message
	 *
	 * @param string $name
	 * @param string $message
	 * @param string $type (FLASH:: ERROR, WARNING, INFO, SUCCESS)
	 * @return void
	 */
	function flash(string $name = '', string $message = '', string $type = ''): void
	{
		if ($name !== '' && $message !== '' && $type !== '') {
			create_flash_message($name, $message, $type);
		} else if ($name !== '' && $message === '' && $type === '') {
			display_flash_message($name);
		} else if ($name === '' && $message === '' && $type === '') {
			display_all_flash_messages();
		}
	}
