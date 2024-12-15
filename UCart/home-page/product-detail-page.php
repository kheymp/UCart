<?php
// Start session and include the database connection
require '../template/connection.php';
include('../template/home-nav.php');

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../signin-page/serve-signin.php");
    exit();
}

// Get the ProductID from the URL
if (isset($_GET['ProductID'])) {
    $productID = $_GET['ProductID'];

    // Fetch the product details from the database
    $query = "SELECT * FROM ProductTable WHERE ProductID = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the product exists
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "<p>Product not found.</p>";
        exit();
    }
} else {
    echo "<p>Invalid Product ID.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="../template/styles-general.css">
    <link rel="stylesheet" href="styles-product-details.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            padding: 0;
            margin: 0;
            background-image: url('../images/white-wallpeper.jpg'); /* Specify your image here */
            background-size: cover; /* Ensures the image covers the entire screen */
            background-position: center center; /* Centers the image */
            background-attachment: fixed; /* Keeps the image fixed in place while scrolling */
            background-repeat: no-repeat; /* Prevents the image from repeating */
}

       .details-container {
    display: flex;
    gap: 20px;
    padding: 40px;
    margin: 20px auto;
    max-width: 900px;
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Left section: Product image */
.product-image-section {
    flex: 1;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 20px;
    border-right: 1px solid #ddd;
}

.product-image {
    width: 100%;
    max-width: 300px;
    height: auto;
    border-radius: 10px;
    object-fit: cover;
}

/* Right section: Product details */
.product-info-section {
    flex: 2;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.product-info-section h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 10px;
}

.product-info-section p {
    font-size: 1rem;
    color: #555;
    line-height: 1.6;
}

.product-info-section strong {
    font-weight: bold;
    color: #222;
}

/* Responsive styling for mobile */
@media (max-width: 768px) {
    .details-container {
        flex-direction: column;
        padding: 20px;
        max-width: 100%;
    }

    .product-image-section {
        border-right: none;
        border-bottom: 1px solid #ddd;
    }

    .product-info-section {
        padding: 10px;
    }

    .product-info-section h1 {
        font-size: 1.5rem;
    }

    .product-info-section p {
        font-size: 0.9rem;
    }
}
    </style>
</head>
<body>
    <div class="details-container">
        <!-- Left side with product image -->
        <div class="product-image-section">
            <img src="<?php echo $product['ProductPicture']; ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>" class="product-image">
        </div>

        <!-- Right side with product details -->
        <div class="product-info-section">
            <h1><?php echo htmlspecialchars($product['ProductName']); ?></h1>
            <p><strong>Price:</strong> ₱<?php echo number_format($product['ProductPrice'], 2); ?></p>
            <p><strong>Sold by:</strong> <?php echo htmlspecialchars($product['ProductBusiness']); ?></p>
            <p><strong>Description:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($product['ProductDescription'])); ?></p>
        </div>
    </div>
</body>
</html>

<?php
// Close connection
$stmt->close();
$mysqli->close();
?>