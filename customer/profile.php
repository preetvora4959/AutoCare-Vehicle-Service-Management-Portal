
<?php
include "../includes/functions.php";
customer_only();
include "../db.php";

$uid = (int)$_SESSION["user_id"];
$error = "";
$success = "";

$q = mysqli_prepare($conn, "SELECT * FROM users WHERE id=? AND role='customer'");
mysqli_stmt_bind_param($q, "i", $uid);
mysqli_stmt_execute($q);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($q));

if (!$user) {
    die("Customer account not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if ($name === "" || $email === "" || $phone === "") {
        $error = "Name, email and phone are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error = "Phone number must contain exactly 10 digits.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email=? AND id!=?");
        mysqli_stmt_bind_param($check, "si", $email, $uid);
        mysqli_stmt_execute($check);

        if (mysqli_num_rows(mysqli_stmt_get_result($check)) > 0) {
            $error = "This email address is already registered.";
        } elseif ($new_password !== "" || $confirm_password !== "" || $current_password !== "") {
            if ($current_password === "" || $new_password === "" || $confirm_password === "") {
                $error = "Fill all password fields to change your password.";
            } elseif (!password_verify($current_password, $user["password"])) {
                $error = "Your current password is incorrect.";
            } elseif (strlen($new_password) < 6) {
                $error = "New password must be at least 6 characters.";
            } elseif ($new_password !== $confirm_password) {
                $error = "New password and confirmation do not match.";
            } else {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);

                $update = mysqli_prepare($conn, "UPDATE users SET name=?, email=?, phone=?, password=? WHERE id=? AND role='customer'");
                mysqli_stmt_bind_param($update, "ssssi", $name, $email, $phone, $hash, $uid);

                if (mysqli_stmt_execute($update)) {
                    $_SESSION["user_name"] = $name;
                    $success = "Profile and password updated successfully.";

                    $q = mysqli_prepare($conn, "SELECT * FROM users WHERE id=? AND role='customer'");
                    mysqli_stmt_bind_param($q, "i", $uid);
                    mysqli_stmt_execute($q);
                    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
                } else {
                    $error = "Unable to update your profile. Please try again.";
                }
            }
        } else {
            $update = mysqli_prepare($conn, "UPDATE users SET name=?, email=?, phone=? WHERE id=? AND role='customer'");
            mysqli_stmt_bind_param($update, "sssi", $name, $email, $phone, $uid);

            if (mysqli_stmt_execute($update)) {
                $_SESSION["user_name"] = $name;
                $success = "Profile updated successfully.";

                $q = mysqli_prepare($conn, "SELECT * FROM users WHERE id=? AND role='customer'");
                mysqli_stmt_bind_param($q, "i", $uid);
                mysqli_stmt_execute($q);
                $user = mysqli_fetch_assoc(mysqli_stmt_get_result($q));
            } else {
                $error = "Unable to update your profile. Please try again.";
            }
        }
    }
}

include "../includes/header.php";
?>

<section class="portal-page">
    <div class="container">

        <div class="portal-page-hero">
            <div>
                <p class="tag">CUSTOMER PORTAL</p>
                <h1>My Profile</h1>
                <p>Manage your personal information and account security.</p>
            </div>
            <div class="portal-hero-icon">👤</div>
        </div>

        <div class="profile-layout">

            <div class="profile-summary-card">

                <div class="profile-avatar">👤</div>

                <h2><?php echo clean($user["name"]); ?></h2>
                <p><?php echo clean($user["email"]); ?></p>

                <span class="profile-role-badge">Customer Account</span>

                <div class="profile-summary-divider"></div>

                <div class="profile-summary-info">
                    <span>📞</span>
                    <div>
                        <small>Phone Number</small>
                        <strong><?php echo clean($user["phone"]); ?></strong>
                    </div>
                </div>

                <div class="profile-summary-info">
                    <span>🛡️</span>
                    <div>
                        <small>Account Type</small>
                        <strong>Customer</strong>
                    </div>
                </div>

                <a href="dashboard.php" class="profile-dashboard-link">← Back to Dashboard</a>

            </div>

            <div class="profile-edit-card">

                <div class="portal-card-heading">
                    <div class="portal-heading-icon">✏️</div>
                    <div>
                        <h2>Edit Personal Information</h2>
                        <p>Keep your account details up to date.</p>
                    </div>
                </div>

                <?php if ($error !== "") { ?>
                    <div class="login-error"><?php echo clean($error); ?></div>
                <?php } ?>

                <?php if ($success !== "") { ?>
                    <div class="portal-message"><?php echo clean($success); ?></div>
                <?php } ?>

                <form method="POST" class="portal-form">

                    <div class="portal-field">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo clean($user["name"]); ?>" placeholder="Enter your full name" required>
                    </div>

                    <div class="portal-field">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo clean($user["email"]); ?>" placeholder="Enter your email address" required>
                    </div>

                    <div class="portal-field">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo clean($user["phone"]); ?>" maxlength="10" placeholder="Enter 10-digit phone number" required>
                    </div>

                    <div class="profile-password-section">

                        <div class="profile-password-heading">
                            <div class="portal-heading-icon">🔒</div>
                            <div>
                                <h3>Change Password</h3>
                                <p>Leave these fields empty if you don't want to change your password.</p>
                            </div>
                        </div>

                        <div class="portal-field">
                            <label>Current Password</label>
                            <input type="password" name="current_password" placeholder="Enter current password">
                        </div>

                        <div class="portal-field">
                            <label>New Password</label>
                            <input type="password" name="new_password" minlength="6" placeholder="Enter new password">
                        </div>

                        <div class="portal-field">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" minlength="6" placeholder="Confirm new password">
                        </div>

                    </div>

                    <button type="submit" class="portal-submit-btn">Save Profile Changes →</button>

                </form>

            </div>

        </div>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
