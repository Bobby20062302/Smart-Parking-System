<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'user_auth';

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $email = 'bobbyudamala@gmail.com';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // First, check if the email already exists
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        // Update existing user
        $sql = "UPDATE users SET firstName = '$firstName', lastName = '$lastName', 
                password = '$password', role = 'admin' WHERE email = '$email'";
    } else {
        // Create new user
        $sql = "INSERT INTO users (firstName, lastName, email, password, role) 
                VALUES ('$firstName', '$lastName', '$email', '$password', 'admin')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: admin_login.html?registered=success");
    } else {
        header("Location: admin_register.html?error=true");
    }

    $conn->close();
}
?>