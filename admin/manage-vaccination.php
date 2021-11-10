<?php
require_once '../includes/table_generator.php';
require_once '../database/administrator_queries.php';
require_once '../includes/flash.php';

$vaccinationID = $_GET['vaccinationID'];
$vaccination = $admin_queries->find_vaccination($vaccinationID);
$patient = $admin_queries->find_user($vaccination['username']);
$batch = $admin_queries->find_batch($vaccination['batchNo']);
$vaccine = $admin_queries->find_vaccine($batch['vaccineID']);

$statusColor =
    $vaccination['status'] === "pending" ? "secondary" : 
    ($vaccination['status'] === "confirmed" ? "primary" : 
    ($vaccination['status'] === "rejected" ? "danger" : "success"));
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
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
    <header class="container-md">
        <nav class="navbar navbar-expand-md navbar-light">
            <a class="navbar-brand d-flex align-items-center" href="/index.html">
                <img src="../asset/svg/logo.svg" alt="navbar logo" class="me-1" />
                <span class="fw-bold">PCVS</span><span class="align-self-stretch border-end mx-1"></span><span class="fs-6 fw-light text-secondary">Private Covid-19 Vaccination Service</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar" aria-controls="main-navbar" aria-expanded="false" aria-label="Toggle main navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="main-navbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="./index.html">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./dashboard/administrator.html">Dashboard</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="container flex-grow-1">
        <div class="row">
            <div class="col-12 col-lg-3 bg-light py-3">
                <aside class="border rounded p-3 pb-1 mt-3 bg-white">
                    <h6 class="text-muted">Location</h6>
                    <nav style="--bs-breadcrumb-divider: '➤';" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item col-lg-12"><a href="./">Select Batches</a></li>
                            <li class="breadcrumb-item"><a href=<?= "./batch.php?batchNo=" . $vaccination['batchNo'] ?>>Select Vaccination</a></li>
                            <li class="breadcrumb-item active">Manage Vaccination</li>
                        </ol>
                    </nav>
                </aside>

                <!--user info-->
                <aside class="border rounded bg-white p-3 mt-3">
                    <header class="d-flex justify-content-between align-items-end">
                        <h6 class="text-muted">User Information</h6>
                        <div id="logout" class="d-none">
                            <button id="logoutBtn" class="btn btn-warning btn-sm">logout</button>
                        </div>
                    </header>
                    <figure class="row mt-3 justify-content-center align-items-center">
                        <div class="col-lg-6 col-3">
                            <img id="user-avatar" class="img-fluid" src="../asset/img/male_man_people_person_avatar_white_tone_icon.png" />
                        </div>
                        <footer class="col-9 col-lg-12 mt-3">
                            <ul id="userInfo" class="list-group list-group-flush text-break text-center">
                                <li class="list-group-item">Not logged in</li>
                            </ul>
                        </footer>
                    </figure>
                </aside>
            </div>

            <div class="col-12 col-lg-9" style="min-height: 50vh">
                <section class="p-4 rounded-3 shadow-sm h-75 bg-filter-darken" style="background-image: url(https://image.freepik.com/free-vector/flat-hand-drawn-hospital-reception-scene_52683-54613.jpg);">
                    <div class="text-white">
                        <h3>Vaccination | <?= $vaccinationID ?></h3>
                        <p>Appointment Date | <?= $vaccination['appointmentDate'] ?>
                            <span class=<?= sprintf('"badge text-uppercase mx-2 bg-%s"', $statusColor) ?>>
                                <?= $vaccination['status'] ?>
                            </span>
                            <a class="btn btn-warning btn-sm float-end w-25 fw-bold" href=<?= "./batch.php?batchNo=" . $vaccination['batchNo'] ?>>
                                Back to list</a>
                        </p>
                    </div>

                    <div class="bg-white rounded shadow p-4">
                        <?php display_flash_message("ok") ?>
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
                                    <input type="hidden" name="batchNo" value=<?= $batch['batchNo'] ?>>
                                    <input type="hidden" name="vaccinationID" value=<?= $vaccinationID ?>>
                                </div>
                            </form>

                            <div class="d-flex justify-content-center">

                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <footer class="bg-dark">
        <section class="container-md py-2 text-white text-center">
            <h2 class="h2 m-0">PCVS</h2>
            <p class="fs-4 fw-light m-0">Private Covid-19 Vaccination Service </p>
            <hr />
            <small class="text-muted">PCVS - copyright&copy; 2021</small>
        </section>
    </footer>

    <script src="../asset/js/bootstrap.bundle.min.js"></script>
    <script>
        const statusButtonGroup = document.getElementById("statusButtonGroup");
        const rdbAccept = document.getElementById("rdbAccept");
        const remarksInput = document.getElementById("remarksInput");

        statusButtonGroup.onchange = () => {
            const isAccepting = rdbAccept.checked;
            remarksInput.disabled = isAccepting;
            btnSubmit.innerHTML = (isAccepting ? "Confirm" : "Reject") + " Appointment";
        };
    </script>
</body>

</html>