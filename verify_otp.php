<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "config/config.php";

$message = "";

if (!isset($_SESSION["otp_email"])) {

    header("Location: register.php");
    exit();

}

$email = $_SESSION["otp_email"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $otp = trim($_POST["otp"]);

    if ($otp == "") {

        $message = "Please enter the OTP.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, otp, otp_expires
             FROM users
             WHERE email = ?"
        );

        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {

            if (
                $user["otp"] == $otp &&
                !empty($user["otp_expires"]) &&
                strtotime($user["otp_expires"]) > time()
            ) {

                $update = $conn->prepare(
                    "UPDATE users
                     SET email_verified = 1,
                         otp = NULL,
                         otp_expires = NULL
                     WHERE id = ?"
                );

                if (!$update) {
                    die("Update Error: " . $conn->error);
                }

                $update->bind_param("i", $user["id"]);
                $update->execute();

                $update->close();

                unset($_SESSION["otp_email"]);
                unset($_SESSION["demo_otp"]);

                header("Location: login.php");
                exit();

            } else {

                $message = "Invalid or expired OTP.";

            }

        } else {

            $message = "User not found.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Verify OTP - JobConnect</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>💼 JobConnect</h1>

    <nav>
        <a href="index.php">Home</a>
    </nav>

</header>

<section>

    <h2>📧 Verify Your Email</h2>

    <p>
        Enter the 6-digit OTP.
    </p>

    <?php if (isset($_SESSION["demo_otp"])): ?>

        <p>
            <strong>
                Demo OTP:
                <?php echo htmlspecialchars($_SESSION["demo_otp"]); ?>
            </strong>
        </p>

    <?php endif; ?>

    <?php if ($message != ""): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Enter OTP</label>

        <input
            type="text"
            name="otp"
            maxlength="6"
            pattern="[0-9]{6}"
            placeholder="Enter 6-digit OTP"
            required
        >

        <button type="submit">
            Verify OTP
        </button>

    </form>

</section>

</body>

</html>
