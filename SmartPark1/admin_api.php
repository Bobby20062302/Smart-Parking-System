<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';

// Connect to parking_system database
$parking_db = new mysqli($db_host, $db_user, $db_pass, 'parking_system');
if ($parking_db->connect_error) {
    die("Parking system connection failed: " . $parking_db->connect_error);
}

// Connect to user_auth database
$user_db = new mysqli($db_host, $db_user, $db_pass, 'user_auth');
if ($user_db->connect_error) {
    die("User auth connection failed: " . $user_db->connect_error);
}

// Handle DELETE requests
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $booking_id = $parking_db->real_escape_string($data['booking_id']);
    
    $sql = "DELETE FROM bookings WHERE booking_id = '$booking_id'";
    if ($parking_db->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to delete booking']);
    }
    exit();
}

// Get statistics and booking data
$stats = [
    'totalBookings' => 0,
    'activeBookings' => 0,
    'totalUsers' => 0
];

// Total bookings
$result = $parking_db->query("SELECT COUNT(*) as total FROM bookings");
$stats['totalBookings'] = $result->fetch_assoc()['total'];

// Active bookings
$result = $parking_db->query("SELECT COUNT(*) as active FROM bookings 
    WHERE DATE(booking_date) = CURDATE() 
    AND TIME(NOW()) BETWEEN check_in_time AND check_out_time");
$stats['activeBookings'] = $result->fetch_assoc()['active'];

// Total users
$result = $user_db->query("SELECT COUNT(*) as total FROM users");
$stats['totalUsers'] = $result->fetch_assoc()['total'];

// Get all bookings
$bookings = [];
$result = $parking_db->query("SELECT * FROM bookings ORDER BY created_at DESC");

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

// Return JSON response
echo json_encode([
    'stats' => $stats,
    'bookings' => $bookings
]);

$parking_db->close();
$user_db->close();
?>