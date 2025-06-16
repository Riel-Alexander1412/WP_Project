<?php
include("connection.php");

// 🟩 Collect form data
$empEmail   = $_POST["empEmail"];
$empPwd     = password_hash($_POST["empPwd"], PASSWORD_DEFAULT); // Secure password hash
$empName    = $_POST["empName"];
$empContact = $_POST["empContact"];
$empAddr    = $_POST["empAddr"];
$empDesc    = $_POST["empDesc"];

// 🟩 Handle uploaded image (store in 'datastore/' folder)
$empImagePath = "";
if (isset($_FILES["empImage"]) && $_FILES["empImage"]["error"] === UPLOAD_ERR_OK) {
    $imageName = uniqid("empimg_") . "_" . basename($_FILES["empImage"]["name"]);
    $imageDir = "datastore/";
    
    if (!is_dir($imageDir)) {
        mkdir($imageDir, 0777, true);
    }

    $empImagePath = $imageDir . $imageName;
    move_uploaded_file($_FILES["empImage"]["tmp_name"], $empImagePath);
} else {
    echo "<script>alert('Employer image file is missing.'); window.location.href='register.html';</script>";
    exit();
}

// 🔍 Check if email already exists
$checkQuery = "SELECT Email FROM employer WHERE Email = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("s", $empEmail);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo "<script>alert('Email already exists. Please use a different one.'); window.location.href='register.html';</script>";
    $checkStmt->close();
    $conn->close();
    exit();
}
$checkStmt->close();

// 📝 Simulated SQL query with actual values (for debug)
$insertquery = "INSERT INTO employer 
(email, password, name, contact, address, description, image)
VALUES (
'$empEmail',
'$empPwd',
'$empName',
'$empContact',
'$empAddr',
'$empDesc',
'$empImagePath'
)";
echo "<pre>DEBUG SQL QUERY:\n$insertquery</pre>";

// ✅ Insert using prepared statement
$insertStmt = $conn->prepare("INSERT INTO employer 
(email, password, name, contact, address, description, image)
VALUES (?, ?, ?, ?, ?, ?, ?)");

$insertStmt->bind_param("sssssss",
    $empEmail,
    $empPwd,
    $empName,
    $empContact,
    $empAddr,
    $empDesc,
    $empImagePath
);

// 🔄 Execute insert
if ($insertStmt->execute()) {
    echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
} else {
    echo "<script>alert('Registration failed. Please try again.'); window.location.href='register.html';</script>";
}

$insertStmt->close();
$conn->close();



?>
