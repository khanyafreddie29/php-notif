<?php
// app/includes/db.php

function db() {
    static $conn;

    if ($conn === null) {
        // Update these with your actual DB credentials
        $host = 'localhost';
        $username = 'root';
        $password = '';  // If you set a password, include it here
        $database = 'tracker_db'; // change this to your DB name

        $conn = new mysqli($host, $username, $password, $database);

        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }

        // Set charset to UTF-8 for safety
        $conn->set_charset('utf8mb4');
    }

    return $conn;
}
?>