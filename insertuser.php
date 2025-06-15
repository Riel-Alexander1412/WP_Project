<?php
include("connection.php");

$userName = $_POST["userName"];
$userEmail = $_POST["userEmail"];
$userPwd = $_POST["userPwd"];
$userPhone = $_POST["userPhone"];
$userAddr = $_POST["userAddr"];
$userCOO = $_POST["userCOO"];
$userDOB = $_POST[""];
$userGender = $_POST[""];

$userEdu = $_POST[""];
$userResume = $_POST[""];
$userImage = $_POST[""];
$userUnique = $_POST[""];


$query = "INSERT INTO user 
        (name,email,password,phonenum,address,coo,
        dob,gender,hiedu,unifeat,resume,image)
        VALUES
        ('$userName','$userEmail','$userPwd','$userPhone','$userAddr','$userCOO',
        '$userDOB','$userGender','$userEdu','$userUnique','$$userResume','$$userImage')";


$result = $conn->query($query);


if($result !== TRUE) {
    echo "Error: ".$insertquery;
}

?>

