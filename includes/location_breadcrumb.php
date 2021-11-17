<?php
if (!isset($locations)) $locations = [];

if (!isset($locations['Select Batches'])) //prepend the location with "Select Batches" as root
    $locations = array('Select Batches' => './index.php') + $locations;
?>

<aside class="border rounded p-3 pb-1 mt-3 bg-white">
    <h6 class="text-muted">Location</h6>
    <nav style="--bs-breadcrumb-divider: '➤'" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <?php
            foreach ($locations as $name => $href) {

                //consider the last index in the locations as active
                $isActive = array_search($name, array_keys($locations)) === count($locations) - 1;

                //echo each location
                echo sprintf(
                    '<li class="breadcrumb-item %s"> %s </li>',
                    $isActive ? 'active' : 'col-lg-12',
                    $isActive ? $name : //active location does not go anywhere 
                    "<a href=\"$href\">$name</a>" //non-active location will have href
                );
            }
            ?>

        </ol>
    </nav>
</aside>