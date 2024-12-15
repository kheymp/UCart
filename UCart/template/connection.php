<?php 
    $servername = "localhost";
    $username = "datapioneer";
    $password = "password";
    $dbname = "UCart";
    
    try {
        $mysqli = new mysqli($servername, $username, $password, $dbname);
        if ($mysqli->connect_errno) {
            throw new Exception("Failed to connect to MySQL: " . $mysqli->connect_error);
        }
    } catch (Exception $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    
    ?>