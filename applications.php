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

$user_id = $_SESSION["user_id"];


/* =========================
   GET USER APPLICATIONS
========================= */

$stmt = $conn->prepare(
    "SELECT
        applications.id,
        applications.cover_letter,
        applications.status,
        applications.applied_at,

        jobs.title,
        jobs.company,
        jobs.location,
        jobs.job_type,
        jobs.salary,

        categories.category_name

     FROM applications

     INNER JOIN jobs
        ON applications.job_id = jobs.id

     INNER JOIN categories
        ON jobs.category_id = categories.id

     WHERE applications.user_id = ?

     ORDER BY applications.applied_at DESC"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>

<html>

<head>

    <title>
        My Applications - JobConnect
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

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="jobs.php">
            Find Jobs
        </a>

        <a href="profile.php">
            Profile
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<section>

    <h2>
        📋 My Applications
    </h2>

    <p>
        Track all your job applications here.
    </p>


    <?php if ($result->num_rows > 0): ?>


        <div class="cards">


            <?php while ($application = $result->fetch_assoc()): ?>


                <div class="card">

                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $application["title"]
                        );
                        ?>

                    </h3>


                    <p>

                        🏢
                        <strong>Company:</strong>

                        <?php
                        echo htmlspecialchars(
                            $application["company"]
                        );
                        ?>

                    </p>


                    <p>

                        📂
                        <strong>Category:</strong>

                        <?php
                        echo htmlspecialchars(
                            $application["category_name"]
                        );
                        ?>

                    </p>


                    <p>

                        📍
                        <strong>Location:</strong>

                        <?php
                        echo htmlspecialchars(
                            $application["location"]
                        );
                        ?>

                    </p>


                    <p>

                        💼
                        <strong>Job Type:</strong>

                        <?php
                        echo htmlspecialchars(
                            $application["job_type"]
                        );
                        ?>

                    </p>


                    <p>

                        💰
                        <strong>Salary:</strong>

                        ₹<?php
                        echo number_format(
                            $application["salary"],
                            2
                        );
                        ?>

                    </p>


                    <p>

                        📅
                        <strong>Applied On:</strong>

                        <?php
                        echo date(
                            "d M Y, h:i A",
                            strtotime(
                                $application["applied_at"]
                            )
                        );
                        ?>

                    </p>


                    <p>

                        📌
                        <strong>Status:</strong>

                        <?php
                        echo htmlspecialchars(
                            $application["status"]
                        );
                        ?>

                    </p>


                    <hr>


                    <h4>
                        ✉️ Cover Letter
                    </h4>


                    <p>

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $application[
                                    "cover_letter"
                                ]
                            )
                        );
                        ?>

                    </p>


                    <br>


                    <a
                        href="job_details.php?id=<?php
                        echo $application["id"];
                        ?>"
                        class="button"
                    >
                        View Job
                    </a>


                </div>


            <?php endwhile; ?>


        </div>


    <?php else: ?>


        <div class="card">

            <h3>
                📭 No Applications Yet
            </h3>

            <p>
                You have not applied for any jobs yet.
            </p>

            <a
                href="jobs.php"
                class="button"
            >
                🔎 Find Jobs
            </a>

        </div>


    <?php endif; ?>


</section>


<footer>

    <p>
        © 2026 JobConnect | Online Job Portal
    </p>

</footer>


</body>

</html>

<?php

$stmt->close();

$conn->close();

?>
