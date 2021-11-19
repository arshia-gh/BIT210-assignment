<?php
require_once '../includes/table_generator.php';
require_once '../database/administrator_queries.php';
require_once '../includes/app_metadata.inc.php';

authenticate(); //make sure the request is coming from actual admin
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
  <header>
    <?php
    $nav_links = [
      'Home' => ['index.php', false],
      'Dashboard' => ['admin/index.php', false]
    ];
    require_once('../includes/navbar.inc.php');
    ?>
  </header>

  <main class="container flex-grow-1">
    <div class="row">
      <div class="col-12 col-lg-3 bg-light py-3">

      <!--location links-->
      <?php 
        $locations = ['Select Vaccination' => null];
        require_once '../includes/location_breadcrumb.php'
      ?>

        <!--user info-->
      <?php require_once '../includes/user_info.inc.php'?>
      
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
              $table_headers = ['Vaccination ID', 'Appointment Date', 'Status'];
              $vaccinations = $admin_queries->find_vaccinations_of_batch($batchNo);

              //map the vaccinations into the intended format to show
              $vaccinations = array_map(
                fn ($vaccination) => [
                  'vaccinationID' => $vaccination['vaccinationID'],
                  'appointmentDate' => $vaccination['appointmentDate'],
                  'status' => sprintf('<span class="badge bg-%s"> %s </span>', 
                  statusToColor($vaccination['status']), strtoupper($vaccination['status']))
                ],
                $vaccinations
              );

              //generates the vaccination table
              generate_table($vaccinations, 'vaccinationID', $table_headers, 
              true, 'There are no vaccination appointment currently.');
              ?>
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>

  <!--footer-->
	<?php require_once('../includes/footer.inc.php'); ?>

  <script type="text/javascript" src="../asset/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="batch.js"></script>
</body>

</html>