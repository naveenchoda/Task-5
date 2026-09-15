<?php

session_start();

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Dashboard - JobConnect</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>💼 JobConnect</h1>

    <nav>

        <a href="index.php">Home</a>
        <a href="jobs.php">Find Jobs</a>
        <a href="profile.php">Profile</a>
        <a href="applications.php">My Applications</a>
        <a href="logout.php">Logout</a>

    </nav>

</header>

<section>

    <h2>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["user_name"]); ?> 👋
    </h2>

    <div class="cards">

        <div class="card">

            <h3>🔎 Find Jobs</h3>

            <p>Search available jobs.</p>

            <a href="jobs.php" class="button">
                Search Jobs
            </a>

        </div>

        <div class="card">

            <h3>📄 Applications</h3>

            <p>View your applications.</p>

            <a href="applications.php" class="button">
                My Applications
            </a>

        </div>

        <div class="card">

            <h3>👤 Profile</h3>

            <p>Update your profile.</p>

            <a href="profile.php" class="button">
                My Profile
            </a>

        </div>

    </div>

</section>

</body>

</html>
