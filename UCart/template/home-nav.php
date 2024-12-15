<?php
include('../session-handler.php');  // Start the session to access session variables

if (isset($_POST['logout'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        logout();
    }
}

// Determine the redirection target based on the user's role
$homeRedirect = '';
if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    if ($role === 'business') {
        $homeRedirect = '../home-page/serve-home-business.php';
    } else {
        $homeRedirect = '../home-page/serve-home.php';
    }
} else {
    $homeRedirect = '../signin-page/serve-signin.php';  // Default for non-signed-in users
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/favicon.png" type="image/x-icon">
    <title>UCart</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .nav-bar {
            position: sticky;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;

            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f8f9fa;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease; /* Smooth transition for background color */
        }

        .nav-bar .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-container img {
            height: 50px;
            cursor: pointer;
        }

        .logo-label-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Updated color to match the requested design */
        .logo-label-container h1 {
            margin: 0;
            font-size: 1.5rem;
            color: #00264d; /* Dark blue for "UCart" title */
        }

        .logo-label-container h5 {
            margin: 0;
            font-size: 0.8rem;
            color: #00ADB5; /* Teal for "Product Listing" subtitle */
        }

        .nav-selection {
            display: flex;
            gap: 20px;
        }

        .nav-selection h4 {
            margin: 0;
            cursor: pointer;
            font-size: 1rem;
            color: #333;
            transition: color 0.3s ease, transform 0.3s ease; /* Smooth transition for color and scale */
        }

        .nav-selection h4:hover {
            color: #007bff;
            transform: scale(1.1); /* Slight enlargement of navigation links on hover */
        }

        .button-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .signout-button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: bold;
            cursor: pointer;
            background-color: #dc3545;
            color: white;
            transition: background-color 0.3s ease, transform 0.3s ease; /* Transition for background color and scale */
        }

        .signout-button:hover {
            background-color: #c82333;
            transform: scale(1.05); /* Slight enlargement of the signout button */
        }

        .greetings {
            font-size: 0.9rem;
            color: #333;
        }

        @media (max-width: 768px) {
            .nav-bar {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .nav-selection {
                flex-wrap: wrap;
                gap: 15px;
            }

            .logo-label-container h1 {
                font-size: 1.2rem;
            }

            .logo-label-container h5 {
                font-size: 0.7rem;
            }

            .signout-button {
                padding: 8px 10px;
                font-size: 0.8rem;
            }

            .greetings {
                text-align: center;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="nav-bar">
    <!-- Logo Section -->
    <div class="logo-container">
        <img src="../images/logo-canva2.png" alt="logo" onclick="window.location.href='<?php echo $homeRedirect; ?>';">
        <div class="logo-label-container">
            <h1>UCart</h1>
            <h5>Product Listing</h5>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="nav-selection">
        <h4 onclick="window.location.href='<?php echo $homeRedirect; ?>';">Home</h4>
        <h4 onclick="alert('About Page Coming Soon!');">About</h4>
        <h4 onclick="alert('Contact Page Coming Soon!');">Contact Us</h4>
    </div>

    <!-- User Greetings and Sign Out -->
    <div class="button-container">
        <div class="greetings">
            <?php
            if (isset($_SESSION['username'])) {
                echo "Hello, " . htmlspecialchars($_SESSION['username']);
            } else {
                echo "Hello, Guest";
            }
            ?>
        </div>
        <form method="POST">
            <button type="submit" name="logout" class="signout-button">SIGN OUT</button>
        </form>
    </div>
</div>

</body>
</html>
