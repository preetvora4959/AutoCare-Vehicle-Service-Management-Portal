
<?php
include "../includes/functions.php";
customer_only();
include "../db.php";

$uid = (int)$_SESSION["user_id"];

$vehicle_query = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM vehicles WHERE user_id=?");
mysqli_stmt_bind_param($vehicle_query, "i", $uid);
mysqli_stmt_execute($vehicle_query);
$vehicle_count = mysqli_fetch_assoc(mysqli_stmt_get_result($vehicle_query))["total"];

$booking_query = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=?");
mysqli_stmt_bind_param($booking_query, "i", $uid);
mysqli_stmt_execute($booking_query);
$booking_count = mysqli_fetch_assoc(mysqli_stmt_get_result($booking_query))["total"];

$pending_query = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=? AND status='Pending'");
mysqli_stmt_bind_param($pending_query, "i", $uid);
mysqli_stmt_execute($pending_query);
$pending_count = mysqli_fetch_assoc(mysqli_stmt_get_result($pending_query))["total"];

$recent_query = mysqli_prepare($conn, "SELECT b.*, s.name AS service_name, v.brand, v.model, v.registration_number FROM bookings b JOIN services s ON b.service_id=s.id JOIN vehicles v ON b.vehicle_id=v.id WHERE b.user_id=? ORDER BY b.id DESC LIMIT 3");
mysqli_stmt_bind_param($recent_query, "i", $uid);
mysqli_stmt_execute($recent_query);
$recent_bookings = mysqli_stmt_get_result($recent_query);

include "../includes/header.php";
?>

<section class="customer-dashboard">
    <div class="container">

        <div class="customer-welcome">
            <div>
                <p class="tag">CUSTOMER PORTAL</p>
                <h1>Welcome back, <?php echo clean($_SESSION["user_name"]); ?>!</h1>
                <p>Manage your vehicles, book services and keep track of your vehicle care.</p>
            </div>

            <div class="dashboard-welcome-icon">🚘</div>
        </div>

        <div class="customer-stats">

            <article class="customer-stat-card">
                <div class="customer-stat-icon">🚗</div>
                <div>
                    <p>My Vehicles</p>
                    <h2><?php echo (int)$vehicle_count; ?></h2>
                    <small>Registered vehicles</small>
                </div>
            </article>

            <article class="customer-stat-card">
                <div class="customer-stat-icon">📋</div>
                <div>
                    <p>Total Bookings</p>
                    <h2><?php echo (int)$booking_count; ?></h2>
                    <small>All service requests</small>
                </div>
            </article>

            <article class="customer-stat-card">
                <div class="customer-stat-icon">⏳</div>
                <div>
                    <p>Pending Bookings</p>
                    <h2><?php echo (int)$pending_count; ?></h2>
                    <small>Awaiting confirmation</small>
                </div>
            </article>

        </div>

        <div class="customer-dashboard-heading">
            <div>
                <p class="tag">QUICK ACCESS</p>
                <h2>Manage Your Account</h2>
                <p>Everything you need to manage your vehicle services.</p>
            </div>
        </div>

        <div class="customer-action-grid">

            <a href="vehicles.php" class="customer-action-card">
                <div class="customer-action-icon">🚘</div>
                <h3>My Vehicles</h3>
                <p>Add, update and manage your registered vehicles.</p>
                <span>Manage Vehicles →</span>
            </a>

            <a href="book-service.php" class="customer-action-card">
                <div class="customer-action-icon">🔧</div>
                <h3>Book a Service</h3>
                <p>Choose your vehicle and schedule a service.</p>
                <span>Book Now →</span>
            </a>

            <a href="my-bookings.php" class="customer-action-card">
                <div class="customer-action-icon">📅</div>
                <h3>My Bookings</h3>
                <p>View your service bookings and their current status.</p>
                <span>View Bookings →</span>
            </a>

        </div>

        <div class="customer-recent-section">

            <div class="customer-dashboard-heading">
                <div>
                    <p class="tag">RECENT ACTIVITY</p>
                    <h2>Recent Bookings</h2>
                </div>

                <a href="my-bookings.php" class="dashboard-view-all">View All →</a>
            </div>

            <?php if (mysqli_num_rows($recent_bookings) > 0) { ?>

                <div class="customer-recent-list">

                    <?php while ($b = mysqli_fetch_assoc($recent_bookings)) { ?>

                        <article class="customer-recent-card">
                            <div class="recent-service-icon">🔧</div>

                            <div class="recent-service-info">
                                <h3><?php echo clean($b["service_name"]); ?></h3>
                                <p><?php echo clean($b["brand"] . " " . $b["model"]); ?></p>
                                <small><?php echo clean($b["registration_number"]); ?></small>
                            </div>

                            <div class="recent-booking-details">
                                <span><?php echo clean($b["booking_date"]); ?></span>
                                <strong class="booking-status"><?php echo clean($b["status"]); ?></strong>
                            </div>
                        </article>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <div class="customer-empty-state">
                    <div>📅</div>
                    <h3>No Bookings Yet</h3>
                    <p>Your recent service bookings will appear here.</p>
                    <a href="book-service.php" class="btn">Book Your First Service</a>
                </div>

            <?php } ?>

        </div>

        <div class="customer-dashboard-cta">
            <div>
                <p class="tag">YOUR VEHICLE DESERVES THE BEST</p>
                <h2>Keep Your Vehicle Running Smoothly.</h2>
                <p>Schedule your next vehicle service with AutoCare.</p>
            </div>

            <a href="book-service.php" class="btn light">Book a Service →</a>
        </div>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
