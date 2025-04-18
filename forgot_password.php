<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "doctor_appointmentsss");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Check if the email exists
        $stmt = $conn->prepare("SELECT email FROM patients WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // Update the password in plain text
            $update_stmt = $conn->prepare("UPDATE patients SET password = ? WHERE email = ?");
            if (!$update_stmt) {
                die("Prepare failed: " . $conn->error);
            }

            // Save the new password directly (plain text)
            $update_stmt->bind_param("ss", $new_password, $email);
            if ($update_stmt->execute()) {
                $message = "Password has been updated successfully.";
            } else {
                $message = "Failed to update password. Please try again.";
            }
            $update_stmt->close();
        } else {
            $message = "No account found with that email.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="login style.css">
</head>
<body>
<div class="background">
        <svg class="ecg-wave" viewBox="0 0 500 100" preserveAspectRatio="none">
            <path d="M0,50 L50,50 L70,10 L90,90 L110,50 L150,50 L170,80 L190,50 L230,50 L250,20 L270,80 L290,50 L500,50" />
        </svg>
    </div>
    <div class="container">
        <div class="login-box">
            <h2>Forgot Password</h2>

            <!-- Message -->
            <?php if (!empty($message)) echo "<p class='message' style='color:green;'>$message</p>"; ?>

            <form action="" method="POST">
                <div class="input-group">
                    <label for="email">Enter your registered email</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>
                </div>
                <div class="input-group">
                    <label for="new_password">Enter new password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm new password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                </div>
                <button type="submit">Reset Password</button>
            </form>
            <p class="login-back"><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</body>
</html>