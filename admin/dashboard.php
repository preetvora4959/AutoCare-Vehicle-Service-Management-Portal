
<?php
include "../includes/functions.php";
admin_only();
include "../db.php";

$services = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM services"))["total"];
$customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='customer'"))["total"];
$bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings"))["total"];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookings WHERE status='Pending'"))["total"];

$recent = mysqli_query($conn, "SELECT b.id,b.booking_date,b.status,u.name,s.name AS service_name FROM bookings b JOIN users u ON b.user_id=u.id JOIN services s ON b.service_id=s.id ORDER BY b.id DESC LIMIT 5");

include "../includes/header.php";
?>

<section class="admin-page">
    <div class="container">

        <div class="admin-welcome">
            <div>
                <p class="tag">ADMINISTRATION PORTAL</p>
                <h1>Welcome back, <?php echo clean($_SESSION["user_name"]); ?>!</h1>
                <p>Manage your AutoCare operations from one central dashboard.</p>
            </div>
            <div class="admin-welcome-icon">⚙️</div>
        </div>

        <div class="admin-stats-grid">

            <article class="admin-stat-card">
                <div class="admin-stat-icon">🔧</div>
                <div>
                    <p>Total Services</p>
                    <h2><?php echo (int)$services; ?></h2>
                    <small>Available service records</small>
                </div>
            </article>

            <article class="admin-stat-card">
                <div class="admin-stat-icon">👥</div>
                <div>
                    <p>Total Customers</p>
                    <h2><?php echo (int)$customers; ?></h2>
                    <small>Registered customers</small>
                </div>
            </article>

            <article class="admin-stat-card">
                <div class="admin-stat-icon">📋</div>
                <div>
                    <p>Total Bookings</p>
                    <h2><?php echo (int)$bookings; ?></h2>
                    <small>All service requests</small>
                </div>
            </article>

            <article class="admin-stat-card">
                <div class="admin-stat-icon">⏳</div>
                <div>
                    <p>Pending Bookings</p>
                    <h2><?php echo (int)$pending; ?></h2>
                    <small>Awaiting action</small>
                </div>
            </article>

        </div>

        <div class="admin-section-heading">
            <div>
                <p class="tag">ADMIN TOOLS</p>
                <h2>Manage Your Portal</h2>
                <p>Quick access to important administration tasks.</p>
            </div>
        </div>

        <div class="admin-action-grid">

            <a href="services.php" class="admin-action-card">
                <div class="admin-action-icon">🔧</div>
                <h3>Manage Services</h3>
                <p>Add, update and remove vehicle services.</p>
                <span>Manage Services →</span>
            </a>

            <a href="bookings.php" class="admin-action-card">
                <div class="admin-action-icon">📅</div>
                <h3>Manage Bookings</h3>
                <p>Review bookings and update service status.</p>
                <span>Manage Bookings →</span>
            </a>

            <a href="customers.php" class="admin-action-card">
                <div class="admin-action-icon">👥</div>
                <h3>Manage Customers</h3>
                <p>Search customer records and view their details.</p>
                <span>Manage Customers →</span>
            </a>

        </div>

        <div class="admin-recent-section">

            <div class="admin-section-heading">
                <div>
                    <p class="tag">LATEST ACTIVITY</p>
                    <h2>Recent Bookings</h2>
                </div>
                <a href="bookings.php" class="admin-view-all">View All →</a>
            </div>

            <?php if (mysqli_num_rows($recent) > 0) { ?>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Booking Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($b = mysqli_fetch_assoc($recent)) { ?>
                                <tr>
                                    <td><?php echo clean($b["name"]); ?></td>
                                    <td><?php echo clean($b["service_name"]); ?></td>
                                    <td><?php echo clean($b["booking_date"]); ?></td>
                                    <td>
                                        <span class="admin-status status-<?php echo strtolower(str_replace(" ", "-", $b["status"])); ?>">
                                            <?php echo clean($b["status"]); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            <?php } else { ?>

                <div class="admin-empty-state">
                    <div>📋</div>
                    <h3>No Bookings Yet</h3>
                    <p>New customer bookings will appear here.</p>
                </div>

            <?php } ?>

        </div>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
