<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
require_once '../includes/table_generator.php';
require_once '../database/administrator_queries.php';
require_once '../includes/app_metadata.inc.php';
require_once '../includes/flash_messages.inc.php';

$current_admin = ['centreName' => 'Century Medical Centre'];
$healthcare_centre = $admin_queries->find_centre($current_admin['centreName']);
$prevFormData = null;

if (isset($_COOKIE['addBatch_formData'])) {
    $prevFormData = json_decode($_COOKIE['addBatch_formData'], true);
}
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

    <title>List of batches</title>
</head>

<body class="d-flex flex-column min-vh-100">
    <?php
	display_flash_message('AddBatchMessage');

    $nav_links = [
        'Home' => ['../index.php', false],
        'Dashboard' => ['../admin/index.php', false]
    ];
    require_once('../includes/navbar.inc.php');
    ?>

    <main class="container flex-grow-1">
        <div class="row">
            <div class="col-12 col-lg-3 bg-light py-3">
                <aside class="border rounded p-3 pb-1 mt-3 bg-white">
                    <h6 class="text-muted">Location</h6>
                    <nav style="--bs-breadcrumb-divider: '➤'" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item col-lg-12"><a href="./index.php">Select Batches</a></li>
                            <li class="breadcrumb-item active">Add Batch</li>
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
                    <h3 class="text-white mx-3">Add New Batch</h3>
                    <div class="row bg-white rounded shadow m-3">
                        <div class="col-sm p-4 bg-light rounded">
                            <form id="addBatchForm" method="POST" action="batch_submission.php">
                                <p class="text-muted text-center">Select Vaccine</p>
                                <input type="hidden" name="centreName" value=<?=sprintf('"%s"', $healthcare_centre['centreName'])?>/>

                                <select class="form-select" id="vaccineSelect" aria-label="Vaccine ID" required name="vaccineID"
                                    value=<?=$prevFormData['vaccineID'] ?>>
                                    
                                <option selected hidden value="">Select a vaccine</option>
                                    <?php
                                    $vaccines = $admin_queries->get_all_vaccines();
                                    $prevVaccineID = $prevFormData['vaccineID'] ?? null;

                                    foreach ($vaccines as $vaccine) {
                                        echo sprintf('"<option value="%s" data-manufacturer="%s" %s> %s </option>',
                                            $vaccine['vaccineID'], $vaccine['manufacturer'], 
                                            $prevVaccineID === $vaccine['vaccineID'] ? 'selected' : '',
                                            $vaccine['vaccineName']);
                                    }
                                    ?>
                                </select>

                                <input readonly class="form-control mt-3" id="manufacturerInput" placeholder="Manufacturer" />

                                <hr />

                                <p class="text-muted text-center">Enter Batch Details</p>

                                <div class="form-floating my-3">
                                    <input required type="text" class="form-control" id="batchNoInput" 
                                        placeholder="Batch Number" name="batchNo"
                                        style="text-transform: uppercase"
                                        <?= $prevFormData == null ? '' : 'autofocus' 
                                        //autofocus if previous formData exist because it means containing duplicated batchNo ?> /> 
                                    <label for="floatingInput">Batch Number</label>
                                </div>

                                <div class="form-floating my-3">
                                    <input required type="number" min="1" class="form-control" id="quantityInput" 
                                    placeholder="Quantity Available" name="quantityAvailable" 
                                    value=<?=$prevFormData['quantityAvailable'] ?? null?> />
                                    <label for="floatingInput">Quantity Available</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input required type="date" class="form-control" id="expiryDateInput" placeholder="Expiry Date" 
                                        name="expiryDate" min=<?=date('Y-m-d')?>
                                        value=<?=$prevFormData['expiryDate'] ?? null?> />
                                    <label for="floatingInput">Expiry Date</label>
                                </div>

                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary w-100" id="submitButton">Add</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-sm d-none d-lg-block">
                            <div id="carouselCovid" class="carousel slide h-100 mx-3 d-flex align-items-center" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="https://image.freepik.com/free-vector/organic-flat-vaccination-campaign-illustration_23-2148955324.jpg" class="d-block w-100" alt="image of encouraging youth vaccination" />
                                    </div>
                                    <div class="carousel-item">
                                        <img src="https://image.freepik.com/free-vector/flat-hand-drawn-doctor-injecting-vaccine-patient_23-2148872143.jpg" class="d-block w-100" alt="image of encouraging adult vaccination" />
                                    </div>
                                    <div class="carousel-item">
                                        <img src="https://image.freepik.com/free-vector/vaccine-concept-illustration_114360-5376.jpg" class="d-block w-100" alt="image of encouraging old forks vaccination" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
    </main>

	<?php require_once('../includes/footer.inc.php'); ?>

    <script src="../asset/js/bootstrap.bundle.min.js"></script>
    <script src="add-batch.js"></script>
</body>

</html>