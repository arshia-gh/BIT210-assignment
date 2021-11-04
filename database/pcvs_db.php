<?php

	require_once "db_connection.php";

	function find_vaccine($vaccine_id) {
		$sql = "SELECT * FROM vaccines WHERE vaccineID = '$vaccine_id'";
		return queryOne($sql);
	}

	function get_all_vaccines() {
		$sql = "SELECT * FROM vaccines";
		return queryAll($sql);
	}

	function query($sql) {
		global $db;
		return mysqli_query($db, $sql);
	}

	function queryOne($sql) {
		return mysqli_fetch_assoc(query($sql));
	}

	function queryAll($sql) {
		return mysqli_fetch_all(query($sql));
	}

	function set_vaccination_status($vaccination_id, $status) {
		global $db;
		$sql = "UPDATE VACCINATIONS SET status = '$status' WHERE vaccinationID = $vaccination_id";
		$result = mysqli_query($db, $sql);
		return $result ?: mysqli_error($db);
	}