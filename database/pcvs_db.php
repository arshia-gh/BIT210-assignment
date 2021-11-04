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

	function find_batches_of_centre($centreName) {
		$sql = "SELECT Batches.batchNo, expiryDate, vaccineName, quantityAvailable, quantityAdministered, 
				COUNT(vaccinationID) AS quantityPending FROM Batches 
				JOIN Vaccines ON Batches.vaccineID = Vaccines.vaccineID 
				LEFT JOIN 
				(SELECT batchNo, vaccinationID FROM Vaccinations WHERE status = 'pending') AS PendingVaccination 
				ON Batches.batchNo = PendingVaccination.batchNo 
				WHERE centreName = '$centreName'
				GROUP BY Batches.batchNo";
		
		return queryAll($sql);
	}

	function find_batch($batchNo) {
		$sql = "SELECT *, quantityAvailable - quantityOccupied AS 'quantityRemaining' FROM

		(SELECT * FROM batches WHERE batchNo = '$batchNo') AS batch,
	
		(SELECT COUNT(*) AS 'quantityPending' FROM vaccinations WHERE batchNo = '$batchNo' AND status = 'pending') AS QuantityPending,
	
		(SELECT COUNT(*) AS 'quantityOccupied' FROM vaccinations WHERE batchNo = '$batchNo' AND status IN ('pending','confirmed','administered')) AS quantityOccupied;";

		return queryAll($sql);
	}

	function find_vaccinations_of_batch($batchNo) {
		$sql = "SELECT * FROM vaccinations WHERE batchNo = '$batchNo'";
		return queryAll($sql);	
	}
