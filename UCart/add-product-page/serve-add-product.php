<?php

// Include the database connection
require '../template/connection.php';

include('../template/home-nav.php');

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get the form inputs
    $productName = $_POST['product-name'] ?? '';
    $productPrice = $_POST['product-price'] ?? '';
    $productDescription = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';

    // Ensure that the user is logged in (i.e., the session is active)
    if (!isset($_SESSION['username'])) {
        echo "<script>alert('You need to log in to add a product.');</script>";
        echo "<script>window.location.href = '../signin-page/serve-signin.php';</script>";
        exit();
    }

    // Get the current username from session (ProductBusiness)
    $productBusiness = $_SESSION['username'];

    // Handle the product image upload (if an image is uploaded)
    $productPicture = '';
    if (isset($_FILES['product-picture']) && $_FILES['product-picture']['error'] == 0) {
        // Set the upload directory
        $uploadDir = '../uploads/';
        $uploadFile = $uploadDir . basename($_FILES['product-picture']['name']);
        
        // Validate the uploaded file (check if it's an image)
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        // Check if the file is an image (can be extended to more file types if needed)
        if (getimagesize($_FILES['product-picture']['tmp_name'])) {
            // Move the uploaded file to the desired directory
            if (move_uploaded_file($_FILES['product-picture']['tmp_name'], $uploadFile)) {
                $productPicture = $uploadFile;  // Store the image path for the product
            } else {
                echo "<script>alert('Failed to upload image.');</script>";
            }
        } else {
            echo "<script>alert('Uploaded file is not a valid image.');</script>";
            echo "<script>window.location.href = 'serve-add-product.php';</script>";
            exit();
        }
    }

    // If no image is uploaded, stop the process, alert the user, and return to the form
    if (empty($productPicture)) {
        echo "<script>alert('Please upload a product image.');</script>";
        echo "<script>window.location.href = 'serve-add-product.php';</script>";
        exit();
    }

    // Insert the product into the database if all fields are filled and valid
    if (!empty($productName) && !empty($productPrice) && !empty($productDescription) && !empty($category)) {
        // Prepare the SQL query
        $query = "INSERT INTO ProductTable (ProductBusiness, ProductPicture, ProductName, ProductPrice, ProductDescription, category) 
                  VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssssss", $productBusiness, $productPicture, $productName, $productPrice, $productDescription, $category);

        // Execute the query
        if ($stmt->execute()) {
            echo "<script>alert('Product added successfully!');</script>";
            echo "<script>window.location.href = '../home-page/serve-home-business.php';</script>";
        } else {
            echo "<script>alert('Failed to add product. Please try again.');</script>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all the fields.');</script>";
        echo "<script>window.location.href = 'serve-add-product.php';</script>";
        exit();
    }
}

// Include the form display
include('add-product.html');

// Close the database connection
$mysqli->close();
?>