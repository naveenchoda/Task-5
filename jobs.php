<?php

session_start();

?>

<!DOCTYPE html>

<html>

<head>

    <title>Jobs - JobConnect</title>

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

    <h2>
        🔎 Find Your Dream Job
    </h2>

    <p>
        Search jobs in real time.
    </p>


    <!-- SEARCH BOX -->

    <form
        id="searchForm"
        style="max-width:600px;"
    >

        <label>
            Search Jobs
        </label>

        <input
            type="text"
            id="searchInput"
            placeholder="Search by job title, company, category or location..."
            autocomplete="off"
        >

    </form>


    <!-- JOB RESULTS -->

    <div
        class="cards"
        id="jobResults"
    >

        <div class="card">

            <h3>
                Loading Jobs...
            </h3>

        </div>

    </div>


</section>


<footer>

    <p>
        © 2026 JobConnect | Online Job Portal
    </p>

</footer>


<script>

const searchInput =
    document.getElementById("searchInput");

const jobResults =
    document.getElementById("jobResults");


function searchJobs() {

    const search =
        searchInput.value.trim();


    fetch(
        "ajax/search_jobs.php?search=" +
        encodeURIComponent(search)
    )

    .then(response => {

        if (!response.ok) {
            throw new Error(
                "Network response failed"
            );
        }

        return response.text();

    })

    .then(data => {

        jobResults.innerHTML = data;

    })

    .catch(error => {

        jobResults.innerHTML =

            '<div class="card">' +

            '<h3>⚠️ Error</h3>' +

            '<p>Unable to load jobs.</p>' +

            '</div>';

        console.error(error);

    });

}


/* Search while typing */

searchInput.addEventListener(
    "input",
    searchJobs
);


/* Load all jobs when page opens */

searchJobs();

</script>


</body>

</html>
