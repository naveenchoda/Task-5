<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>

    <title>JobConnect - Online Job Portal</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>💼 JobConnect</h1>

    <nav>

        <a href="index.php">Home</a>
        <a href="jobs.php">Jobs</a>

        <?php if (isset($_SESSION["user_id"])): ?>

            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>

        <?php else: ?>

            <a href="login.php">Login</a>
            <a href="register.php">Register</a>

        <?php endif; ?>

    </nav>

</header>

<section class="hero">

    <h2>Find Your Dream Job 🚀</h2>

    <p>
        Search jobs, apply online and build your career.
    </p>

    <a href="jobs.php" class="button">
        Explore Jobs
    </a>

</section>

<section>

    <h2>Why Choose JobConnect?</h2>

    <div class="cards">

        <div class="card">
            <h3>🔎 Easy Job Search</h3>
            <p>Find jobs quickly using search and filters.</p>
        </div>

        <div class="card">
            <h3>📄 Easy Applications</h3>
            <p>Apply for jobs online with a simple process.</p>
        </div>

        <div class="card">
            <h3>📊 Track Applications</h3>
            <p>Track your job application status easily.</p>
        </div>

    </div>

</section>

<footer>

    <p>© 2026 JobConnect | Online Job Portal</p>

</footer>

</body>
</html>
