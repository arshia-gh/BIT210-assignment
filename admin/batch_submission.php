<?php 

require_once '../database/administrator_queries.php';

$batchNo = $_POST['batchNo'];
$quantityAvailable = $_POST['quantityAvailable'];
$expiryDate = $_POST['expiryDate'];
$vaccineID = $_POST['vaccineID'];
$centreName = $_POST['centreName'];

try {
    $insertedResult = $admin_queries->add_batch($batchNo, $quantityAvailable, $expiryDate, $vaccineID, $centreName);
    header("Location: " . $_SERVER['HTTP_REFERER']); //go back to previous page which page will be updated with new batch
}
catch (Exception $ex) {
    echo $ex->getMessage();
}

?>