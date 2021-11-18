<?php
require_once '../database/administrator_queries.php';
require_once '../includes/app_metadata.inc.php';
require_once '../includes/flash_messages.inc.php';

authenticate(); //make sure the request is coming from actual admin

$vaccinationID = $_POST['vaccinationID'];
$batchNo = $_POST['batchNo'];
$status = $_POST['status'];
$remarks = $_POST['remarks'] ?? null;
$result = null;

try {
    //update vaccination status with database queries
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
        //display the status in strong uppercase for message
        $status = '<strong>' . ucfirst($status) . ' </strong>';
        
        create_flash_message($vaccinationID . 'UpdateMessage', 
        "Successfully marked this vaccination as $status", FLASH::SUCCESS);
    }
}
catch (Exception $ex) {
    //print the error as message
    create_flash_message($vaccinationID . 'UpdateMessage', 
    "Error " . $ex->getCode() . " | " . $ex->getMessage(), FLASH::ERROR);
}
 

header('Location:' . $_SERVER['HTTP_REFERER']); //go back to manage-vaccination.php




