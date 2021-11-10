<?php
require_once '../database/administrator_queries.php';
require_once '../includes/flash.php';

$vaccinationID = $_POST['vaccinationID'];
$batchNo = $_POST['batchNo'];
$status = $_POST['status'];
$remarks = $_POST['remarks'];
$result;

switch ($status) {
    case 'confirmed':
        $result = $admin_queries->confirm_appointment($vaccinationID, $batchNo);
        break;
    case 'rejected':
        $result = $admin_queries->reject_appointment($vaccinationID, $remarks);
        break;
    case 'administered':
        $result = $admin_queries->confirm_vaccination_administered($vaccinationID, $remarks, $batchNo);
        break;
}

header('Location:' . $_SERVER['HTTP_REFERER']);

flash("ok", "message", FLASH_SUCCESS);


