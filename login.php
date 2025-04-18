<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "doctor_appointmentsss");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Securely fetch the user record
    $stmt = $conn->prepare("SELECT fullname, password FROM patients WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($fullname, $storedPassword);
        $stmt->fetch();

        // Compare plain text passwords
        if ($password === $storedPassword) {
            $_SESSION['fullname'] = $fullname;
            header("Location: meet our.html");
            exit(); // Always exit after a header redirect
        } else {
            $message = "Invalid password. Please try again.";
        }
    } else {
        $message = "No account found with that email.";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Appointment Login</title>
    <!-- 🔗 Link to external CSS -->
    <link rel="stylesheet" href="login style.css">
</head>
<body>
    <!-- 🌊 Background with ECG line -->
    <div class="background">
        <svg class="ecg-wave" viewBox="0 0 500 100" preserveAspectRatio="none">
            <path d="M0,50 L50,50 L70,10 L90,90 L110,50 L150,50 L170,80 L190,50 L230,50 L250,20 L270,80 L290,50 L500,50" />
        </svg>
    </div>

    <!-- 🔒 Login Form -->
    <div class="container">
        <div class="login-box">
            <h2>Login</h2>

            <!-- Error message -->
            <?php if (!empty($message)) echo "<p class='message' style='color:red;'>$message</p>"; ?>

            <form action="" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>
                <button type="submit">Login</button>
                <p class="register">Don't have an account? <a href="reg.html">Sign up</a></p>
                <p class="forgot-password"><a href="forgot_password.php">Forgot your password?</a></p>
            </form>
        </div>
    </div>
</body>
</html>