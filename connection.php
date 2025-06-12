<?php
$db = "job_finder_system";
$conn = mysqli_connect("localhost", "root", "", $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>