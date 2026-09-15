<?php

session_start();

include "config/config.php";


// Check whether job ID is provided

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: jobs.php");
    exit();

}

$job_id = intval($_GET["id"]);


// Get job details

$stmt = $conn->prepare(
    "SELECT
        jobs.id,
        jobs.title,
        jobs.company,
        jobs.location,
        jobs.salary,
        jobs.job_type,
        jobs.description,
        jobs.requirements,
        jobs.created_at,
        categories.category_name
     FROM jobs
     INNER JOIN categories
        ON jobs.category_id = categories.id
     WHERE jobs.id = ?
       AND jobs.status = 'Active'"
);

$stmt->bind_param("i", $job_id);

$stmt->execute();

$result = $stmt->get_result();

$job = $result->fetch_assoc();

$stmt->close();


// If job does not exist

if (!$job) {

    echo "Job not found.";
    exit();

}

?>

<!DOCTYPE html>

<html>

<head>

    <title>
        <?php
        echo htmlspecialchars($job["title"]);
        ?>
        - JobConnect
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>


<header>

    <h1>💼 JobConnect</h1>

    <nav>

        <a href="index.php">
            Home
        </a>

        <a href="jobs.php">
            Jobs
        </a>


        <?php if (isset($_SESSION["user_id"])): ?>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="profile.php">
                Profile
            </a>

            <a href="applications.php">
                Applications
            </a>

            <a href="logout.php">
                Logout
            </a>

        <?php else: ?>

            <a href="login.php">
                Login
            </a>

            <a href="register.php">
                Register
            </a>

        <?php endif; ?>

    </nav>

</header>


<section>

    <div class="card">

        <h2>
            <?php
            echo htmlspecialchars(
                $job["title"]
            );
            ?>
        </h2>


        <h3>
            🏢
            <?php
            echo htmlspecialchars(
                $job["company"]
            );
            ?>
        </h3>


        <p>

            📂 <strong>Category:</strong>

            <?php
            echo htmlspecialchars(
                $job["category_name"]
            );
            ?>

        </p>


        <p>

            📍 <strong>Location:</strong>

            <?php
            echo htmlspecialchars(
                $job["location"]
            );
            ?>

        </p>


        <p>

            💰 <strong>Salary:</strong>

            ₹<?php
            echo number_format(
                $job["salary"],
                2
            );
            ?>

        </p>


        <p>

            💼 <strong>Job Type:</strong>

            <?php
            echo htmlspecialchars(
                $job["job_type"]
            );
            ?>

        </p>


        <p>

            📅 <strong>Posted:</strong>

            <?php
            echo date(
                "d M Y",
                strtotime($job["created_at"])
            );
            ?>

        </p>


        <hr>


        <h3>
            📋 Job Description
        </h3>

        <p>
            <?php
            echo nl2br(
                htmlspecialchars(
                    $job["description"]
                )
            );
            ?>
        </p>


        <h3>
            📝 Requirements
        </h3>

        <p>
            <?php
            echo nl2br(
                htmlspecialchars(
                    $job["requirements"]
                )
            );
            ?>
        </p>


        <br>


        <?php if (isset($_SESSION["user_id"])): ?>

            <a
                href="apply.php?id=<?php
                echo $job["id"];
                ?>"
                class="button"
            >
                🚀 Apply Now
            </a>

        <?php else: ?>

            <p>
                Please login to apply for this job.
            </p>

            <a
                href="login.php"
                class="button"
            >
                🔐 Login to Apply
            </a>

        <?php endif; ?>


        <br><br>


        <a
            href="jobs.php"
            class="button"
        >
            ← Back to Jobs
        </a>

    </div>

</section>


<footer>

    <p>
        © 2026 JobConnect | Online Job Portal
    </p>

</footer>


</body>

</html>
