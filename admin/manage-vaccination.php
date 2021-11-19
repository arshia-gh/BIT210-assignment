<?php
require_once '../includes/table_generator.php';
require_once '../database/administrator_queries.php';
require_once '../includes/app_metadata.inc.php';
require_once '../includes/flash_messages.inc.php';

$current_admin = authenticate();

$vaccinationID = $_GET['vaccinationID'];
$vaccination = $admin_queries->find_vaccination($vaccinationID);
$patient = $admin_queries->find_user($vaccination['username']);
$batch = $admin_queries->find_batch($vaccination['batchNo']);
$vaccine = $admin_queries->find_vaccine($batch['vaccineID']);

if($batch['centreName'] !== $current_admin['centreName']) { //checks if the current admin have access to this vaccination
    redirect_to_login_form();
}

$statusColor = statusToColor($vaccination['status']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../asset/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../asset/css/style.css" />
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

    <title>Batch</title>

</head>

<body class="d-flex flex-column min-vh-100">
    <?php display_flash_message($vaccinationID . "UpdateMessage"); //flash if there is any message for this vaccination 
    ?>

    <header>
        <?php
        $nav_links = [
            'Home' => ['index.php', false],
            'Dashboard' => ['admin/index.php', false]
        ];

        //generates the nav bar
        require_once('../includes/navbar.inc.php');
        ?>
    </header>

    <main class="container flex-grow-1">
        <div class="row">
            <div class="col-12 col-lg-3 bg-light py-3">
                <!--location links-->
                <?php
                $locations =
                    [
                        'Select Vaccination' => "batch.php?batchNo=" . $vaccination['batchNo'],
                        'Manage Vaccination' => null
                    ];

                //generates the location breadcrumb
                require_once '../includes/location_breadcrumb.php'
                ?>

                <!--user info-->
                <?php require_once '../includes/user_info.inc.php' ?>
            </div>

            <div class="col-12 col-lg-9" style="min-height: 50vh">
                <section class="p-4 rounded-3 shadow-sm h-75 bg-filter-darken" style="background-image: url(https://image.freepik.com/free-vector/flat-hand-drawn-hospital-reception-scene_52683-54613.jpg);">
                   
                    <!--Vaccination information-->
                    <h3 class="text-white">Vaccination | <?= $vaccinationID ?></h3>
                    <div class="row text-white">
                        <div class="col-12 col-lg-9">
                            <p>Appointment Date | <?= $vaccination['appointmentDate'] ?>
                                <span class=<?= sprintf('"badge text-uppercase mx-2 bg-%s"', $statusColor) ?>>
                                    <?= $vaccination['status'] ?>
                                </span>
                            </p>
                        </div>

                        <!--"Back to list" button-->
                        <div class="col-12 col-lg-3">
                            <a class="btn btn-warning btn-sm float-end w-100 fw-sbold" href=<?= "./batch.php?batchNo=" . $vaccination['batchNo'] ?>>
                                Back to list</a>
                        </div>
                    </div>

                    <!--Other infomation such as Patient, Batch and Vaccine-->
                    <div class="bg-white rounded shadow p-4 mt-2 mt-lg-0">
                        <div id="vaccinationDetails">
                            <table class="table">
                                <thead class="table-primary">
                                    <tr>
                                        <th colspan="2">Patient Information</th>
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
                                <thead class="table-primary">
                                    <tr>
                                        <th colspan="2">Batch Information</th>
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

                            <!-- next operation title -->
                            <h6 class="text-center mb-3 text-dark">
                                <?=
                                $vaccination['status'] === 'pending' ?
                                    'Approval of Appointment' : ($vaccination['status'] === 'confirmed' ?
                                        'Administration of Vaccination' :
                                        "This vaccination has been {$vaccination['status']}") ?>
                            </h6>

                            <form method="POST" action="update-vaccination.php">
                                <div class="form-floating my-3">
                                    <!--show confirm/reject control if status is pending-->
                                    <?php if ($vaccination['status'] === 'pending') : ?>
                                        <div class="btn-group w-100" role="group" aria-label="radio toggle button group" id="statusButtonGroup">
                                            <input type="radio" class="btn-check" name="status" value="confirmed" id="rdbAccept" autocomplete="off" required />
                                            <label class="btn btn-outline-primary" for="rdbAccept">Confirm</label>

                                            <input type="radio" class="btn-check" name="status" value="rejected" id="rdbReject" autocomplete="off" />
                                            <label class="btn btn-outline-primary" for="rdbReject">Reject</label>
                                        </div>
                                    <?php elseif ($vaccination['status'] === 'confirmed') : ?>
                                        <input type="hidden" name="status" value="administered" />
                                    <?php endif ?>

                                    <!--Do not show remarks input and submit button if status is already rejected or administered-->
                                    <?php if ($vaccination['status'] === 'pending' || $vaccination['status'] === 'confirmed') : ?>
                                        <div class="form-floating my-3">
                                            <input type="text" class="form-control" placeholder="remarks" name="remarks" id="remarksInput" />
                                            <label for="floatingInput">Remarks</label>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary w-100" id='submitButton'>Submit</button>
                                        </div>
                                    <?php else : ?>
                                        <p><strong>Remarks | </strong> <?= $vaccination['remarks'] ?? 'No remarks recorded' ?></p>
                                    <?php endif ?>

                                    <!--hidden input for extra POST method information-->
                                    <input type="hidden" name="batchNo" value=<?= $batch['batchNo'] ?>>
                                    <input type="hidden" name="vaccinationID" value=<?= $vaccinationID ?>>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!--footer-->
    <?php require_once('../includes/footer.inc.php'); ?>

    <script src="../asset/js/bootstrap.bundle.min.js"></script>
    <script src="manage-vaccination.js"></script>
</body>

</html>