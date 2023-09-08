<?php

// Connect to the database
$conn = mysqli_connect("127.0.0.1", "Andrew", "Summer99", "data_schema");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to get product details by product_id
function getProductById($conn, $productId) {
    $sql = "SELECT * FROM Products WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $productId);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// Check if the product_id is provided in the URL
if (isset($_GET['product_id'])) {
    // Get the product_id from the URL parameter
    $productId = $_GET['product_id'];

    // Fetch product details
    $productResult = getProductById($conn, $productId);
    $productDetails = mysqli_fetch_assoc($productResult);

    // Display product details (e.g., player_name, set_name, etc.)
    echo '<h1>' . $productDetails['player_name'] . ' - ' . $productDetails['set_name'] . ' - ' . $productDetails['sport_id'] . ' - ' . $productDetails['number'] . '</h1>';

    // Now fetch listings for the card with different grades using $productDetails['player_name'] and $productDetails['set_name']

    // ... Fetch and display listings ...

} else {
    // Product ID not provided, handle the error or redirect to a default page
    echo 'Product not found.';
}

// Close the database connection
mysqli_close($conn);

?>
