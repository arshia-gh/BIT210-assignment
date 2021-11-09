<?php
require_once '../includes/table_generator.php';
require_once '../database/administrator_queries.php';
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
              <li class="breadcrumb-item"><a href="./">Select Batches</a></li>
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
          <div class="border rounded text-white shadow p-4">
            <?php
            $batchNo = $_GET['batchNo'];
            $batch = $admin_queries->find_batch($batchNo);

            echo "<h3>Batch {$batchNo}</h3>";
            echo "<p>Expires on {$batch['expiryDate']}</p>";

            echo '<ul class="list-group list-group-horizontal-md">
                  <li class="list-group-item list-group-item-primary">Quantity</li>';

            function generateInfo($attribute)
            { //this will generate the list group item depending on attribute name
              global $batch;
              echo '<li class="list-group-item">' . $attribute;
              echo '<span class="badge rounded-pill list-group-item-primary ms-2">';
              echo "{$batch['quantity' .$attribute]}</span></li>";
            }

            generateInfo('Available');
            generateInfo('Pending');
            generateInfo('Administered');
            echo "</ul>";
            ?>
          </div>

          <div class="container border rounded bg-white rounded shadow text-dark py-3 my-3">
            <h3>Vaccination List</h3>
            <p class="text-muted">Select a vaccination to view
              <span id="changesAppliedBadge" class="float-end badge bg-success d-none">
                <span class="fa fa-check pe-1"></span> Changes Applied
              </span>
            </p>
            <div id='tableContainer'>
              <?php
              $table_headers = ['Vaccination ID', 'Status', 'Appoinement Date'];
              $vaccinations = $admin_queries->find_vaccinations_of_batch($batchNo);

              // function onBatchSelected($batchNo) {
              //   header("Location: /batch/$batchNo");
              // }

              $vaccinations = array_map(
                fn ($vaccination) => [
                  'vaccinationID' => $vaccination['vaccinationID'],
                  'appointmentDate' => $vaccination['appointmentDate'],
                  'status' => $vaccination['status']
                ],
                $vaccinations
              );

              GenerateTable($vaccinations, 'vaccinationID', $table_headers);
              ?>
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

  <!--Modal-->
  <div class="modal fade" id="manageVaccinationModal" tabindex="-1" aria-labelledby="addBatchLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addBatchLabel">Vaccination</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body px-4">
          <div id="vaccinationDetailContainer"></div>
          <div>
            <h6 class="text-center mb-3" id="operationTitle">Operation Title</h6>
            <form id='manageVaccinationForm'>
              <div class="form-floating my-3">

                <div class="btn-group w-100" role="group" aria-label="radio toggle button group" id="statusButtonGroup">
                  <input type="radio" class="btn-check" name="status" value="confirmed" id="rdbAccept" autocomplete="off" required>
                  <label class="btn btn-outline-primary" for="rdbAccept">Confirm</label>

                  <input type="radio" class="btn-check" name="status" value="rejected" id="rdbtn-reject" autocomplete="off">
                  <label class="btn btn-outline-primary" for="rdbtn-reject">Reject</label>
                </div>

                <div class="form-floating my-3">
                  <input type="text" class="form-control" placeholder="remarks" name="remarks" id="remarks-input">
                  <label for="floatingInput">Remarks</label>
                </div>

                <div class="d-flex justify-content-center">
                  <button type="submit" class="btn btn-primary w-100" id='submitButton'>Submit</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../asset/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="./batch.js"></script>
</body>

</html>