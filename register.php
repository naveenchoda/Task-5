<?php

session_start();

include "config/config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($name == "" || $email == "" || $password == "") {

        $message = "All fields are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email.";

    } elseif (strlen($password) < 6) {

        $message = "Password must contain at least 6 characters.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email already exists.";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $otp = (string) rand(100000, 999999);

            $otpExpires = date(
                "Y-m-d H:i:s",
                time() + 600
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, password, otp, otp_expires, email_verified)
                VALUES (?, ?, ?, ?, ?, 0)"
            );

            $stmt->bind_param(
                "sssss",
                $name,
                $email,
                $hashedPassword,
                $otp,
                $otpExpires
            );

            if ($stmt->execute()) {

                $_SESSION["otp_email"] = $email;
                $_SESSION["demo_otp"] = $otp;

                header("Location: verify_otp.php");
                exit();

            } else {

                $message = "Registration failed: " . $stmt->error;
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Register - JobConnect</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>💼 JobConnect</h1>

    <nav>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
    </nav>

</header>

<section>

    <h2>Create Your Account 👤</h2>

    <?php if ($message != ""): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <label>Name</label>

        <input
            type="text"
            name="name"
            placeholder="Enter your name"
            required
        >

        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Minimum 6 characters"
            minlength="6"
            required
        >

        <button type="submit">
            Register
        </button>

    </form>

</section>

</body>

</html>
