<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "do-an-1";

    $conn = new mysqli($host, $user, $password, $database);

    if($conn->connect_error){
        die("KET NOI THAT BAI" . $conn->connect_error);
    }
?>
