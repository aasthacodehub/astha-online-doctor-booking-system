<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // change if needed
$password = "";     // change if needed
$dbname = "appointment dr gyno"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'] ?? '';
$age = $_POST['age'] ?? '';
$contact = $_POST['contact'] ?? '';
$doctor = $_POST['doctor'] ?? '';
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';

// Validate (basic)
if ($name && $age && $contact && $doctor && $date && $time) {
    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO appointments (name, age, contact, doctor, date, time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $name, $age, $contact, $doctor, $date, $time);

    if ($stmt->execute()) {
        echo "Appointment booked successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "All fields are required!";
}

$conn->close();
?>
