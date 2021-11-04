<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <title>index</title>
</head>
<body>
<?php
include_once "database/pcvs_db.php";

try {
print_table(find_batch("PF01"));

    // set_vaccination_status("20341010", "adminsterd");
 } catch (Exception $e) { echo $e->getMessage();}
 
//  set_vaccination_status("20341010", "administered");
// // print_r(find_vaccine("PF"));
?>
</body>
</html>
