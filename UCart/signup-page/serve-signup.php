<?php
// Set the content type to HTML
header('Content-Type: text/html');
require '../template/connection.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form inputs
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $accountType = $_POST['account-type']; // Either 'customer' or 'business'

    // Check if any field is empty
    if (empty($user) || empty($email) || empty($password) || empty($accountType)) {
        echo "<script>alert('All fields are required. Please fill in the missing fields.');</script>";
        echo "<script>window.location.href = 'serve-signup.php';</script>";
        exit;
    }

    // Check if the email already exists in the database
    $checkEmailQuery = "SELECT * FROM usertable WHERE email = ?";
    $stmt = $mysqli->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('This email is already registered. Please use a different email.');</script>";
        echo "<script>window.location.href = 'serve-signup.php';</script>";
        exit;
    } else {
        // Insert the user into the database without hashing the password
        $insertQuery = "INSERT INTO usertable (username, email, password, accounttype) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insertQuery);
        $stmt->bind_param("ssss", $user, $email, $password, $accountType);

        if ($stmt->execute()) {
            echo "<script>alert('Signup successful! Please sign in.');</script>";
            echo "<script>window.location.href = '../signin-page/serve-signin.php';</script>";
        } else {
            echo "<script>alert('An error occurred during signup. Please try again.');</script>";
        }
    }

    // Close the statement and connection
    $stmt->close();
}

$mysqli->close();



// Read and output the contents of index.html
echo file_get_contents('../template/nav-bar.html');
echo file_get_contents('signup.html');
?>