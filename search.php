<?php
// Function to bind parameters
function bindParams($stmt, $params){
    if (!empty($params)) {
        $paramTypes = str_repeat('s', count($params));
        $paramReferences = [];
        foreach ($params as $k => &$param){
            $paramReferences[$k] = &$param;
        }
        mysqli_stmt_bind_param($stmt, $paramTypes, ...$paramReferences);
    }
}
// Function to get the total number of rows in the result set with sport_id and set_name filters
function getTotalRowsForPlayerNameAndSport($conn, $player, $sport_id, $set_name, $season, $color_variant, $variant, $number) {
    $sql = "SELECT COUNT(*) FROM Products WHERE 1 = 1";
    $params = [];

    if (!empty($player)) {
        $sql .= " AND player_name = ?";
        $params[] = $player;
    }

    if (!empty($sport_id)) {
        $sql .= " AND sport_id IN (" . implode(',', array_fill(0, count($sport_id), '?')) . ")";
        $params = array_merge($params, $sport_id);
    }

    if (!empty($set_name)) {
        $sql .= " AND set_name IN (" . implode(',', array_fill(0, count($set_name), '?')) . ")";
        $params = array_merge($params, $set_name);
    }

    if (!empty($season)) {
        $sql .= " AND season IN (" . implode(',', array_fill(0, count($season), '?')) . ")";
        $params = array_merge($params, $season);
    }

    if (!empty($color_variant)) {
        $sql .= " AND color_variant IN (" . implode(',', array_fill(0, count($color_variant), '?')) . ")";
        $params = array_merge($params, $color_variant);
    }

    if (!empty($variant)) {
        $sql .= " AND variant IN (" . implode(',', array_fill(0, count($variant), '?')) . ")";
        $params = array_merge($params, $variant);
    }

    if (!empty($number)) {
        $sql .= " AND number IN (" . implode(',', array_fill(0, count($number), '?')) . ")";
        $params = array_merge($params, $number);
    }

    $stmt = mysqli_prepare($conn, $sql);
    bindParams($stmt, $params);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $totalRows);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $totalRows;
}

// Function to fetch products with pagination for player_name search and sport_id and set_name filters
function getProductsPerPageForPlayerNameAndSport($conn, $offset, $limit, $player, $sport_id, $set_name, $season, $color_variant, $variant, $number) {
    $sql = "SELECT * FROM Products WHERE grade = 10";
    $params = [];

    if (!empty($player)) {
        $sql .= " AND player_name = ?";
        $params[] = $player;
    }

    if (!empty($sport_id)) {
        $sql .= " AND sport_id IN (" . implode(',', array_fill(0, count($sport_id), '?')) . ")";
        $params = array_merge($params, $sport_id);
    }

    if (!empty($set_name)) {
        $sql .= " AND set_name IN (" . implode(',', array_fill(0, count($set_name), '?')) . ")";
        $params = array_merge($params, $set_name);
    }

    if (!empty($season)) {
        $sql .= " AND season IN (" . implode(',', array_fill(0, count($season), '?')) . ")";
        $params = array_merge($params, $season);
    }

    if (!empty($color_variant)) {
        $sql .= " AND color_variant IN (" . implode(',', array_fill(0, count($color_variant), '?')) . ")";
        $params = array_merge($params, $color_variant);
    }

    if (!empty($variant)) {
        $sql .= " AND variant IN (" . implode(',', array_fill(0, count($variant), '?')) . ")";
        $params = array_merge($params, $variant);
    }

    if (!empty($number)) {
        $sql.= " AND number IN (". implode(',', array_fill(0, count($number), '?')). ")";
        $params = array_merge($params, $number);
    }
    
    $sql .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $limit;

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }

    // Bind parameters based on the existence of search term, sport ID, and set name
    bindParams($stmt, $params);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// Check if the form is submitted with a player, sport_id, set_name, season, or color_variant value
if (isset($_POST['player']) || isset($_POST['sport_id']) || isset($_POST['set_name']) || isset($_POST['season']) || isset($_POST['color_variant']) || isset($_POST['variant']) || isset($_POST['number'])) {
    // Get the selected search term, sport_id, set_name, season, and color_variant from the form
    $player = isset($_POST['player']) ? $_POST['player'] : '';
    $sport_id = isset($_POST['sport_id']) && is_array($_POST['sport_id']) ? $_POST['sport_id'] : [];
    $set_name = isset($_POST['set_name']) && is_array($_POST['set_name']) ? $_POST['set_name'] : [];
    $season = isset($_POST['season']) && is_array($_POST['season']) ? $_POST['season'] : []; 
    $color_variant = isset($_POST['color_variant']) && is_array($_POST['color_variant']) ? $_POST['color_variant'] : [];
    $variant = isset($_POST['variant']) && is_array($_POST['variant']) ? $_POST['variant'] : [];
    $number = isset($_POST['number']) && is_array($_POST['number']) ? $_POST['number'] : [];
    

    // Connect to the database
    $conn = mysqli_connect("127.0.0.1", "Andrew", "Summer99", "data_schema");

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Fetch products with the selected player_name, sport_id, and set_name from the Products table
    $totalRows = getTotalRowsForPlayerNameAndSport($conn, $player, $sport_id, $set_name, $season, $color_variant, $variant, $number);

    // Pagination settings
    $perPage = 40;
    $totalPages = ceil($totalRows / $perPage / 10);
    $currentPage = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
    $offset = ($currentPage - 1) * $perPage;

    // Fetch products with pagination
    $result = getProductsPerPageForPlayerNameAndSport($conn, $offset, $perPage, $player, $sport_id, $set_name, $season, $color_variant, $variant, $number);

    // Display the products in a grid of cards
    $sport_id_str = is_array($sport_id) ? implode(', ', $sport_id) : $sport_id;
    $set_name_str = is_array($set_name) ? implode(', ', $set_name) : $set_name;
    $year_str = is_array($season) ? implode(', ', $season) : $season;
    $variant_str = is_array($variant) ? implode(', ', $variant) : $variant;
    $colorVariant_str = is_array($color_variant) ? implode(', ', $color_variant) : $color_variant;

    $filters = [];
    if ($player) $filters[] = $player;
    if ($sport_id_str) $filters[] = $sport_id_str;
    if ($set_name_str) $filters[] = $set_name_str;
    if ($year_str) $filters[] = $year_str;
    if ($variant_str) $filters[] = $variant_str;
    if ($colorVariant_str) $filters[] = $colorVariant_str;

    $filter_str = implode(', ', $filters);

    echo '<div class="resultsAndSort">';
    echo '<span class="resultsText">' . ($totalRows / 10) . ' results for ' . ($filter_str ? $filter_str : 'All Products') . '</span>';

    echo '<div class="custom-select-wrapper">';
    echo '  <div class="custom-select">';
    echo '    <select id="sortMenu" name="sort" style="margin-left: 10px;">';
    echo '      <option value="default">Sort By</option>';
    echo '      <option value="az">A-Z</option>';
    echo '      <option value="za">Z-A</option>';
    echo '      <option value="bestSelling">Best Selling</option>';
    echo '      <option value="highestPrice">Highest Price</option>';
    echo '      <option value="lowestPrice">Lowest Price</option>';
    echo '    </select>';
    echo '  </div>';
    echo '</div>';
    
    echo '</div>';
    echo '<div class="card-grid">';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<a href="product.php?product_id=' . $row['product_id'] . '">';
        echo '<div class="card">';
        echo '<div class="card-img-container">';
        echo '<img class="card-img" src="PSA10shohei.jpeg" alt="Card Image">';
        echo '</div>'; // end card-img-container
        echo '<div class="card-content">';
        echo '<div class="card-detail">' . $row['sport_id'] . '</div>';
        echo '<div class="card-detail">' . $row['season'] . '</div>';
        echo '<div class="card-detail">' . $row['set_name'] . ' #' . $row['number'] . '</div>';
        echo '<div class="player-name card-detail">' . $row['player_name'] . '</div>';
        echo '<div class="market_price card-detail">Market Price: ' . $row['market_price'] . '</div>';

        echo '</div>'; // end card-content
        echo '</div>'; // end card
        echo '</a>';

    }
    
    
// Pagination links
echo '<ul class="pagination">';
// First page link
if ($currentPage > 1) {
    echo '<li><a href="?page=1';
    if($player) echo '&player=' . rawurlencode($player);
    if($sport_id) echo '&sport_id=' . rawurlencode(implode(',', $sport_id));
    if($set_name) echo '&set_name=' . rawurlencode(implode(',', $set_name));
    if($season) echo '&season=' . rawurlencode(implode(',', $season));
    if($variant) echo '&variant=' . rawurlencode(implode(',', $variant));
    if($color_variant) echo '&color_variant=' . rawurlencode(implode(',', $color_variant));
    echo '#search-results">&lt;&lt;</a></li>';
}

// Previous page link
if ($currentPage > 1) {
    echo '<li><a href="?page=' . ($currentPage - 1);
    if($player) echo '&player=' . rawurlencode($player);
    if($sport_id) echo '&sport_id=' . rawurlencode(implode(',', $sport_id));
    if($set_name) echo '&set_name=' . rawurlencode(implode(',', $set_name));
    if($season) echo '&season=' . rawurlencode(implode(',', $season));
    if($variant) echo '&variant=' . rawurlencode(implode(',', $variant));
    if($color_variant) echo '&color_variant=' . rawurlencode(implode(',', $color_variant));
    echo '#search-results">&lt;</a></li>';
}

// Page numbers
for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
    echo '<li><a href="?page=' . $i;
    if($player) echo '&player=' . rawurlencode($player);
    if($sport_id) echo '&sport_id=' . rawurlencode(implode(',', $sport_id));
    if($set_name) echo '&set_name=' . rawurlencode(implode(',', $set_name));
    if($season) echo '&season=' . rawurlencode(implode(',', $season));
    if($variant) echo '&variant=' . rawurlencode(implode(',', $variant));
    if($color_variant) echo '&color_variant=' . rawurlencode(implode(',', $color_variant));
    echo '"';
    if ($i === $currentPage) {
        echo ' class="current-page"';
    }
    echo '>' . $i . '</a></li>';
}

// Next page link
if ($currentPage < $totalPages) {
    echo '<li><a href="?page=' . ($currentPage + 1);
    if($player) echo '&player=' . rawurlencode($player);
    if($sport_id) echo '&sport_id=' . rawurlencode(implode(',', $sport_id));
    if($set_name) echo '&set_name=' . rawurlencode(implode(',', $set_name));
    if($season) echo '&season=' . rawurlencode(implode(',', $season));
    if($variant) echo '&variant=' . rawurlencode(implode(',', $variant));
    if($color_variant) echo '&color_variant=' . rawurlencode(implode(',', $color_variant));
    echo '#search-results">&gt;</a></li>';
}

// Last page link
if ($currentPage < $totalPages) {
    echo '<li><a href="?page=' . $totalPages;
    if($player) echo '&player=' . rawurlencode($player);
    if($sport_id) echo '&sport_id=' . rawurlencode(implode(',', $sport_id));
    if($set_name) echo '&set_name=' . rawurlencode(implode(',', $set_name));
    if($season) echo '&season=' . rawurlencode(implode(',', $season));
    if($variant) echo '&variant=' . rawurlencode(implode(',', $variant));
    if($color_variant) echo '&color_variant=' . rawurlencode(implode(',', $color_variant));
    echo '#search-results">&gt;&gt;</a></li>';
}

echo '</ul>';

// Close the database connection
mysqli_close($conn);
}
?>
