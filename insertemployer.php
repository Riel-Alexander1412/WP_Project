<?php

include("connection.php");

$empEmail = $_POST["empEmail"];
$empPwd = $_POST["empPwd"];
$empName = $_POST["empName"];
$empContact = $_POST["empContact"];
$empAddr = $_POST["empAddr"];
$empDesc = $_POST["empDesc"];
$empImage= $_POST["empImage"];


$empquery = "INSERT INTO employer
        (email,password,name,contact,address,description,image)
        VALUES
        ('$empEmail','$empPwd','$empName','$empContact',
        '$empAddr','$empDesc','$empImage')";

$result = $conn->query($empquery);

$emailexist = $conn->query("select email from ");

if($result !== TRUE) {
    echo "Error: ".$insertquery;
    echo"<script>alert('Registration Failed. Please Try Again')</script>";

}
else{
    echo"<script>window.location.assign='login.html'</script>";
}

?>