<?php
include "db.php";

$name = "Admin";
$email = "admin@autocare.com";
$phone = "9876543210";
$password = password_hash("Admin@123", PASSWORD_DEFAULT);
$role = "admin";

$q = mysqli_prepare($conn, "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($q, "sssss", $name, $email, $phone, $password, $role);

if (mysqli_stmt_execute($q)) {
    echo "Admin account created successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>