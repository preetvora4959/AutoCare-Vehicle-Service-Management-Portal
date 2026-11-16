
<?php
include "../includes/functions.php";
admin_only();
include "../db.php";

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$like = "%" . $search . "%";

$q = mysqli_prepare($conn, "SELECT id,name,email,phone,created_at FROM users WHERE role='customer' AND (name LIKE ? OR email LIKE ? OR phone LIKE ?) ORDER BY id DESC");
mysqli_stmt_bind_param($q, "sss", $like, $like, $like);
mysqli_stmt_execute($q);

$rows = mysqli_stmt_get_result($q);
$total = mysqli_num_rows($rows);

include "../includes/header.php";
?>

<section class="admin-page">
    <div class="container">

        <div class="admin-page-banner">
            <div>
                <p class="tag">ADMINISTRATION</p>
                <h1>Manage Customers</h1>
                <p>Search and manage registered AutoCare customers.</p>
            </div>
            <div class="admin-welcome-icon">👥</div>
        </div>

        <div class="admin-section-heading">
            <div>
                <p class="tag">CUSTOMER DIRECTORY</p>
                <h2>Registered Customers</h2>
            </div>
            <span class="admin-count"><?php echo $total; ?> Results</span>
        </div>

        <form method="GET" class="admin-search-form">
            <input type="search" name="search" placeholder="Search by name, email or phone..." value="<?php echo clean($search); ?>">
            <button type="submit" class="admin-submit-btn">Search Customers</button>

            <?php if ($search !== "") { ?>
                <a href="customers.php" class="admin-reset-link">Clear</a>
            <?php } ?>
        </form>

        <?php if ($total > 0) { ?>

            <div class="admin-customer-grid">

                <?php while ($u = mysqli_fetch_assoc($rows)) { ?>

                    <article class="admin-customer-card">

                        <div class="admin-customer-avatar">👤</div>

                        <h3><?php echo clean($u["name"]); ?></h3>

                        <span class="admin-customer-label">Registered Customer</span>

                        <div class="admin-customer-info">
                            <p><span>✉</span><?php echo clean($u["email"]); ?></p>
                            <p><span>☎</span><?php echo clean($u["phone"]); ?></p>
                            <p><span>📅</span><?php echo clean($u["created_at"]); ?></p>
                        </div>

                        <div class="admin-record-actions">
                            <a href="customer-details.php?id=<?php echo (int)$u["id"]; ?>" class="admin-edit-btn">View Details</a>
                            <a href="customer-edit.php?id=<?php echo (int)$u["id"]; ?>" class="admin-secondary-btn">Edit</a>
                        </div>

                    </article>

                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="admin-empty-state">
                <div>👥</div>
                <h3>No Customers Found</h3>
                <p>Try another search term or check back later.</p>
            </div>

        <?php } ?>

    </div>
</section>

<?php include "../includes/footer.php"; ?>
