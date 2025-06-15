<?php
include ('connection.php');

$userID = $_POST[''];
$userName = $_POST[''];
$userEmail = $_POST[''];
$userPwd = $_POST[''];
$userPhone = $_POST[''];
$userAddr = $_POST[''];
$userCOO = $_POST[''];
$userDOB = $_POST[''];
$userGender = $_POST[''];
$userEdu = $_POST[''];
$userUnique = $_POST[''];

$query = "INSERT INTO user VALUES
        ('$userID','$userName','$userPwd','$userPhone','$userAddr',
        '$userCOO','$userDOB','$userGender','$userEdu','$userUnique')";

$result = $conn->query($query);

if ($result === TRUE) {
    echo 'Upload Successful';
    echo "<script>window.location.assign='index.html'</script>";
} else {
    echo 'Error: ' . $query;
}

?>
