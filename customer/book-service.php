
<?php
include "../includes/functions.php";
customer_only();
include "../db.php";

$uid = (int)$_SESSION["user_id"];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vid = (int)$_POST["vehicle_id"];
    $sid = (int)$_POST["service_id"];
    $date = $_POST["booking_date"];
    $notes = trim($_POST["notes"]);

    $q = mysqli_prepare($conn, "SELECT id FROM vehicles WHERE id=? AND user_id=?");
    mysqli_stmt_bind_param($q, "ii", $vid, $uid);
    mysqli_stmt_execute($q);

    $valid = mysqli_num_rows(mysqli_stmt_get_result($q));

    $service_query = mysqli_prepare($conn, "SELECT id FROM services WHERE id=? AND status='Active'");
    mysqli_stmt_bind_param($service_query, "i", $sid);
    mysqli_stmt_execute($service_query);

    $valid_service = mysqli_num_rows(mysqli_stmt_get_result($service_query));

    if (!$valid) {
        $error = "Select one of your vehicles.";
    } elseif (!$valid_service) {
        $error = "Select a valid active service.";
    } elseif ($date < date("Y-m-d")) {
        $error = "Date cannot be in the past.";
    } else {
        $q = mysqli_prepare($conn, "INSERT INTO bookings(user_id,vehicle_id,service_id,booking_date,notes) VALUES(?,?,?,?,?)");
        mysqli_stmt_bind_param($q, "iiiss", $uid, $vid, $sid, $date, $notes);

        if (mysqli_stmt_execute($q)) {
            header("Location: my-bookings.php");
            exit();
        } else {
            $error = "Booking failed. Please try again.";
        }
    }
}

$vq = mysqli_prepare($conn, "SELECT * FROM vehicles WHERE user_id=?");
mysqli_stmt_bind_param($vq, "i", $uid);
mysqli_stmt_execute($vq);

$vehicles = mysqli_stmt_get_result($vq);

$services = mysqli_query($conn, "SELECT * FROM services WHERE status='Active'");

$vehicle_count = mysqli_num_rows($vehicles);
$service_count = $services ? mysqli_num_rows($services) : 0;

include "../includes/header.php";
?>

<section class="portal-page">
    <div class="container">

        <div class="portal-page-hero">
            <div>
                <p class="tag">CUSTOMER PORTAL</p>
                <h1>Book a Service</h1>
                <p>Schedule your vehicle service with just a few simple steps.</p>
            </div>
            <div class="portal-hero-icon">🔧</div>
        </div>

        <div class="booking-form-layout">

            <div class="portal-form-card booking-form-card">

                <div class="portal-card-heading">
                    <div class="portal-heading-icon">📅</div>
                    <div>
                        <h2>Service Details</h2>
                        <p>Fill in the information to schedule your booking.</p>
                    </div>
                </div>

                <?php if ($error) { ?>
                    <div class="login-error"><?php echo clean($error); ?></div>
                <?php } ?>

                <?php if ($vehicle_count > 0 && $service_count > 0) { ?>

                    <form method="POST" class="portal-form">

                        <div class="portal-field">
                            <label>Select Your Vehicle</label>
                            <select name="vehicle_id" required>
                                <option value="">Choose your vehicle</option>

                                <?php while ($v = mysqli_fetch_assoc($vehicles)) { ?>
                                    <option value="<?php echo (int)$v["id"]; ?>" <?php echo isset($vid) && $vid === (int)$v["id"] ? "selected" : ""; ?>>
                                        <?php echo clean($v["brand"] . " " . $v["model"] . " - " . $v["registration_number"]); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="portal-field">
                            <label>Select Service</label>
                            <select name="service_id" required>
                                <option value="">Choose a service</option>

                                <?php while ($s = mysqli_fetch_assoc($services)) { ?>
                                    <option value="<?php echo (int)$s["id"]; ?>" <?php echo isset($sid) && $sid === (int)$s["id"] ? "selected" : ""; ?>>
                                        <?php echo clean($s["name"]); ?> - ₹<?php echo number_format((float)$s["price"], 2); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div class="portal-field">
                            <label>Preferred Booking Date</label>
                            <input type="date" name="booking_date" min="<?php echo date("Y-m-d"); ?>" value="<?php echo isset($date) ? clean($date) : ""; ?>" required>
                        </div>

                        <div class="portal-field">
                            <label>Additional Notes</label>
                            <textarea name="notes" rows="4" placeholder="Describe any specific vehicle issue or service requirements"><?php echo isset($notes) ? clean($notes) : ""; ?></textarea>
                        </div>

                        <button type="submit" class="portal-submit-btn">Confirm Service Booking →</button>

                    </form>

                <?php } else { ?>

                    <div class="portal-empty-state">

                        <div>🚘</div>

                        <?php if ($vehicle_count === 0) { ?>
                            <h3>Register Your Vehicle First</h3>
                            <p>You need to add a vehicle before booking a service.</p>
                            <a href="vehicles.php" class="btn">Add My Vehicle</a>
                        <?php } else { ?>
                            <h3>No Services Available</h3>
                            <p>There are currently no active services available for booking.</p>
                            <a href="dashboard.php" class="btn">Back to Dashboard</a>
                        <?php } ?>

                    </div>

                <?php } ?>

            </div>

            <aside class="booking-info-panel">

                <div class="booking-info-icon">🛠️</div>
                <p class="tag">SIMPLE & CONVENIENT</p>
                <h2>Book Your Vehicle Service</h2>
                <p>Choose your vehicle, select the required service and pick a convenient date.</p>

                <div class="booking-step">
                    <span>01</span>
                    <div>
                        <strong>Select Vehicle</strong>
                        <small>Choose from your registered vehicles.</small>
                    </div>
                </div>

                <div class="booking-step">
                    <span>02</span>
                    <div>
                        <strong>Choose Service</strong>
                        <small>Select the service your vehicle needs.</small>
                    </div>
                </div>

                <div class="booking-step">
                    <span>03</span>
                    <div>
                        <strong>Pick a Date</strong>
                        <small>Submit your preferred booking date.</small>
                    </div>
                </div>

                <div class="booking-info-footer">
                    <span>✓</span>
                    Your booking will be submitted for admin review.
                </div>

            </aside>

        </div>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
