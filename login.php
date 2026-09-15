<?php

session_start();

include "config/config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email == "" || $password == "") {

        $message = "Please enter email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT users.*, roles.role_name
             FROM users
             INNER JOIN roles
             ON users.role_id = roles.id
             WHERE users.email = ?"
        );

        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {

            if ($user["email_verified"] != 1) {

                $message = "Please verify your email first.";

            } elseif ($user["status"] != "active") {

                $message = "Your account is inactive.";

            } else {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["role_name"] = $user["role_name"];

                if ($user["role_name"] == "admin") {

                    header("Location: admin/index.php");

                } else {

                    header("Location: dashboard.php");

                }

                exit();
            }

        } else {

            $message = "Invalid email or password.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Login - JobConnect</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>💼 JobConnect</h1>

    <nav>
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
    </nav>

</header>

<section>

    <h2>Login 🔐</h2>

    <?php if ($message != ""): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST">

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
            placeholder="Enter your password"
            required
        >

        <button type="submit">
            Login
        </button>

    </form>

</section>

</body>

</html>
