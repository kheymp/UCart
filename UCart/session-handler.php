<?php
session_start();
require '../template/connection.php';

/**
 * Check if the user is logged in.
 */
function ensureLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../signin-page/serve-signin.php");
        exit();
    }
}

/**
 * Destroy the current session and redirect to the login page.
 */
function logout() {
    session_unset();
    session_destroy();
    header("Location: ../signin-page/serve-signin.php");
    exit();
}

/**
 * Authenticate a user based on email and password.
 * @param string $email User's email
 * @param string $password User's password
 * @return bool True if authentication succeeds, otherwise false.
 */
function authenticateUser($email, $password) {
    global $mysqli;

    // Check if fields are empty
    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill in both email and password.');</script>";
        echo "<script>window.location.href = '../signin-page/serve-signin.php';</script>";
        exit();
    }

    // Check the database for the user
    $query = "SELECT * FROM usertable WHERE email = ? AND password = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Set session variables for the user
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['role'] = $user['AccountType'];

        // Check the AccountType
        if ($user['AccountType'] == 'business') {
            // Redirect to business home page if AccountType is business
            echo "<script>alert('Login successful! Redirecting to business home page.');</script>";
            echo "<script>window.location.href = '../home-page/serve-home-business.php';</script>";
        } else {
            // Redirect to regular home page for other account types
            echo "<script>alert('Login successful! Redirecting to home page.');</script>";
            echo "<script>window.location.href = '../home-page/serve-home.php';</script>";
        }
        return true;
    } else {
        // Login failed
        echo "<script>alert('Incorrect email or password. Please try again.');</script>";
        echo "<script>window.location.href = '../signin-page/serve-signin.php';</script>";
        return false;
    }

    $stmt->close();
    $mysqli->close();
}


?>