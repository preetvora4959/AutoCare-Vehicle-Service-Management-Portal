
<?php
include "../includes/functions.php";
admin_only();
include "../db.php";

$msg = "";

if (isset($_POST["delete_booking"])) {
    $id = (int)$_POST["id"];

    $q = mysqli_prepare($conn, "DELETE FROM bookings WHERE id=?");
    mysqli_stmt_bind_param($q, "i", $id);

    if (mysqli_stmt_execute($q) && mysqli_stmt_affected_rows($q) > 0) {
        $msg = "Booking deleted successfully.";
    } else {
        $msg = "Unable to delete booking.";
    }
}

if (isset($_POST["update_status"])) {
    $id = (int)$_POST["id"];
    $status = $_POST["status"];

    $allowed = array("Pending", "Confirmed", "In Progress", "Completed", "Cancelled");

    if (in_array($status, $allowed, true)) {
        $q = mysqli_prepare($conn, "UPDATE bookings SET status=? WHERE id=?");
        mysqli_stmt_bind_param($q, "si", $status, $id);

        $msg = mysqli_stmt_execute($q) ? "Booking status updated successfully." : "Unable to update booking status.";
    } else {
        $msg = "Invalid booking status.";
    }
}

$rows = mysqli_query($conn, "SELECT b.*,u.name,v.brand,v.model,v.registration_number,s.name AS service_name FROM bookings b JOIN users u ON b.user_id=u.id JOIN vehicles v ON b.vehicle_id=v.id JOIN services s ON b.service_id=s.id ORDER BY b.id DESC");

$total = mysqli_num_rows($rows);

include "../includes/header.php";
?>

<section class="admin-page">
    <div class="container">

        <div class="admin-page-banner">
            <div>
                <p class="tag">ADMINISTRATION</p>
                <h1>Manage Bookings</h1>
                <p>Review service requests and manage their progress.</p>
            </div>
            <div class="admin-welcome-icon">📅</div>
        </div>

        <?php if ($msg !== "") { ?>
            <div class="admin-message"><?php echo clean($msg); ?></div>
        <?php } ?>

        <div class="admin-section-heading">
            <div>
                <p class="tag">SERVICE MANAGEMENT</p>
                <h2>All Service Bookings</h2>
            </div>
            <span class="admin-count"><?php echo $total; ?> Bookings</span>
        </div>

        <?php if ($total > 0) { ?>

            <div class="admin-booking-list">

                <?php while ($b = mysqli_fetch_assoc($rows)) { ?>

                    <article class="admin-booking-card">

                        <div class="admin-booking-header">

                            <div class="admin-booking-icon">🔧</div>

                            <div class="admin-booking-title">
                                <h3><?php echo clean($b["service_name"]); ?></h3>
                                <p>Booking #<?php echo (int)$b["id"]; ?></p>
                            </div>

                            <span class="admin-status status-<?php echo strtolower(str_replace(" ", "-", $b["status"])); ?>">
                                <?php echo clean($b["status"]); ?>
                            </span>

                        </div>

                        <div class="admin-booking-details">

                            <div>
                                <small>Customer</small>
                                <strong><?php echo clean($b["name"]); ?></strong>
                            </div>

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
                            <div class="admin-booking-notes">
                                <strong>Customer Notes:</strong>
                                <p><?php echo clean($b["notes"]); ?></p>
                            </div>
                        <?php } ?>

                        <form method="POST" class="admin-status-form">

                            <input type="hidden" name="id" value="<?php echo (int)$b["id"]; ?>">

                            <div>
                                <label>Update Booking Status</label>
                                <select name="status">
                                    <?php
                                    $statuses = array("Pending", "Confirmed", "In Progress", "Completed", "Cancelled");
                                    foreach ($statuses as $status_option) {
                                    ?>
                                        <option value="<?php echo $status_option; ?>" <?php echo $b["status"] === $status_option ? "selected" : ""; ?>>
                                            <?php echo $status_option; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <button type="submit" name="update_status" class="admin-submit-btn">Update Status →</button>

                        </form>

                        <form method="POST" class="admin-booking-delete-form" onsubmit="return confirm('Are you sure you want to permanently delete this booking?');">
                            <input type="hidden" name="id" value="<?php echo (int)$b["id"]; ?>">
                            <button type="submit" name="delete_booking" class="admin-booking-delete-btn">Delete Booking</button>
                        </form>

                    </article>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="admin-empty-state">
                <div>📅</div>
                <h3>No Bookings Found</h3>
                <p>Customer service bookings will appear here.</p>
            </div>

        <?php } ?>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
