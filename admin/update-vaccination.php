<?php
require_once '../database/administrator_queries.php';
require_once '../includes/app_metadata.inc.php';
require_once '../includes/flash_messages.inc.php';

authenticate();

$vaccinationID = $_POST['vaccinationID'];
$batchNo = $_POST['batchNo'];
$status = $_POST['status'];
$remarks = $_POST['remarks'] ?? null;
$result = null;

try {
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

    if($result) {
        create_flash_message($vaccinationID . 'UpdateMessage', 
        "Successfully marked this vaccination as <strong class=\"text-capitalize\">$status</strong>.", FLASH::SUCCESS);
    }
}
catch (Exception $ex) {
    create_flash_message($vaccinationID . 'UpdateMessage', 
    "Error " . $ex->getCode() . " | " . $ex->getMessage(), FLASH::ERROR);
}
 

header('Location:' . $_SERVER['HTTP_REFERER']);




