<?php
require_once '../database/administrator_queries.php';
require_once '../includes/app_metadata.inc.php';
require_once '../includes/flash_messages.inc.php';

authenticate(); //make sure the request is coming from actual admin

$batchNo = $_POST['batchNo'];
$quantityAvailable = $_POST['quantityAvailable'];
$expiryDate = $_POST['expiryDate'];
$vaccineID = $_POST['vaccineID'];
$centreName = $_POST['centreName'];

if ($batchNo) { //change to uppercase if exist
    $batchNo = strtoupper($batchNo);
}

try {
    //server side validation
    if ($quantityAvailable < 1)
        throw new Exception("Illegal Operation, initial value of Quantity Available must be at least 1.");

    $tomorrowDate =  date('Y-m-d', strtotime("+1 day"));

    if ($expiryDate < $tomorrowDate)
        throw new Exception("Illegal Operation, the expiry date must be one day after current date.");
    //end of server side validation

    //inserting new batch into database
    $insertedResult = $admin_queries->add_batch($batchNo, $quantityAvailable, $expiryDate, $vaccineID, $centreName);

    if ($insertedResult === 1) {
        create_flash_message(
            'AddBatchMessage',
            "Successfully added batch <strong class=\"text-capitalize\">$batchNo</strong>.",
            FLASH::SUCCESS
        );

        header("Location: " . PROJECT_URL . '/admin/index.php'); //go back to index page which will be updated with new batch
    }
} catch (Exception $ex) {

    $isDuplicatedEntry = str_contains($ex->getMessage(), "1062 Duplicate entry"); //error code 1062 is for duplicate entry
    $duplicatedMessage = "Batch Number <strong>$batchNo</strong> already exists. <br/> Kindly enter a new one.";

    create_flash_message(
        'AddBatchMessage',
        $isDuplicatedEntry ? $duplicatedMessage : $ex->getMessage(),
        FLASH::ERROR
    );

    $addBatch_formData = array(
        "quantityAvailable" => $quantityAvailable,
        "expiryDate" => $expiryDate,
        "vaccineID" => $vaccineID
    );

    //store the current form data in the cookies to 
    //fill back the form data in add-batch form later
    setcookie("addBatch_formData", json_encode($addBatch_formData), time() + 30); //set the expiry time same as default timeout which is 30 seconds

    header("Location: " . $_SERVER['HTTP_REFERER']); //go back to the add batch form
}
