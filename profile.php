<?php

session_start();

include "config/config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$message = "";


/* =========================
   UPDATE PROFILE
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $phone = trim($_POST["phone"]);
    $skills = trim($_POST["skills"]);
    $education = trim($_POST["education"]);

    if ($name == "") {

        $message = "Name is required.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE users
             SET name = ?,
                 phone = ?,
                 skills = ?,
                 education = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ssssi",
            $name,
            $phone,
            $skills,
            $education,
            $user_id
        );

        if ($stmt->execute()) {

            $_SESSION["user_name"] = $name;

            $message = "Profile updated successfully.";

        } else {

            $message = "Profile update failed.";
        }

        $stmt->close();
    }
}


/* =========================
   RESUME UPLOAD
========================= */

if (
    isset($_FILES["resume"]) &&
    $_FILES["resume"]["error"] != UPLOAD_ERR_NO_FILE
) {

    if ($_FILES["resume"]["error"] != UPLOAD_ERR_OK) {

        $message = "Resume upload failed.";

    } else {

        $fileName = $_FILES["resume"]["name"];
        $fileSize = $_FILES["resume"]["size"];
        $tmpName = $_FILES["resume"]["tmp_name"];

        $extension = strtolower(
            pathinfo($fileName, PATHINFO_EXTENSION)
        );

        /* Allowed file types */

        $allowedExtensions = [
            "pdf",
            "doc",
            "docx"
        ];

        /* Maximum size = 5 MB */

        $maxSize = 5 * 1024 * 1024;

        if (!in_array($extension, $allowedExtensions)) {

            $message = "Only PDF, DOC and DOCX files are allowed.";

        } elseif ($fileSize > $maxSize) {

            $message = "Resume size must be less than 5 MB.";

        } else {

            /* Check MIME type */

            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            $mimeType = finfo_file(
                $finfo,
                $tmpName
            );

            finfo_close($finfo);


            $allowedMimeTypes = [
                "application/pdf",
                "application/msword",
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            ];


            if (!in_array($mimeType, $allowedMimeTypes)) {

                $message = "Invalid resume file.";

            } else {

                $uploadFolder = __DIR__ . "/uploads/";

                /* Create uploads folder if not exists */

                if (!is_dir($uploadFolder)) {
                    mkdir($uploadFolder, 0777, true);
                }


                /* Create unique file name */

                $newFileName =
                    "resume_" .
                    $user_id .
                    "_" .
                    time() .
                    "_" .
                    rand(1000, 9999) .
                    "." .
                    $extension;


                $destination =
                    $uploadFolder . $newFileName;


                if (move_uploaded_file(
                    $tmpName,
                    $destination
                )) {

                    /* Get old resume */

                    $oldStmt = $conn->prepare(
                        "SELECT resume
                         FROM users
                         WHERE id = ?"
                    );

                    $oldStmt->bind_param(
                        "i",
                        $user_id
                    );

                    $oldStmt->execute();

                    $oldResult =
                        $oldStmt->get_result();

                    $oldUser =
                        $oldResult->fetch_assoc();

                    $oldStmt->close();


                    /* Save new resume path */

                    $resumePath =
                        "uploads/" . $newFileName;


                    $updateResume =
                        $conn->prepare(
                            "UPDATE users
                             SET resume = ?
                             WHERE id = ?"
                        );

                    $updateResume->bind_param(
                        "si",
                        $resumePath,
                        $user_id
                    );


                    if ($updateResume->execute()) {

                        /* Delete old resume */

                        if (
                            !empty($oldUser["resume"]) &&
                            strpos(
                                $oldUser["resume"],
                                "uploads/"
                            ) === 0
                        ) {

                            $oldFile =
                                __DIR__ .
                                "/" .
                                $oldUser["resume"];

                            if (file_exists($oldFile)) {
                                unlink($oldFile);
                            }
                        }


                        $message =
                            "Profile and resume updated successfully.";

                    } else {

                        $message =
                            "Resume uploaded but database update failed.";
                    }

                    $updateResume->close();

                } else {

                    $message =
                        "Unable to save resume.";
                }
            }
        }
    }
}


/* =========================
   GET USER DETAILS
========================= */

$stmt = $conn->prepare(
    "SELECT
        name,
        email,
        phone,
        skills,
        education,
        resume
     FROM users
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>

<html>

<head>

    <title>My Profile - JobConnect</title>

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
            Jobs
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

    <h2>👤 My Profile</h2>


    <?php if ($message != ""): ?>

        <p>
            <strong>
                <?php
                echo htmlspecialchars($message);
                ?>
            </strong>
        </p>

    <?php endif; ?>


    <form
        method="POST"
        enctype="multipart/form-data"
    >

        <label>
            Name
        </label>

        <input
            type="text"
            name="name"
            value="<?php
            echo htmlspecialchars($user["name"]);
            ?>"
            required
        >


        <label>
            Email
        </label>

        <input
            type="email"
            value="<?php
            echo htmlspecialchars($user["email"]);
            ?>"
            readonly
        >


        <label>
            Phone
        </label>

        <input
            type="text"
            name="phone"
            value="<?php
            echo htmlspecialchars(
                $user["phone"] ?? ""
            );
            ?>"
            placeholder="Enter phone number"
        >


        <label>
            Skills
        </label>

        <textarea
            name="skills"
            rows="4"
            placeholder="Example: Java, Python, PHP, MySQL"
        ><?php
        echo htmlspecialchars(
            $user["skills"] ?? ""
        );
        ?></textarea>


        <label>
            Education
        </label>

        <textarea
            name="education"
            rows="4"
            placeholder="Example: B.Tech Data Science"
        ><?php
        echo htmlspecialchars(
            $user["education"] ?? ""
        );
        ?></textarea>


        <label>
            Upload Resume
        </label>

        <input
            type="file"
            name="resume"
            accept=".pdf,.doc,.docx"
        >

        <p>
            Allowed: PDF, DOC, DOCX
            | Maximum size: 5 MB
        </p>


        <button type="submit">
            Update Profile & Upload Resume
        </button>

    </form>


    <!-- RESUME STATUS -->

    <div class="card">

        <h3>
            📄 Resume
        </h3>


        <?php if (!empty($user["resume"])): ?>

            <p>
                Resume uploaded successfully.
            </p>

            <p>
                File:
                <?php
                echo htmlspecialchars(
                    basename($user["resume"])
                );
                ?>
            </p>

            <a
                href="<?php
                echo htmlspecialchars(
                    $user["resume"]
                );
                ?>"
                target="_blank"
                class="button"
            >
                View Resume
            </a>

        <?php else: ?>

            <p>
                No resume uploaded yet.
            </p>

        <?php endif; ?>

    </div>


</section>


</body>

</html>
