<?php
mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect("localhost", "root", "", "autocare_core_db");

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Unable to connect to the database. Please try again later.");
}

mysqli_set_charset($conn, "utf8mb4");
?>
