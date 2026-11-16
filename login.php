<?php
session_start();
include "db.php";
include "includes/functions.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $pass = $_POST["password"];

    $q = mysqli_prepare($conn, "SELECT * FROM users WHERE email=?");
    mysqli_stmt_bind_param($q, "s", $email);
    mysqli_stmt_execute($q);

    $r = mysqli_stmt_get_result($q);

    if ($u = mysqli_fetch_assoc($r)) {
        if (password_verify($pass, $u["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $u["id"];
            $_SESSION["user_name"] = $u["name"];
            $_SESSION["role"] = $u["role"];

            header("Location: " . ($u["role"] === "admin" ? "admin/dashboard.php" : "customer/dashboard.php"));
            exit();
        }
    }

    $error = "Invalid email or password.";
}

include "includes/header.php";
?>

<section class="login-section">
    <div class="login-container">

        <div class="login-visual">
            <div class="login-visual-content">
                <span class="login-tag">WELCOME TO AUTOCARE</span>
                <h1>Your Vehicle.<br>Our Responsibility.</h1>
                <p>Reliable vehicle care starts here. Login to manage your vehicles, book services and track your service requests.</p>

                <div class="login-car-illustration">
                    <div class="login-car-top"></div>
                    <div class="login-car-body">
                        <div class="login-car-light"></div>
                    </div>
                    <div class="login-car-wheel wheel-left"></div>
                    <div class="login-car-wheel wheel-right"></div>
                </div>

                <div class="login-visual-bottom">
                    <span>🔧</span>
                    <div>
                        <strong>Professional Vehicle Care</strong>
                        <small>Simple. Reliable. Convenient.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="login-form-area">
            <div class="login-form-box">

                <div class="login-form-icon">👤</div>

                <p class="tag">CUSTOMER PORTAL</p>
                <h2>Welcome Back!</h2>
                <p class="login-subtitle">Login to access your AutoCare account.</p>

                <?php if ($error) { ?>
                    <div class="login-error">
                        <?php echo clean($error); ?>
                    </div>
                <?php } ?>

                <form method="POST" class="modern-login-form">

                    <div class="login-field">
                        <label>Email Address</label>
                        <div class="login-input-wrap">
                            <span>✉</span>
                            <input type="email" name="email" placeholder="Enter your email address" required>
                        </div>
                    </div>

                    <div class="login-field">
                        <label>Password</label>
                        <div class="login-input-wrap">
                            <span>🔒</span>
                            <input type="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <button type="submit" class="login-submit-btn">Login to Account <span>→</span></button>

                </form>

                <p class="login-register-text">
                    Don't have an account?
                    <a href="register.php">Create Account</a>
                </p>

                <div class="login-divider"><span>OR</span></div>

                <a href="admin/login.php" class="admin-login-link">Login as Administrator →</a>

                <a href="index.php" class="login-back-link">← Back to Home</a>

            </div>
        </div>

    </div>
</section>

<?php include "includes/footer.php"; ?>