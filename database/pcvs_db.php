<?php

require_once "db_connection.php";



function query($sql)
{
	global $db_connection;

	$result = $db_connection->query($sql);

	if ($result === false)
		throw new mysqli_sql_exception($db_connection->error, $db_connection->errno);

	return $result;
}

function queryOne($sql)
{
	return (query($sql)->fetch_assoc());
}

function queryAll($sql)
{
	return (query($sql)->fetch_all(MYSQLI_ASSOC));
}

function set_vaccination_status_and_remarks($vaccination_id, $status, $remarks = null)
{
	$remarks = '' ? `'$remarks'` : null; 
	$sql = "UPDATE VACCINATIONS SET status = '$status', remark = $remarks WHERE vaccinationID = $vaccination_id";
	return query($sql);
}

function decreases_batch_quantity_available($batchNo) {
	$sql = "UPDATE Batch SET quantityAvailable = quantityAvailable - 1 WHERE batchNo = '$batchNo'";
	return query($sql);
}

function increases_batch_quantity_administered($batchNo) {
	$sql = "UPDATE Batch SET quantityAdministered = quantityAdministered + 1 WHERE batchNo = '$batchNo'";
	return query($sql);
}


function confirm_appointment($vaccinationID, $batchNo) {
	set_vaccination_status_and_remarks($vaccinationID, 'confirmed');
	decreases_batch_quantity_available($batchNo);
}

function reject_appointment($vaccinationID, $remarks) {
	set_vaccination_status_and_remarks($vaccinationID, 'confirmed', $remarks);
}

function confirm_vaccination_administered($vaccinationID, $remarks, $batchNo) {
	set_vaccination_status_and_remarks($vaccinationID, 'administered', $remarks);
	increases_batch_quantity_administered($batchNo);
}

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
