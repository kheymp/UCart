<?php
// Start session and include required files
require '../template/connection.php';
include('../template/home-nav.php');

// Ensure the user is logged in (optional check)
if (!isset($_SESSION['username'])) {
    header("Location: ../signin-page/serve-signin.php");
    exit();
}

// Initialize category filter variable
$selectedCategories = [];
$searchQuery = ''; // Initialize variable for the search query

// Check if the form is submitted and capture selected categories and search query
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['categories'])) {
        $selectedCategories = $_POST['categories'];  // Store selected categories
    }
    if (isset($_POST['search'])) {
        $searchQuery = $_POST['search'];  // Capture search query
    }
}

// Build the SQL query dynamically based on selected categories and search query
$query = "SELECT * FROM ProductTable";

// If categories are selected, modify the query
if (!empty($selectedCategories)) {
    $categoriesList = implode("', '", $selectedCategories);  // Create a comma-separated list of categories
    $query .= " WHERE Category IN ('$categoriesList')";
}

// If a search query is provided, add it to the query
if (!empty($searchQuery)) {
    // Check if there's already a WHERE clause, and append the search condition accordingly
    if (strpos($query, 'WHERE') !== false) {
        $query .= " AND (ProductName LIKE ? OR ProductDescription LIKE ?)";
    } else {
        $query .= " WHERE (ProductName LIKE ? OR ProductDescription LIKE ?)";
    }
}

// Prepare the statement
$stmt = $mysqli->prepare($query);

// If a search query is provided, bind the parameters
if (!empty($searchQuery)) {
    $searchQueryWithWildcards = "%" . $searchQuery . "%";
    if (!empty($selectedCategories)) {
        // Bind parameters for both categories and search query
        $stmt->bind_param("ss", $searchQueryWithWildcards, $searchQueryWithWildcards);
    } else {
        // Bind parameters only for the search query
        $stmt->bind_param("ss", $searchQueryWithWildcards, $searchQueryWithWildcards);
    }
} else {
    // If no search query, just execute the categories filter (if any)
    $stmt->execute();
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Home</title>
    <link rel="stylesheet" href="../template/styles-general.css">
    <link rel="stylesheet" href="styles-home.css">
</head>
<body>
    <div class="parent-container">
    <div class="filter-container">
    <h1 class="section-title">Search</h1>
        <!-- Form to handle both search and category filter -->
        <form method="POST" action="">
            <div class="search-section">
                <input type="text" class="search-box" name="search" placeholder="Search Products" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="search-button" type="submit">Search</button>
            </div>

            <div class="categories-container">
                <h2 class="section-title">Categories</h2>
                <div class="checkbox-list">
                    <label><input type="checkbox" name="categories[]" value="Electronics" <?php echo in_array('Electronics', $selectedCategories) ? 'checked' : ''; ?>> Electronics</label>
                    <label><input type="checkbox" name="categories[]" value="Fashion" <?php echo in_array('Fashion', $selectedCategories) ? 'checked' : ''; ?>> Fashion</label>
                    <label><input type="checkbox" name="categories[]" value="Wellness" <?php echo in_array('Wellness', $selectedCategories) ? 'checked' : ''; ?>> Wellness</label>
                    <label><input type="checkbox" name="categories[]" value="Furniture" <?php echo in_array('Furniture', $selectedCategories) ? 'checked' : ''; ?>> Furniture</label>
                    <label><input type="checkbox" name="categories[]" value="Gifts" <?php echo in_array('Gifts', $selectedCategories) ? 'checked' : ''; ?>> Gifts</label>
                    <label><input type="checkbox" name="categories[]" value="Foods" <?php echo in_array('Foods', $selectedCategories) ? 'checked' : ''; ?>> Foods</label>
                </div>
            </div>

            <div class="buttons">
                <button class="apply-button" type="submit">Apply</button>
            </div>
        </form>
    </div>

        <div class="product-container">
            <?php
            // Check if any products exist
            if ($result->num_rows > 0) {
                // Loop through the products and display them as cards
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<img src="' . $row['ProductPicture'] . '" alt="' . $row['ProductName'] . '" class="product-image">';
                    echo '<h3 class="product-name">' . $row['ProductName'] . '</h3>';
                    echo '<p class="product-price">₱' . number_format($row['ProductPrice'], 2) . '</p>';
                    echo '<p class="product-category">Category: ' . $row['Category'] . '</p>';
                    echo '<p class="product-seller">Seller: ' . $row['ProductBusiness'] . '</p>';
                    echo '<a href="product-detail-page.php?ProductID=' . $row['ProductID'] . '" class="view-more">View Details</a>';
                    echo '</div>'; // Close the product card
                }
            } else {
                echo '<p>No products available for the selected categories and/or search query.</p>';
            }
            ?>
        </div>
    </div>

</body>
</html>

<?php
// Close the statement and database connection
$stmt->close();
$mysqli->close();
?>