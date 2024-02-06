<?php
$servername = "localhost";
$username = "root123";
$password = "";
$dbname = "crudb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);


    if (!$conn) {
        die("<script>alert('connection failed')</script>");
    }
?>