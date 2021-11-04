<?php

require_once "db_connection.php";

function find_vaccine($vaccine_id)
{
	$sql = "SELECT * FROM vaccines WHERE vaccineID = '$vaccine_id'";
	return queryOne($sql);
}

function get_all_vaccines()
{
	$sql = "SELECT * FROM vaccines";
	return queryAll($sql);
}

function query($sql)
{
	global $db;
	$result = mysqli_query($db, $sql);
	if ($result === false)
		throw new ErrorException(mysqli_error($db), mysqli_errno($db));

	return $result;
}

function queryOne($sql)
{
	return mysqli_fetch_assoc(query($sql));
}

function queryAll($sql)
{
	return mysqli_fetch_all(query($sql), MYSQLI_ASSOC);
}

function set_vaccination_status($vaccination_id, $status)
{
	$sql = "UPDATE VACCINATIONS SET status = '$status' WHERE vaccinationID = $vaccination_id";
	return query($sql);
}

function find_batches_of_centre($centreName)
{
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

function find_batch($batchNo)
{
	$sql = "SELECT * FROM
		(SELECT * FROM batches WHERE batchNo = '$batchNo') AS batch,
		(SELECT COUNT(*) AS 'quantityPending' FROM vaccinations WHERE batchNo = '$batchNo' AND status = 'pending') AS quantityPending";

	return queryAll($sql);
}

function find_vaccinations_of_batch($batchNo)
{
	$sql = "SELECT * FROM vaccinations WHERE batchNo = '$batchNo'";
	return queryAll($sql);
}

// function confirm_appointment($vaccinationID) {

// }

function print_table($result)
{
	echo "<table class='table table-hover table-light'>";
	foreach ($result as $index => $obj) {

		if ($index === 0) {
			echo "<tr class='table-secondary'>";
			foreach ($obj as $key => $value) {
				echo "<th>$key</th>";
			}
			echo "</tr>";
		}

		echo "<tr>";
		foreach ($obj as $key => $value) {
			echo "<td>$value<br /></td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}
