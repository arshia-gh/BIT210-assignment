<?php

require_once "../classes/DatabaseHandler.php";

final class AdminstratorDatabaseHandler extends DatabaseHandler
{
	public function find_batches_of_centre($centreName): bool|array
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

	public function find_batch($batchNo)
	{
		$sql = "SELECT * FROM
		(SELECT * FROM batches WHERE batchNo = ?) AS batch,
		(SELECT COUNT(*) AS 'quantityPending' FROM vaccinations WHERE batchNo = ? AND status = 'pending') AS quantityPending";

		return $this->query_one($sql, $batchNo, $batchNo);
	}

	public function find_vaccinations_of_batch($batchNo)
	{
		$sql = "SELECT * FROM vaccinations WHERE batchNo = ?";
		return $this->query_all($sql, $batchNo);
	}

	public function set_vaccination_status_and_remarks($vaccination_id, $status, $remarks = null)
	{
		$remarks = $remarks === '' ? null : $remarks; //consider empty string as null on purpose
		$sql = "UPDATE VACCINATIONS SET status = ?, remarks = ? WHERE vaccinationID = ?";
		return $this->cud_query($sql, $status, $remarks, $vaccination_id);
	}

	public function decreases_batch_quantity_available($batchNo)
	{
		$sql = "UPDATE Batches SET quantityAvailable = quantityAvailable - 1 WHERE batchNo = '$batchNo'";
		return $this->cud_query($sql);
	}

	public function increases_batch_quantity_administered($batchNo)
	{
		$sql = "UPDATE Batches SET quantityAdministered = quantityAdministered + 1 WHERE batchNo = '$batchNo'";
		return $this->cud_query($sql);
	}

	public function confirm_appointment($vaccinationID, $batchNo)
	{
		$affectedVaccination = $this->set_vaccination_status_and_remarks($vaccinationID, 'confirmed');
		$affectedBatch = $this->decreases_batch_quantity_available($batchNo);
		return $affectedVaccination && $affectedBatch;
	}

	public function reject_appointment($vaccinationID, $remarks)
	{
		try {
			return $this->set_vaccination_status_and_remarks($vaccinationID, 'rejected', $remarks) == 1;
		} catch (Exception $ex) {
			return $ex;
		}
	}

	public function confirm_vaccination_administered($vaccinationID, $remarks, $batchNo)
	{
		try {
			$affectedVaccination = $this->set_vaccination_status_and_remarks($vaccinationID, 'administered', $remarks);
			$affectedBatch = $this->increases_batch_quantity_administered($batchNo);
			return $affectedVaccination && $affectedBatch;
		} catch (Exception $ex) {
			return $ex;
		}
	}
}

$admin_queries = new AdminstratorDatabaseHandler();
