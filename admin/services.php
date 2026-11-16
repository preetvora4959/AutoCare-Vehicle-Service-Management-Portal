
<?php
include "../includes/functions.php";
admin_only();
include "../db.php";

$msg = "";

if (isset($_POST["add"]) || isset($_POST["update"])) {
    $name = trim($_POST["name"]);
    $desc = trim($_POST["description"]);
    $price = (float)$_POST["price"];
    $duration = (int)$_POST["duration"];

    if ($name === "" || $desc === "" || $price < 0 || $duration < 1) {
        $msg = "Please enter valid service details.";
    } else {
        if (isset($_POST["add"])) {
            $q = mysqli_prepare($conn, "INSERT INTO services(name,description,price,duration) VALUES(?,?,?,?)");
            mysqli_stmt_bind_param($q, "ssdi", $name, $desc, $price, $duration);
        } else {
            $id = (int)$_POST["id"];
            $q = mysqli_prepare($conn, "UPDATE services SET name=?,description=?,price=?,duration=? WHERE id=?");
            mysqli_stmt_bind_param($q, "ssdii", $name, $desc, $price, $duration, $id);
        }

        $msg = mysqli_stmt_execute($q) ? "Service saved successfully." : "Unable to save service.";
    }
}

if (isset($_POST["delete"])) {
    $id = (int)$_POST["id"];
    $q = mysqli_prepare($conn, "DELETE FROM services WHERE id=?");
    mysqli_stmt_bind_param($q, "i", $id);

    $msg = mysqli_stmt_execute($q) ? "Service deleted successfully." : "Unable to delete service. It may be linked to existing bookings.";
}

$edit = null;

if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $q = mysqli_prepare($conn, "SELECT * FROM services WHERE id=?");
    mysqli_stmt_bind_param($q, "i", $id);
    mysqli_stmt_execute($q);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
}

$rows = mysqli_query($conn, "SELECT * FROM services ORDER BY id DESC");

include "../includes/header.php";
?>

<section class="admin-page">
    <div class="container">

        <div class="admin-page-banner">
            <div>
                <p class="tag">ADMINISTRATION</p>
                <h1>Manage Services</h1>
                <p>Create and maintain the services offered by AutoCare.</p>
            </div>
            <div class="admin-welcome-icon">🔧</div>
        </div>

        <?php if ($msg !== "") { ?>
            <div class="admin-message"><?php echo clean($msg); ?></div>
        <?php } ?>

        <div class="admin-management-layout">

            <div class="admin-form-card">
                <div class="admin-card-heading">
                    <div class="admin-action-icon">🛠️</div>
                    <div>
                        <h2><?php echo $edit ? "Edit Service" : "Add New Service"; ?></h2>
                        <p>Enter the service information below.</p>
                    </div>
                </div>

                <form method="POST" class="admin-form">

                    <?php if ($edit) { ?>
                        <input type="hidden" name="id" value="<?php echo (int)$edit["id"]; ?>">
                    <?php } ?>

                    <div class="admin-field">
                        <label>Service Name</label>
                        <input name="name" placeholder="Enter service name" value="<?php echo $edit ? clean($edit["name"]) : ""; ?>" required>
                    </div>

                    <div class="admin-field">
                        <label>Description</label>
                        <textarea name="description" rows="4" placeholder="Describe the service" required><?php echo $edit ? clean($edit["description"]) : ""; ?></textarea>
                    </div>

                    <div class="admin-field">
                        <label>Price (₹)</label>
                        <input type="number" step="0.01" min="0" name="price" placeholder="Enter price" value="<?php echo $edit ? clean($edit["price"]) : ""; ?>" required>
                    </div>

                    <div class="admin-field">
                        <label>Duration (Minutes)</label>
                        <input type="number" min="1" name="duration" placeholder="Enter duration" value="<?php echo $edit ? (int)$edit["duration"] : ""; ?>" required>
                    </div>

                    <button class="admin-submit-btn" type="submit" name="<?php echo $edit ? "update" : "add"; ?>">
                        <?php echo $edit ? "Update Service" : "Add Service"; ?> →
                    </button>

                    <?php if ($edit) { ?>
                        <a href="services.php" class="admin-cancel-link">Cancel Editing</a>
                    <?php } ?>

                </form>
            </div>

            <div class="admin-record-area">

                <div class="admin-section-heading">
                    <div>
                        <p class="tag">SERVICE CATALOGUE</p>
                        <h2>All Services</h2>
                    </div>
                    <span class="admin-count"><?php echo mysqli_num_rows($rows); ?> Services</span>
                </div>

                <div class="admin-service-grid">

                    <?php while ($s = mysqli_fetch_assoc($rows)) { ?>

                        <article class="admin-service-card">
                            <div class="admin-service-icon">🔧</div>
                            <h3><?php echo clean($s["name"]); ?></h3>
                            <p><?php echo clean($s["description"]); ?></p>

                            <div class="admin-service-meta">
                                <div>
                                    <small>Price</small>
                                    <strong>₹<?php echo number_format((float)$s["price"], 2); ?></strong>
                                </div>
                                <div>
                                    <small>Duration</small>
                                    <strong><?php echo (int)$s["duration"]; ?> min</strong>
                                </div>
                            </div>

                            <div class="admin-record-actions">
                                <a href="services.php?edit=<?php echo (int)$s["id"]; ?>" class="admin-edit-btn">Edit</a>

                                <form method="POST" onsubmit="return confirm('Delete this service?');">
                                    <input type="hidden" name="id" value="<?php echo (int)$s["id"]; ?>">
                                    <button type="submit" name="delete" class="admin-delete-btn">Delete</button>
                                </form>
                            </div>
                        </article>

                    <?php } ?>

                </div>

            </div>

        </div>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
