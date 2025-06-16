<?php
include("connection.php");

// 1. Capture user input
$userName   = $_POST["userName"];
$userEmail  = $_POST["userEmail"];
$userPwd    = password_hash($_POST["userPwd"], PASSWORD_DEFAULT);
$userPhone  = $_POST["userPhone"];
$userAddr   = $_POST["userAddr"];
$userCOO    = $_POST["userCOO"] ?? "Unknown";
$userDOB    = $_POST["userDOB"];
$userGender = $_POST["userGender"];
$userEdu    = $_POST["userEdu"];
$userUnique = $_POST["userUnique"];

// 2. Prepare folders
$resumeDir  = "uploads/resumes/";
$imageDir   = "datastore/";

if (!is_dir($resumeDir)) mkdir($resumeDir, 0777, true);
if (!is_dir($imageDir)) mkdir($imageDir, 0777, true);

// 3. Handle resume upload
if ($_FILES["userResume"]["error"] !== UPLOAD_ERR_OK) {
    die("Resume upload failed with error: " . $_FILES["userResume"]["error"]);
}
$resumeFile  = $_FILES["userResume"];
$resumeName  = uniqid("resume_") . "_" . basename($resumeFile["name"]);
$resumePath  = $resumeDir . $resumeName;
move_uploaded_file($resumeFile["tmp_name"], $resumePath);

// 4. Handle image upload (to datastore/)
$imagePath = "";
if (isset($_FILES["userImage"]) && $_FILES["userImage"]["error"] === UPLOAD_ERR_OK) {
    $imageFile = $_FILES["userImage"];
    $imageName = uniqid("img_") . "_" . basename($imageFile["name"]);
    $imagePath = $imageDir . $imageName;
    move_uploaded_file($imageFile["tmp_name"], $imagePath);
}

// 5. Check if email already exists
$check = $conn->prepare("SELECT Email FROM user WHERE Email = ?");
$check->bind_param("s", $userEmail);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "<script>alert('Email already registered. Please use a different one.'); window.location.href='register.html';</script>";
    $check->close();
    $conn->close();
    exit();
}
$check->close();

$insertquery = "INSERT INTO user 
(Name, Email, Password, PhoneNum, Address, COO, DoB, Gender, HiEdu, UniFeat, Resume, Image)
VALUES (
'$userName',
'$userEmail',
'$userPwd',
'$userPhone',
'$userAddr',
'$userCOO',
'$userDOB',
'$userGender',
'$userEdu',
'$userUnique',
'$resumePath',
'$imagePath'
)";
echo "<pre>DEBUG SQL QUERY:\n$insertquery</pre>";

// 7. Insert user into DB using prepared statement
$insert = $conn->prepare("INSERT INTO user 
(Name, Email, Password, PhoneNum, Address, COO, DoB, Gender, HiEdu, UniFeat, Resume, Image)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$insert->bind_param(
    "ssssssssssss",
    $userName,
    $userEmail,
    $userPwd,
    $userPhone,
    $userAddr,
    $userCOO,
    $userDOB,
    $userGender,
    $userEdu,
    $userUnique,
    $resumePath,
    $imagePath
);

if ($insert->execute()) {
    echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
} else {
    echo "Error: " . $insert->error;
}

$insert->close();
$conn->close();



?>
