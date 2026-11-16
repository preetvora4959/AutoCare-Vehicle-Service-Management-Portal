<?php
session_start();
include "../db.php";
include "../includes/functions.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    $q = mysqli_prepare($conn, "SELECT * FROM users WHERE email=? AND role='admin'");
    mysqli_stmt_bind_param($q, "s", $email);
    mysqli_stmt_execute($q);

    $u = mysqli_fetch_assoc(mysqli_stmt_get_result($q));

    if ($u && password_verify($_POST["password"], $u["password"])) {
        session_regenerate_id(true);

        $_SESSION["user_id"] = $u["id"];
        $_SESSION["user_name"] = $u["name"];
        $_SESSION["role"] = "admin";

        header("Location: dashboard.php");
        exit();
    }

    $error = "Invalid admin login.";
}

include "../includes/header.php";
?>

<section class="login-section">
    <div class="login-container">

        <div class="login-visual admin-login-visual">
            <div class="login-visual-content">
                <span class="login-tag">AUTOCARE MANAGEMENT</span>
                <h1>Manage Everything.<br>With Confidence.</h1>
                <p>Access your administration panel to manage services, customers, bookings and daily operations.</p>

                <div class="admin-dashboard-art">
                    <div class="admin-art-header">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <div class="admin-art-content">
                        <div class="admin-art-sidebar">
                            <i></i>
                            <i></i>
                            <i></i>
                            <i></i>
                        </div>

                        <div class="admin-art-main">
                            <div class="admin-art-title"></div>
                            <div class="admin-art-stats">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <div class="admin-art-chart">
                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="login-visual-bottom">
                    <span>⚙️</span>
                    <div>
                        <strong>Administrator Dashboard</strong>
                        <small>Control your AutoCare operations.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="login-form-area">
            <div class="login-form-box">

                <div class="login-form-icon admin-form-icon">⚙️</div>

                <p class="tag">ADMINISTRATION PORTAL</p>
                <h2>Admin Login</h2>
                <p class="login-subtitle">Sign in to manage your AutoCare portal.</p>

                <?php if ($error) { ?>
                    <div class="login-error">
                        <?php echo clean($error); ?>
                    </div>
                <?php } ?>

                <form method="POST" class="modern-login-form">

                    <div class="login-field">
                        <label>Admin Email</label>
                        <div class="login-input-wrap">
                            <span>✉</span>
                            <input type="email" name="email" placeholder="Enter admin email" required>
                        </div>
                    </div>

                    <div class="login-field">
                        <label>Password</label>
                        <div class="login-input-wrap">
                            <span>🔒</span>
                            <input type="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <button type="submit" class="login-submit-btn">Access Dashboard <span>→</span></button>

                </form>

                <div class="admin-security-note">
                    <span>🛡️</span>
                    <p>Authorized administrators only. Please keep your login credentials secure.</p>
                </div>

                <div class="login-divider"><span>OR</span></div>

                <a href="../login.php" class="admin-login-link">Login as Customer →</a>

                <a href="../index.php" class="login-back-link">← Back to Home</a>

            </div>
        </div>

    </div>
</section>

<?php include "../includes/footer.php"; ?>