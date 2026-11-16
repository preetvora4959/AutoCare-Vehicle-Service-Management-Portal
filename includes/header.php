
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AutoCare</title>
    <link rel="stylesheet" href="/AutoCare-Core-PHP/css/style.css">
</head>
<body>

<header>
    <div class="container nav">

        <a class="brand" href="/AutoCare-Core-PHP/index.php">AutoCare</a>

        <nav class="main-navigation">
            <a href="/AutoCare-Core-PHP/index.php">Home</a>
            <a href="/AutoCare-Core-PHP/services.php">Services</a>
            <a href="/AutoCare-Core-PHP/contact.php">Contact</a>
        </nav>

        <div class="nav-account">

            <?php if (isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer") { ?>

                <a href="/AutoCare-Core-PHP/customer/dashboard.php" class="nav-dashboard-btn">My Dashboard</a>

                <div class="nav-profile-menu">
                    <button type="button" class="nav-profile-trigger">
                        <span class="nav-user-icon">👤</span>
                        <span>My Account</span>
                        <span class="nav-dropdown-arrow">⌄</span>
                    </button>

                    <div class="nav-profile-dropdown">
                        <a href="/AutoCare-Core-PHP/customer/profile.php">My Profile</a>
                        <a href="/AutoCare-Core-PHP/customer/vehicles.php">My Vehicles</a>
                        <a href="/AutoCare-Core-PHP/customer/my-bookings.php">My Bookings</a>
                        <div class="nav-dropdown-divider"></div>
                        <a href="/AutoCare-Core-PHP/logout.php" class="nav-logout-link">Logout</a>
                    </div>
                </div>

            <?php } elseif (isset($_SESSION["user_id"]) && $_SESSION["role"] === "admin") { ?>

                <a href="/AutoCare-Core-PHP/admin/dashboard.php" class="nav-dashboard-btn">Admin Dashboard</a>

                <div class="nav-profile-menu">
                    <button type="button" class="nav-profile-trigger">
                        <span class="nav-user-icon">⚙️</span>
                        <span>Admin Panel</span>
                        <span class="nav-dropdown-arrow">⌄</span>
                    </button>

                    <div class="nav-profile-dropdown">
                        <a href="/AutoCare-Core-PHP/admin/services.php">Manage Services</a>
                        <a href="/AutoCare-Core-PHP/admin/bookings.php">Manage Bookings</a>
                        <a href="/AutoCare-Core-PHP/admin/customers.php">Manage Customers</a>
                        <div class="nav-dropdown-divider"></div>
                        <a href="/AutoCare-Core-PHP/logout.php" class="nav-logout-link">Logout</a>
                    </div>
                </div>

            <?php } else { ?>

                <a href="/AutoCare-Core-PHP/login.php" class="nav-login-btn">Login</a>
                <a href="/AutoCare-Core-PHP/register.php" class="nav-register-btn">Register</a>

            <?php } ?>

        </div>

    </div>
</header>

<main>
