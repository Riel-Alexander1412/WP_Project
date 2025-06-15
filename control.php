<?php
    include "connection.php";
    session_start();
    
    if(!isset($_SESSION["loggedin"])){
        header("Location: Login.php");
        die();
    }
?>