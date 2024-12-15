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

// Handle form submission to update the product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    // Handle image upload if a new image is selected
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imagePath = '../uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    } else {
        // Keep the existing image if no new image is uploaded
        $imagePath = $product['ProductPicture'];
    }

    // Update the product in the database
    $updateQuery = "UPDATE ProductTable SET ProductName = ?, ProductPrice = ?, ProductDescription = ?, Category = ?, ProductPicture = ? WHERE ProductID = ?";
    $stmt = $mysqli->prepare($updateQuery);
    $stmt->bind_param("sssssi", $name, $price, $description, $category, $imagePath, $productID);

    if ($stmt->execute()) {
        // Redirect back to the business homepage after successful update
        echo "<script>window.location.href = 'serve-home-business.php';</script>";
        exit();
    } else {
        echo "<p>Error updating product.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../template/styles-general.css">
    <link rel="stylesheet" href="styles-edit-product.css">
    <style>
        /* Styling similar to the edit product form */

        body {
  padding: 0;
  margin: 0;
  background-image: url('../images/2222.jpg'); /* Specify your image here */
  background-size: cover; /* Ensures the image covers the entire screen */
  background-position: center center; /* Centers the image */
  background-attachment: fixed; /* Keeps the image fixed in place while scrolling */
  background-repeat: no-repeat; /* Prevents the image from repeating */
}

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        .form-container {
            width: 80%;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h1 {
            text-align: center;
            color: #333;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .button-container {
        text-align: center;
        margin-top: 20px; /* Add some space above the buttons */
    }

    .submit-button {
    background-color: #6c63ff;
    color: white;
    padding: 12px 20px;
    font-size: 1rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-right: 20px; /* Add some space between the buttons */
}

.submit-button:hover {
    background-color: #5a53d7;
}

.cancel-button {
    background-color: #f44336;
    color: white;
    padding: 12px 20px;
    font-size: 1rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.cancel-button:hover {
    background-color: #d32f2f;
}
    </style>
</head>
<body>

    <div class="form-container">
        <h1>Edit Product</h1>

        <form method="POST" action="edit-product-page.php?ProductID=<?php echo $productID; ?>" enctype="multipart/form-data">
            <!-- Product Name -->
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['ProductName']); ?>" required>
            </div>

            <!-- Price -->
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" value="<?php echo $product['ProductPrice']; ?>" required>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($product['ProductDescription']); ?></textarea>
            </div>

            <!-- Category -->
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="Electronics" <?php echo $product['Category'] == 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                    <option value="Fashion" <?php echo $product['Category'] == 'Fashion' ? 'selected' : ''; ?>>Fashion</option>
                    <option value="Wellness" <?php echo $product['Category'] == 'Wellness' ? 'selected' : ''; ?>>Wellness</option>
                    <option value="Furniture" <?php echo $product['Category'] == 'Furniture' ? 'selected' : ''; ?>>Furniture</option>
                    <option value="Gifts" <?php echo $product['Category'] == 'Gifts' ? 'selected' : ''; ?>>Gifts</option>
                    <option value="Foods" <?php echo $product['Category'] == 'Foods' ? 'selected' : ''; ?>>Foods</option>
                </select>
            </div>

            <!-- Product Image -->
            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" id="image" name="image">
                <small>Leave blank if you don't want to change the image</small>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="button-container">
                <button type="submit" class="submit-button">Update Product</button>
                <button type="button" class="cancel-button" onclick="window.location.href='serve-home-business.php'">Cancel</button>
            </div>
        </form>
    </div>

</body>
</html>

<?php
// Close the database connection
$stmt->close();
$mysqli->close();
?>