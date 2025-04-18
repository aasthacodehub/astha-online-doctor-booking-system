<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doctor_appointmentsss";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']); // Plain text password

    $errors = [];

    // Validation
    if (empty($fullname) || !preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        $errors[] = "Please enter a valid name.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }

    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Enter a valid 10-digit phone number.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // Show errors, if any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        echo "<p><a href='javascript:history.back()'>Go back</a></p>";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT email FROM patients WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<p style='color:red;'>Email already exists. <a href='login.php'>Login here</a>.</p>";
        } else {
            // Insert data into database with plain text password
            $insert = $conn->prepare("INSERT INTO patients (fullname, email, phone, password) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $fullname, $email, $phone, $password);

            if ($insert->execute()) {
                // Debugging: Confirm successful registration
                // echo "Registration successful. Redirecting...";
            
                // Use JavaScript to redirect
                echo "<script>
                    alert('Registered successfully! Redirecting to login page.');
                    window.location.href = 'meet our.html';
                </script>";
                exit(); // Stop further execution after successful registration
            } else {
                // Debugging: Show error details
                echo "<p style='color:red;'>Error. Try again later.</p>";
                echo "<p style='color:red;'>Database error: " . $insert->error . "</p>";
            }

            $insert->close();
        }

        $stmt->close();
    }

    $conn->close();
}
?>