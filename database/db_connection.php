<?php
	$db_server_name = 'localhost';
	$db_username = 'root';
	$db_password = '';
	$db_name = 'pcvs_bit210';
	$db_connection = new mysqli($db_server_name, $db_username, $db_password, $db_name);

	if($db_connection->connect_errno) {
		die($db_connection->connect_error);
	}
	else {
		echo "<script>console.log(\"Successful connected to $db_name\")</script>";
	}
	// $db = mysqli_connect($db_server_name, $db_username, $db_password, $db_name);
?>