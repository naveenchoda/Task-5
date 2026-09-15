<?php

include "../config/config.php";

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

$searchTerm = "%" . $search . "%";


$stmt = $conn->prepare(
    "SELECT
        jobs.id,
        jobs.title,
        jobs.company,
        jobs.location,
        jobs.salary,
        jobs.job_type,
        jobs.description,
        categories.category_name

     FROM jobs

     INNER JOIN categories
        ON jobs.category_id = categories.id

     WHERE jobs.status = 'Active'
     AND (
        jobs.title LIKE ?
        OR jobs.company LIKE ?
        OR jobs.location LIKE ?
        OR categories.category_name LIKE ?
     )

     ORDER BY jobs.created_at DESC"
);


$stmt->bind_param(
    "ssss",
    $searchTerm,
    $searchTerm,
    $searchTerm,
    $searchTerm
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows > 0) {

    while ($job = $result->fetch_assoc()) {

        $description = $job["description"];

        if (strlen($description) > 100) {

            $description =
                substr($description, 0, 100) . "...";
        }

        ?>

        <div class="card">

            <h3>
                <?php
                echo htmlspecialchars(
                    $job["title"]
                );
                ?>
            </h3>

            <p>
                🏢
                <strong>Company:</strong>

                <?php
                echo htmlspecialchars(
                    $job["company"]
                );
                ?>
            </p>

            <p>
                📂
                <strong>Category:</strong>

                <?php
                echo htmlspecialchars(
                    $job["category_name"]
                );
                ?>
            </p>

            <p>
                📍
                <strong>Location:</strong>

                <?php
                echo htmlspecialchars(
                    $job["location"]
                );
                ?>
            </p>

            <p>
                💰
                <strong>Salary:</strong>

                ₹<?php
                echo number_format(
                    $job["salary"],
                    2
                );
                ?>
            </p>

            <p>
                💼
                <strong>Job Type:</strong>

                <?php
                echo htmlspecialchars(
                    $job["job_type"]
                );
                ?>
            </p>

            <p>
                <?php
                echo htmlspecialchars(
                    $description
                );
                ?>
            </p>

            <a
                href="job_details.php?id=<?php
                echo $job["id"];
                ?>"
                class="button"
            >
                View Details
            </a>

        </div>

        <?php
    }

} else {

    ?>

    <div class="card">

        <h3>
            😔 No Jobs Found
        </h3>

        <p>
            Try another job title, company,
            category or location.
        </p>

    </div>

    <?php
}


$stmt->close();

$conn->close();

?>
