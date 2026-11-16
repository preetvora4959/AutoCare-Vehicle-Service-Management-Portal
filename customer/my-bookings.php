
<?php
include "../includes/functions.php";
customer_only();
include "../db.php";

$uid = (int)$_SESSION["user_id"];
$msg = "";

if (isset($_POST["delete_booking"])) {
    $id = (int)$_POST["id"];

    $q = mysqli_prepare($conn, "DELETE FROM bookings WHERE id=? AND user_id=?");
    mysqli_stmt_bind_param($q, "ii", $id, $uid);

    if (mysqli_stmt_execute($q) && mysqli_stmt_affected_rows($q) > 0) {
        $msg = "Booking deleted successfully.";
    } else {
        $msg = "Unable to delete booking.";
    }
}

$q = mysqli_prepare($conn, "SELECT b.*,s.name AS service_name,v.brand,v.model,v.registration_number FROM bookings b JOIN services s ON b.service_id=s.id JOIN vehicles v ON b.vehicle_id=v.id WHERE b.user_id=? ORDER BY b.id DESC");

mysqli_stmt_bind_param($q, "i", $uid);
mysqli_stmt_execute($q);

$rows = mysqli_stmt_get_result($q);
$total_bookings = mysqli_num_rows($rows);

include "../includes/header.php";
?>

<section class="portal-page">
    <div class="container">

        <div class="portal-page-hero">
            <div>
                <p class="tag">CUSTOMER PORTAL</p>
                <h1>My Bookings</h1>
                <p>View your service requests and track their booking status.</p>
            </div>
            <div class="portal-hero-icon">📋</div>
        </div>

        <?php if ($msg !== "") { ?>
            <div class="admin-message"><?php echo clean($msg); ?></div>
        <?php } ?>

        <div class="booking-page-heading">
            <div>
                <p class="tag">SERVICE HISTORY</p>
                <h2>Your Service Bookings</h2>
            </div>
            <div class="portal-count"><?php echo $total_bookings; ?> Bookings</div>
        </div>

        <?php if ($total_bookings > 0) { ?>

            <div class="booking-list">

                <?php while ($b = mysqli_fetch_assoc($rows)) { ?>

                    <article class="booking-card">

                        <div class="booking-card-header">
                            <div class="booking-service-icon">🔧</div>

                            <div class="booking-service-title">
                                <h3><?php echo clean($b["service_name"]); ?></h3>
                                <p>Service Booking #<?php echo (int)$b["id"]; ?></p>
                            </div>

                            <span class="booking-status-badge status-<?php echo strtolower(preg_replace("/[^a-zA-Z0-9]+/", "-", $b["status"])); ?>">
                                <?php echo clean($b["status"]); ?>
                            </span>
                        </div>

                        <div class="booking-card-details">

                            <div>
                                <small>Vehicle</small>
                                <strong><?php echo clean($b["brand"] . " " . $b["model"]); ?></strong>
                            </div>

                            <div>
                                <small>Registration Number</small>
                                <strong><?php echo clean($b["registration_number"]); ?></strong>
                            </div>

                            <div>
                                <small>Booking Date</small>
                                <strong><?php echo clean($b["booking_date"]); ?></strong>
                            </div>

                        </div>

                        <?php if (!empty($b["notes"])) { ?>
                            <div class="booking-notes">
                                <strong>Additional Notes:</strong>
                                <p><?php echo clean($b["notes"]); ?></p>
                            </div>
                        <?php } ?>

                        <form method="POST" class="booking-delete-form" onsubmit="return confirm('Are you sure you want to delete this booking? This action cannot be undone.');">
                            <input type="hidden" name="id" value="<?php echo (int)$b["id"]; ?>">
                            <button type="submit" name="delete_booking" class="booking-delete-btn">Delete Booking</button>
                        </form>

                    </article>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="portal-empty-state booking-empty-state">
                <div>📅</div>
                <h3>No Bookings Found</h3>
                <p>You haven't booked any vehicle services yet.</p>
                <a href="book-service.php" class="btn">Book Your First Service</a>
            </div>

        <?php } ?>

        <div class="booking-bottom-cta">
            <div>
                <h2>Need Another Service?</h2>
                <p>Schedule your next vehicle maintenance with AutoCare.</p>
            </div>
            <a href="book-service.php" class="btn light">Book a Service →</a>
        </div>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
