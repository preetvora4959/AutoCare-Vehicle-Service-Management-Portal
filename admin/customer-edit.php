
<?php
include "../includes/functions.php";
admin_only();
include "../db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$error = "";

if (isset($_POST["update"])) {
    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);

    if ($name === "" || !preg_match("/^[0-9]{10}$/", $phone)) {
        $error = "Enter a valid name and 10-digit phone number.";
    } else {
        $q = mysqli_prepare($conn, "UPDATE users SET name=?,phone=? WHERE id=? AND role='customer'");
        mysqli_stmt_bind_param($q, "ssi", $name, $phone, $id);
        mysqli_stmt_execute($q);

        header("Location: customers.php");
        exit();
    }
}

$q = mysqli_prepare($conn, "SELECT name,email,phone FROM users WHERE id=? AND role='customer'");
mysqli_stmt_bind_param($q, "i", $id);
mysqli_stmt_execute($q);

$u = mysqli_fetch_assoc(mysqli_stmt_get_result($q));

if (!$u) {
    header("Location: customers.php");
    exit();
}

include "../includes/header.php";
?>

<section class="admin-page">
    <div class="container">

        <div class="admin-page-banner">
            <div>
                <p class="tag">CUSTOMER MANAGEMENT</p>
                <h1>Edit Customer</h1>
                <p>Update the customer's account information.</p>
            </div>
            <div class="admin-welcome-icon">✏️</div>
        </div>

        <div class="admin-single-form">

            <div class="admin-form-card">

                <div class="admin-card-heading">
                    <div class="admin-action-icon">👤</div>
                    <div>
                        <h2>Customer Information</h2>
                        <p>Update name and contact details.</p>
                    </div>
                </div>

                <?php if ($error !== "") { ?>
                    <div class="login-error"><?php echo clean($error); ?></div>
                <?php } ?>

                <form method="POST" class="admin-form">

                    <div class="admin-field">
                        <label>Customer Name</label>
                        <input name="name" value="<?php echo clean($u["name"]); ?>" required>
                    </div>

                    <div class="admin-field">
                        <label>Email Address</label>
                        <input value="<?php echo clean($u["email"]); ?>" disabled>
                    </div>

                    <div class="admin-field">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" maxlength="10" pattern="[0-9]{10}" value="<?php echo clean($u["phone"]); ?>" required>
                    </div>

                    <button type="submit" name="update" class="admin-submit-btn">Update Customer →</button>

                    <a href="customers.php" class="admin-cancel-link">Cancel and Go Back</a>

                </form>

            </div>

        </div>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
