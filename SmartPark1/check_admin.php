<?php
session_start();

function isAdmin($email) {
    return $email === 'bobbyudamala@gmail.com';
}

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'user_auth';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password']) && isAdmin($email)) {
            $_SESSION['admin'] = true;
            $_SESSION['user_id'] = $user['id'];
            header("Location: admin.php");
            exit();
        }
    }
    
    header("Location: login.html?error=invalid");
    exit();
}

$conn->close();
?>