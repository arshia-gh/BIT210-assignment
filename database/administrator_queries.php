<?php

require_once "../classes/DatabaseHandler.php";

final class AdminstratorDatabaseHandler extends DatabaseHandler
{
	public function find_batches_of_centre($centreName) : bool|array
	{
		$sql = "SELECT Batches.batchNo, expiryDate, vaccineName, quantityAvailable, quantityAdministered, 
				COUNT(vaccinationID) AS quantityPending FROM Batches 
				JOIN Vaccines ON Batches.vaccineID = Vaccines.vaccineID 
				LEFT JOIN 
				(SELECT batchNo, vaccinationID FROM Vaccinations WHERE status = 'pending') AS PendingVaccination 
				ON Batches.batchNo = PendingVaccination.batchNo 
				WHERE centreName = ?
				GROUP BY Batches.batchNo";

		return $this->query_all($sql, $centreName);
	}

	function find_batch($batchNo)
	{
		$sql = "SELECT * FROM
		(SELECT * FROM batches WHERE batchNo = ?) AS batch,
		(SELECT COUNT(*) AS 'quantityPending' FROM vaccinations WHERE batchNo = ? AND status = 'pending') AS quantityPending";

		return $this->query_one($sql, $batchNo, $batchNo);
	}

	function find_vaccinations_of_batch($batchNo)
	{
		$sql = "SELECT * FROM vaccinations WHERE batchNo = ?";
		return $this->query_all($sql, $batchNo);
	}

}

$admin_queries = new AdminstratorDatabaseHandler();
