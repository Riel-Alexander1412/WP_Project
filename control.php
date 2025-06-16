<?php
    include "connection.php";
    session_start();
    //error_reporting(0);
    
    if(!isset($_SESSION["loggedin"])){
        header("Location: Login.php");
        die();
    }
?>