<?php
session_start();

// Check if user is admin
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'parking_system';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get statistics
$stats = [
    'total' => 0,
    'active' => 0,
    'revenue' => 0
];

// Get total bookings
$result = $conn->query("SELECT COUNT(*) as total FROM bookings");
$stats['total'] = $result->fetch_assoc()['total'];

// Get active bookings
$result = $conn->query("SELECT COUNT(*) as active FROM bookings 
    WHERE DATE(booking_date) = CURDATE() 
    AND TIME(NOW()) BETWEEN check_in_time AND check_out_time");
$stats['active'] = $result->fetch_assoc()['active'];

// Get all bookings
$bookings = [];
$result = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 50");

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

// Return JSON response
echo json_encode([
    'stats' => $stats,
    'bookings' => $bookings
]);

$conn->close();
?>