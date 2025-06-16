<?php

include("connection.php");

$empEmail = $_POST[""];
$empPwd = $_POST[""];
$empName = $_POST[""];
$empContact = $_POST[""];
$empAddr = $_POST[""];
$empDesc = $_POST[""];
$empImage= $_POST[""];


$empquery = "INSERT INTO employer
        (email,password,name,contact,address,description,image)
        VALUES
        ('$empEmail','$empPwd','$empName','$empContact',
        '$empAddr','$empDesc','$empImage')";

$result = $conn->query($empquery);

if($result !== TRUE) {
    echo "Error: ".$insertquery;
}

?>