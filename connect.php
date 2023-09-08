<?php
// Database credentials
$hostname = "127.0.0.1"; // Usually "localhost"
$username = "Andrew";
$password = "Summer99";
$database = "data_schema";

$conn = mysqli_connect($hostname, $username, $password, $database);


// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Pagination variables
$itemsPerPage = 24;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Step 1: Execute a Query to get one variation (e.g., grade 10) of each card with pagination
$sqlProducts = "SELECT * FROM Products WHERE grade = 10 AND product_id IN (SELECT MIN(product_id) FROM Products WHERE grade = 10 GROUP BY player_name) LIMIT $offset, $itemsPerPage";
$resultProducts = mysqli_query($conn, $sqlProducts);

// Step 2: Get total number of products for pagination
$sqlTotalProducts = "SELECT COUNT(DISTINCT player_name) as total FROM Products WHERE grade = 10";
$resultTotalProducts = mysqli_query($conn, $sqlTotalProducts);
$rowTotalProducts = mysqli_fetch_assoc($resultTotalProducts);
$totalProducts = $rowTotalProducts['total'];

// Calculate total number of pages
$totalPages = ceil($totalProducts / $itemsPerPage);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Products Page</title>
</head>
<body>
    <h1>Products</h1>

    <!-- Display one variation of each card -->
    <ul>
        <?php while ($row = mysqli_fetch_assoc($resultProducts)) : ?>
            <li>
                <?php echo $row['player_name']; ?> - Grade <?php echo $row['grade']; ?>
                <a href="view_variations.php?player_name=<?php echo $row['player_name']; ?>">View All Variations</a>
            </li>
        <?php endwhile; ?>
    </ul>

    <!-- Pagination links -->
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <li>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>

    <?php
    // Close the database connection
    mysqli_close($conn);
    ?>
</body>
</html>
