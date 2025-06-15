<?php
    include "connection.php";
    session_start();
    
    if(!$_SESSION['loggedin']){
        header("Location: Login.php");
        die();
    }
?>