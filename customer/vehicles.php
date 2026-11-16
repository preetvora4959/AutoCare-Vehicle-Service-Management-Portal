
<?php
include "../includes/functions.php";
customer_only();
include "../db.php";

$uid = (int)$_SESSION["user_id"];
$msg = "";

if (isset($_POST["add"]) || isset($_POST["update"])) {
    $type = trim($_POST["vehicle_type"]);
    $brand = trim($_POST["brand"]);
    $model = trim($_POST["model"]);
    $reg = strtoupper(trim($_POST["registration_number"]));
    $year = (int)$_POST["manufacturing_year"];

    if (isset($_POST["add"])) {
        $q = mysqli_prepare($conn, "INSERT INTO vehicles(user_id,vehicle_type,brand,model,registration_number,manufacturing_year) VALUES(?,?,?,?,?,?)");
        mysqli_stmt_bind_param($q, "issssi", $uid, $type, $brand, $model, $reg, $year);
    } else {
        $id = (int)$_POST["id"];
        $q = mysqli_prepare($conn, "UPDATE vehicles SET vehicle_type=?,brand=?,model=?,registration_number=?,manufacturing_year=? WHERE id=? AND user_id=?");
        mysqli_stmt_bind_param($q, "ssssiii", $type, $brand, $model, $reg, $year, $id, $uid);
    }

    $msg = mysqli_stmt_execute($q) ? "Vehicle saved successfully." : "Unable to save vehicle. Check registration number.";
}

if (isset($_POST["delete"])) {
    $id = (int)$_POST["id"];

    $q = mysqli_prepare($conn, "DELETE FROM vehicles WHERE id=? AND user_id=?");
    mysqli_stmt_bind_param($q, "ii", $id, $uid);
    mysqli_stmt_execute($q);

    $msg = "Vehicle deleted.";
}

$edit = null;

if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];

    $q = mysqli_prepare($conn, "SELECT * FROM vehicles WHERE id=? AND user_id=?");
    mysqli_stmt_bind_param($q, "ii", $id, $uid);
    mysqli_stmt_execute($q);

    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
}

$q = mysqli_prepare($conn, "SELECT * FROM vehicles WHERE user_id=? ORDER BY id DESC");
mysqli_stmt_bind_param($q, "i", $uid);
mysqli_stmt_execute($q);
$rows = mysqli_stmt_get_result($q);

include "../includes/header.php";
?>

<section class="portal-page">
    <div class="container">

        <div class="portal-page-hero">
            <div>
                <p class="tag">CUSTOMER PORTAL</p>
                <h1>My Vehicles</h1>
                <p>Manage your registered vehicles and keep their details updated.</p>
            </div>
            <div class="portal-hero-icon">🚘</div>
        </div>

        <?php if ($msg !== "") { ?>
            <div class="portal-message"><?php echo clean($msg); ?></div>
        <?php } ?>

        <div class="vehicle-layout">

            <div class="portal-form-card">
                <div class="portal-card-heading">
                    <div class="portal-heading-icon">🚗</div>
                    <div>
                        <h2><?php echo $edit ? "Edit Vehicle" : "Add New Vehicle"; ?></h2>
                        <p><?php echo $edit ? "Update your vehicle information." : "Enter your vehicle details below."; ?></p>
                    </div>
                </div>

                <form method="POST" class="portal-form">

                    <?php if ($edit) { ?>
                        <input type="hidden" name="id" value="<?php echo (int)$edit["id"]; ?>">
                    <?php } ?>

                    <div class="portal-field">
                        <label>Vehicle Type</label>
                        <input type="text" name="vehicle_type" placeholder="Example: Car, Bike" value="<?php echo $edit ? clean($edit["vehicle_type"]) : ""; ?>" required>
                    </div>

                    <div class="portal-field">
                        <label>Vehicle Brand</label>
                        <input type="text" name="brand" placeholder="Enter vehicle brand" value="<?php echo $edit ? clean($edit["brand"]) : ""; ?>" required>
                    </div>

                    <div class="portal-field">
                        <label>Vehicle Model</label>
                        <input type="text" name="model" placeholder="Enter vehicle model" value="<?php echo $edit ? clean($edit["model"]) : ""; ?>" required>
                    </div>

                    <div class="portal-field">
                        <label>Registration Number</label>
                        <input type="text" name="registration_number" placeholder="Example: GJ10AB1234" value="<?php echo $edit ? clean($edit["registration_number"]) : ""; ?>" required>
                    </div>

                    <div class="portal-field">
                        <label>Manufacturing Year</label>
                        <input type="number" name="manufacturing_year" min="1980" max="<?php echo date("Y"); ?>" placeholder="Enter manufacturing year" value="<?php echo $edit ? (int)$edit["manufacturing_year"] : ""; ?>" required>
                    </div>

                    <button type="submit" class="portal-submit-btn" name="<?php echo $edit ? "update" : "add"; ?>">
                        <?php echo $edit ? "Update Vehicle" : "Save Vehicle"; ?> →
                    </button>

                    <?php if ($edit) { ?>
                        <a href="vehicles.php" class="portal-cancel-link">Cancel Editing</a>
                    <?php } ?>

                </form>
            </div>

            <div class="vehicle-list-area">

                <div class="portal-section-heading">
                    <div>
                        <p class="tag">YOUR GARAGE</p>
                        <h2>Registered Vehicles</h2>
                    </div>
                    <span class="portal-count"><?php echo mysqli_num_rows($rows); ?> Vehicles</span>
                </div>

                <?php if (mysqli_num_rows($rows) > 0) { ?>

                    <div class="vehicle-card-list">

                        <?php while ($v = mysqli_fetch_assoc($rows)) { ?>

                            <article class="vehicle-card">

                                <div class="vehicle-card-top">
                                    <div class="vehicle-card-icon">🚘</div>
                                    <span class="vehicle-type-badge"><?php echo clean($v["vehicle_type"]); ?></span>
                                </div>

                                <h3><?php echo clean($v["brand"] . " " . $v["model"]); ?></h3>

                                <div class="vehicle-info-grid">
                                    <div>
                                        <small>Registration Number</small>
                                        <strong><?php echo clean($v["registration_number"]); ?></strong>
                                    </div>
                                    <div>
                                        <small>Manufacturing Year</small>
                                        <strong><?php echo (int)$v["manufacturing_year"]; ?></strong>
                                    </div>
                                </div>

                                <div class="vehicle-card-actions">
                                    <a href="vehicles.php?edit=<?php echo (int)$v["id"]; ?>" class="vehicle-edit-btn">✎ Edit</a>

                                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                                        <input type="hidden" name="id" value="<?php echo (int)$v["id"]; ?>">
                                        <button type="submit" name="delete" class="vehicle-delete-btn">Delete</button>
                                    </form>
                                </div>

                            </article>

                        <?php } ?>

                    </div>

                <?php } else { ?>

                    <div class="portal-empty-state">
                        <div>🚗</div>
                        <h3>No Vehicles Registered</h3>
                        <p>Add your first vehicle using the form to get started.</p>
                    </div>

                <?php } ?>

            </div>

        </div>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
