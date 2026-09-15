<?php

session_start();

include "config/config.php";


/* =========================
   CHECK LOGIN
========================= */

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}


/* =========================
   CHECK JOB ID
========================= */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: jobs.php");
    exit();

}

$job_id = intval($_GET["id"]);
$user_id = $_SESSION["user_id"];

$message = "";


/* =========================
   GET JOB DETAILS
========================= */

$stmt = $conn->prepare(
    "SELECT
        jobs.id,
        jobs.title,
        jobs.company,
        jobs.location,
        jobs.salary,
        jobs.job_type,
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


/* =========================
   CHECK JOB EXISTS
========================= */

if (!$job) {

    echo "Job not found.";
    exit();

}


/* =========================
   CHECK PREVIOUS APPLICATION
========================= */

$check = $conn->prepare(
    "SELECT id
     FROM applications
     WHERE user_id = ?
       AND job_id = ?"
);

$check->bind_param(
    "ii",
    $user_id,
    $job_id
);

$check->execute();

$checkResult = $check->get_result();

$alreadyApplied =
    $checkResult->num_rows > 0;

$check->close();


/* =========================
   SUBMIT APPLICATION
========================= */

if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    !$alreadyApplied
) {

    $cover_letter =
        trim($_POST["cover_letter"]);


    if ($cover_letter == "") {

        $message =
            "Please enter a cover letter.";

    } elseif (strlen($cover_letter) < 20) {

        $message =
            "Cover letter must contain at least 20 characters.";

    } else {


        $stmt = $conn->prepare(
            "INSERT INTO applications
            (user_id, job_id, cover_letter, status)
            VALUES (?, ?, ?, 'Pending')"
        );

        $stmt->bind_param(
            "iis",
            $user_id,
            $job_id,
            $cover_letter
        );


        if ($stmt->execute()) {

            header(
                "Location: applications.php"
            );

            exit();

        } else {

            $message =
                "Application failed: " .
                $stmt->error;
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>
        Apply - JobConnect
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

    </nav>

</header>


<section>

    <h2>
        🚀 Apply for Job
    </h2>


    <!-- JOB INFORMATION -->

    <div class="card">

        <h3>
            <?php
            echo htmlspecialchars(
                $job["title"]
            );
            ?>
        </h3>


        <p>
            🏢 <strong>Company:</strong>

            <?php
            echo htmlspecialchars(
                $job["company"]
            );
            ?>
        </p>


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

    </div>


    <?php if ($alreadyApplied): ?>


        <div class="card">

            <h3>
                ✅ Already Applied
            </h3>

            <p>
                You have already applied
                for this job.
            </p>

            <a
                href="applications.php"
                class="button"
            >
                View My Applications
            </a>

        </div>


    <?php else: ?>


        <?php if ($message != ""): ?>

            <p>
                <strong>
                    <?php
                    echo htmlspecialchars(
                        $message
                    );
                    ?>
                </strong>
            </p>

        <?php endif; ?>


        <form method="POST">

            <label>
                Cover Letter
            </label>


            <textarea
                name="cover_letter"
                rows="8"
                placeholder="Write why you are suitable for this job..."
                required
            ></textarea>


            <button type="submit">
                🚀 Submit Application
            </button>

        </form>


    <?php endif; ?>


</section>


<footer>

    <p>
        © 2026 JobConnect | Online Job Portal
    </p>

</footer>


</body>

</html>
