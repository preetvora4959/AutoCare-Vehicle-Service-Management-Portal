
<?php
include "db.php";
include "includes/header.php";

$r = mysqli_query($conn, "SELECT * FROM services WHERE status='Active'");
?>

<section class="services-hero">
    <div class="container">
        <p class="tag">AUTOCARE SERVICES</p>
        <h1>Complete Care for <span>Your Vehicle</span></h1>
        <p>
            Explore our vehicle maintenance and repair services.
            Choose the care your vehicle needs.
        </p>
        <div class="services-hero-icon">🔧 ⚙️ 🛠️</div>
    </div>
</section>

<section class="container section services-page">

    <div class="section-heading">
        <p class="tag">OUR EXPERTISE</p>
        <h2>Explore Our Services</h2>
        <p>
            From routine maintenance to essential repairs,
            find the right service for your vehicle.
        </p>
    </div>

    <?php if ($r && mysqli_num_rows($r) > 0) { ?>

        <div class="cards service-list">

            <?php while ($s = mysqli_fetch_assoc($r)) { ?>

                <article class="service-card">

                    <div class="service-card-top">
                        <div class="feature-icon">🔧</div>
                        <span class="service-badge">Available</span>
                    </div>

                    <h3>
                        <?php echo htmlspecialchars($s["name"]); ?>
                    </h3>

                    <p class="service-description">
                        <?php echo htmlspecialchars($s["description"]); ?>
                    </p>

                    <div class="service-details">

                        <div class="service-price">
                            <small>Service Price</small>
                            <strong>
                                ₹<?php echo number_format((float)$s["price"], 2); ?>
                            </strong>
                        </div>

                        <div class="service-duration">
                            <small>Estimated Time</small>
                            <strong>
                                ⏱ <?php echo (int)$s["duration"]; ?> Minutes
                            </strong>
                        </div>

                    </div>

                </article>

            <?php } ?>

        </div>

    <?php } else { ?>

        <div class="empty-services">
            <div>🛠️</div>
            <h3>No Services Available</h3>
            <p>
                Our services are currently being updated.
                Please check again later.
            </p>
        </div>

    <?php } ?>

</section>

<section class="container">
    <div class="services-cta">
        <div>
            <p class="tag">READY TO GET STARTED?</p>
            <h2>Your Vehicle Needs the Right Care.</h2>
            <p>
                Create your account and book your next
                vehicle service with AutoCare.
            </p>
            <a href="register.php" class="btn light">Book a Service</a>
        </div>

        <div class="services-cta-icon">🚘</div>
    </div>
</section>

<?php include "includes/footer.php"; ?>
