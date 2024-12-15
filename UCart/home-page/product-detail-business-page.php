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

if (isset($_POST['delete_product'])) {
    // Delete product from the database
    $deleteQuery = "DELETE FROM ProductTable WHERE ProductID = ?";
    $deleteStmt = $mysqli->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $productID);
    $deleteStmt->execute();
    $deleteStmt->close();

    // Redirect back to the business home page after deletion
    echo "<script>window.location.href = 'serve-home-business.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Business</title>
    <link rel="stylesheet" href="../template/styles-general.css">
    <link rel="stylesheet" href="styles-product-details.css">
    <style>
        /* Reusing customer style for consistency */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
  padding: 0;
  margin: 0;
  background-image: url('../images/2222.jpg'); /* Specify your image here */
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

        /* Buttons for Edit and Delete */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .action-buttons .edit-button,
        .action-buttons .delete-button {
            padding: 10px 20px;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .action-buttons .edit-button {
            background-color: #00bcd4;
            color: white;
        }

        .action-buttons .edit-button:hover {
            background-color: #00a1b2;
        }

        .action-buttons .delete-button {
            background-color: #f44336;
            color: white;
        }

        .action-buttons .delete-button:hover {
            background-color: #e53935;
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

            <!-- Action buttons: Edit and Delete -->
            <div class="action-buttons">
                <a href="edit-product-page.php?ProductID=<?php echo $product['ProductID']; ?>" class="edit-button">Edit</a>
                <!-- Delete Product Form -->
            <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                <button type="submit" name="delete_product" class="delete-button">Delete Product</button>
            </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close connection
$stmt->close();
$mysqli->close();
?>