<?php
require_once '../database/administrator_queries.php';
$vaccinationID = $_GET['vaccinationID'];
$vaccination = $admin_queries->find_vaccination($vaccinationID);
$patient = $admin_queries->find_user($vaccination['username']);
$batch = $admin_queries->find_batch($vaccination['batchNo']);
$vaccine = $admin_queries->find_vaccine($batch['vaccineID']);
?>

<div>
    <table class="table">
        <thead>
            <tr>
                <td colspan="2" class="text-muted">Patient Information</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-muted">Full Name</td>
                <td class="text-end"><?= $patient['fullName'] ?></td>
            </tr>
            <tr>
                <td class="text-muted">IC/Passport</td>
                <td class="text-end"><?= $patient['ICPassport'] ?></td>
            </tr>
        </tbody>
    </table>

    <table class="table mb-4">
        <thead>
            <tr>
                <td colspan="2" class="text-muted">Batch Information</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-muted">Batch Number</td>
                <td class="text-end"><?= $batch['batchNo'] ?></td>
            </tr>
            <tr>
                <td class="text-muted">Expiry Date</td>
                <td class="text-end"><?= $batch['expiryDate'] ?></td>
            </tr>
            <tr>
                <td class="text-muted">Vaccine Name</td>
                <td class="text-end"><?= $vaccine['vaccineName'] ?></td>
            </tr>
            <tr>
                <td class="text-muted">Manufacturer</td>
                <td class="text-end"><?= $vaccine['manufacturer'] ?></td>
            </tr>
        </tbody>
    </table>


    <!-- <div>
        <h6 class="text-center mb-3" id="operationTitle">
            <?= $vaccination['status'] === 'pending' ? 'Approval of Appointment' : 'Administration of Vaccination'; ?>
        </h6>
        <form id="manageVaccinationForm">
            <div class="form-floating my-3">

                <?php
                if ($vaccination['status'] === 'pending') {
                    echo '
                <div class="btn-group w-100" role="group" aria-label="radio toggle button group" id="statusButtonGroup" style="display: inline-flex;">
                    <input type="radio" class="btn-check" name="status" value="confirmed" id="rdbAccept" autocomplete="off" required="">
                    <label class="btn btn-outline-primary" for="rdbAccept">Confirm</label>

                    <input type="radio" class="btn-check" name="status" value="rejected" id="rdbtn-reject" autocomplete="off">
                    <label class="btn btn-outline-primary" for="rdbtn-reject">Reject</label>
                </div>';
                } ?>

                <div class="form-floating my-3">
                    <input type="text" class="form-control" placeholder="remarks" name="remarks" id="remarks-input">
                    <label for="floatingInput">Remarks</label>
                </div>

                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary w-100" id="submitButton" style="display: block;">Update Status</button>
                </div>
            </div>
        </form>
    </div> -->
</div>