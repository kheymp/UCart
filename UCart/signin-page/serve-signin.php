<?php
// Set the content type to HTML
header('Content-Type: text/html');
require '../template/connection.php';

if (isset($_POST['submit'])) {

    require '../session-handler.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form inputs
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
    
        // Authenticate user
        authenticateUser($email, $password);
    }
}

// Read and output the contents of index.html
echo file_get_contents('../template/nav-bar.html');
echo file_get_contents('signin.html');
?>