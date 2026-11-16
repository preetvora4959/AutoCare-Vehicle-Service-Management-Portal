
<?php
session_start();
include "db.php";
include "includes/functions.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $pass = $_POST["password"];

    if ($name === "" || $email === "" || $phone === "" || $pass === "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error = "Phone must be 10 digits.";
    } elseif (strlen($pass) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $q = mysqli_prepare($conn, "SELECT id FROM users WHERE email=?");
        mysqli_stmt_bind_param($q, "s", $email);
        mysqli_stmt_execute($q);

        if (mysqli_num_rows(mysqli_stmt_get_result($q))) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $q = mysqli_prepare($conn, "INSERT INTO users(name,email,phone,password,role) VALUES(?,?,?,?,'customer')");
            mysqli_stmt_bind_param($q, "ssss", $name, $email, $phone, $hash);
            mysqli_stmt_execute($q);

            header("Location: login.php");
            exit();
        }
    }
}

include "includes/header.php";
?>

<section class="register-section">
    <div class="register-container">

        <div class="register-visual">
            <div class="register-visual-content">

                <span class="login-tag">JOIN AUTOCARE TODAY</span>

                <h1>Better Care.<br>Better Driving.</h1>

                <p>Create your AutoCare account and make vehicle maintenance easier. Manage your vehicles and book services in just a few steps.</p>

                <div class="register-benefits">

                    <div class="register-benefit">
                        <span>🚘</span>
                        <div>
                            <strong>Manage Your Vehicles</strong>
                            <small>Keep your vehicle details organized.</small>
                        </div>
                    </div>

                    <div class="register-benefit">
                        <span>🔧</span>
                        <div>
                            <strong>Easy Service Booking</strong>
                            <small>Book the services your vehicle needs.</small>
                        </div>
                    </div>

                    <div class="register-benefit">
                        <span>📋</span>
                        <div>
                            <strong>Track Your Bookings</strong>
                            <small>Stay updated on service requests.</small>
                        </div>
                    </div>

                </div>

                <div class="register-decoration">
                    <span>⚙️</span>
                    <span>🔩</span>
                    <span>🛠️</span>
                </div>

            </div>
        </div>

        <div class="register-form-area">
            <div class="register-form-box">

                <div class="login-form-icon">👤</div>

                <p class="tag">CUSTOMER REGISTRATION</p>

                <h2>Create Account</h2>

                <p class="login-subtitle">Enter your details to get started with AutoCare.</p>

                <?php if ($error) { ?>
                    <div class="login-error">
                        <?php echo clean($error); ?>
                    </div>
                <?php } ?>

                <form method="POST" class="modern-login-form">

                    <div class="login-field">
                        <label>Full Name</label>
                        <div class="login-input-wrap">
                            <span>👤</span>
                            <input type="text" name="name" placeholder="Enter your full name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="login-field">
                        <label>Email Address</label>
                        <div class="login-input-wrap">
                            <span>✉</span>
                            <input type="email" name="email" placeholder="Enter your email address" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="login-field">
                        <label>Phone Number</label>
                        <div class="login-input-wrap">
                            <span>📞</span>
                            <input type="tel" name="phone" placeholder="Enter 10-digit phone number" maxlength="10" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="login-field">
                        <label>Password</label>
                        <div class="login-input-wrap">
                            <span>🔒</span>
                            <input type="password" name="password" placeholder="Create a password (minimum 6 characters)" minlength="6" required>
                        </div>
                    </div>

                    <button type="submit" class="login-submit-btn">Create My Account <span>→</span></button>

                </form>

                <p class="login-register-text">
                    Already have an account?
                    <a href="login.php">Login Here</a>
                </p>

                <a href="index.php" class="login-back-link">← Back to Home</a>

            </div>
        </div>

    </div>
</section>

<?php include "includes/footer.php"; ?>
