<?php
include("connection.php");

$userName = $_POST["userName"];
$userEmail = $_POST["userEmail"];
$userPwd = $_POST["userPwd"];
$userPhone = $_POST["userPhone"];
$userAddr = $_POST["userAddr"];
$userCOO = $_POST["userCOO"];
$userDOB = $_POST["userDOB"];
$userGender = $_POST["userGender"];

$userEdu = $_POST["userEdu"];
$userResume = $_POST["userResume"];
$userImage = $_POST["userImage"];
$userUnique = $_POST["userUnique"];


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
else{
    echo"<script>window.location.assign='login.html'</script>";
}

?>

