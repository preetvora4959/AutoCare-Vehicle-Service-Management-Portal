
<?php
include "../includes/functions.php";
admin_only();
include "../db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

$q = mysqli_prepare($conn, "SELECT id,name,email,phone FROM users WHERE id=? AND role='customer'");
mysqli_stmt_bind_param($q, "i", $id);
mysqli_stmt_execute($q);

$u = mysqli_fetch_assoc(mysqli_stmt_get_result($q));

if (!$u) {
    header("Location: customers.php");
    exit();
}

$q = mysqli_prepare($conn, "SELECT * FROM vehicles WHERE user_id=? ORDER BY id DESC");
mysqli_stmt_bind_param($q, "i", $id);
mysqli_stmt_execute($q);

$vehicles = mysqli_stmt_get_result($q);

$q = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM bookings WHERE user_id=?");
mysqli_stmt_bind_param($q, "i", $id);
mysqli_stmt_execute($q);

$booking_count = mysqli_fetch_assoc(mysqli_stmt_get_result($q))["total"];

include "../includes/header.php";
?>

<section class="admin-page">
    <div class="container">

        <div class="admin-page-banner">
            <div>
                <p class="tag">CUSTOMER MANAGEMENT</p>
                <h1>Customer Details</h1>
                <p>View customer information and registered vehicles.</p>
            </div>
            <div class="admin-welcome-icon">👤</div>
        </div>

        <div class="customer-detail-layout">

            <div class="customer-detail-card">

                <div class="admin-customer-avatar detail-avatar">👤</div>

                <h2><?php echo clean($u["name"]); ?></h2>

                <span class="admin-customer-label">Registered Customer</span>

                <div class="customer-detail-info">
                    <div>
                        <small>Email Address</small>
                        <strong><?php echo clean($u["email"]); ?></strong>
                    </div>

                    <div>
                        <small>Phone Number</small>
                        <strong><?php echo clean($u["phone"]); ?></strong>
                    </div>

                    <div>
                        <small>Total Bookings</small>
                        <strong><?php echo (int)$booking_count; ?></strong>
                    </div>
                </div>

                <a href="customer-edit.php?id=<?php echo (int)$u["id"]; ?>" class="admin-submit-btn customer-detail-edit">Edit Customer →</a>

            </div>

            <div class="customer-vehicles-panel">

                <div class="admin-section-heading">
                    <div>
                        <p class="tag">CUSTOMER GARAGE</p>
                        <h2>Registered Vehicles</h2>
                    </div>
                    <span class="admin-count"><?php echo mysqli_num_rows($vehicles); ?> Vehicles</span>
                </div>

                <?php if (mysqli_num_rows($vehicles) > 0) { ?>

                    <div class="admin-vehicle-list">

                        <?php while ($v = mysqli_fetch_assoc($vehicles)) { ?>

                            <article class="admin-vehicle-card">
                                <div class="admin-action-icon">🚘</div>
                                <div>
                                    <h3><?php echo clean($v["brand"] . " " . $v["model"]); ?></h3>
                                    <p><?php echo clean($v["registration_number"]); ?></p>
                                    <small><?php echo clean($v["vehicle_type"]); ?> · <?php echo (int)$v["manufacturing_year"]; ?></small>
                                </div>
                            </article>

                        <?php } ?>

                    </div>

                <?php } else { ?>

                    <div class="admin-empty-state">
                        <div>🚗</div>
                        <h3>No Vehicles Registered</h3>
                        <p>This customer has not added any vehicles yet.</p>
                    </div>

                <?php } ?>

            </div>

        </div>

        <a href="customers.php" class="admin-back-link">← Back to Customers</a>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
