<?php

require_once "../classes/DatabaseHandler.php";

final class AdminstratorDatabaseHandler extends DatabaseHandler
{
	/**
	 * fetch all batches under the given centreName, 
	 * with vaccineName and quantityPending as additional information
	 */
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

	/**
	 * fetch the batch with the given batchNo, with quantityPending as additional information
	 */
	public function find_batch($batchNo)
	{
		$sql = "SELECT * FROM
		(SELECT * FROM batches WHERE batchNo = ?) AS batch,
		(SELECT COUNT(*) AS 'quantityPending' FROM vaccinations WHERE batchNo = ? AND status = 'pending') AS quantityPending";

		return $this->query_one($sql, $batchNo, $batchNo);
	}

	/**
	 * find all the vaccinations under a batch with the given batchNo
	 */
	public function find_vaccinations_of_batch($batchNo)
	{
		$sql = "SELECT * FROM vaccinations WHERE batchNo = ?";
		return $this->query_all($sql, $batchNo);
	}

	/**
	 * set the status and remarks attribute of vaccination
	 * @return int number of row affected
	 * @param string $vaccinationID the ID of the targeted vaccination to update
	 * @param string $status the status to set
	 * @param string $remarks the remarks to set
	 */
	public function set_vaccination_status_and_remarks($vaccinationID, $status, $remarks = null) : int
	{
		$remarks = $remarks === '' ? null : $remarks; //consider empty string as null on purpose
		$sql = "UPDATE VACCINATIONS SET status = ?, remarks = ? WHERE vaccinationID = ?";
		return $this->cud_query($sql, $status, $remarks, $vaccinationID);
	}

	/**
	 * decrease the quantityAvailable of batch by 1 with the given batchNo
	 * @return int number of row affected
	 * @param string $batchNo the batch number of the targeted batch to update
	 */
	public function decreases_batch_quantity_available($batchNo)
	{
		$sql = "UPDATE Batches SET quantityAvailable = quantityAvailable - 1 WHERE batchNo = '$batchNo'";
		return $this->cud_query($sql);
	}

	
	/**
	 * increase the quantityAdministered of batch by 1 with the given batchNo
	 * @return int number of row affected
	 * @param string $batchNo the batch number of the targeted batch to update
	 */
	public function increases_batch_quantity_administered($batchNo)
	{
		$sql = "UPDATE Batches SET quantityAdministered = quantityAdministered + 1 WHERE batchNo = '$batchNo'";
		return $this->cud_query($sql);
	}

	/**
	 * set the status of the vaccination to "confirmed" and 
	 * invoke decreases_batch_quantity_available() to
	 * decrease the quantityAvailable of the batch by 1
	 */
	public function confirm_appointment($vaccinationID, $batchNo)
	{
		$affectedVaccination = $this->set_vaccination_status_and_remarks($vaccinationID, 'confirmed');
		$affectedBatch = $this->decreases_batch_quantity_available($batchNo);
		return $affectedVaccination && $affectedBatch;
	}

	/**
	 * set the status of the vaccination to "rejected" and
	 * set the remarks if it is given by the parameter
	 */
	public function reject_appointment($vaccinationID, $remarks)
	{
		try {
			return $this->set_vaccination_status_and_remarks($vaccinationID, 'rejected', $remarks) == 1;
		} catch (Exception $ex) {
			return $ex;
		}
	}

	/**
	 * set the status of the vaccination to "administered" and
	 * set the remarks if it is given by the parameter
	 * 
	 * this method also invoke increases_batch_quantity_administered() to
	 * increase the quantityAdministered of the batch by 1
	 */
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
