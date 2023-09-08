<?php
function bindParams($stmt, $params) {
    $types = str_repeat("s", count($params));
    $params_ref = [];
    foreach($params as $key => $value) {
        $params_ref[$key] = &$params[$key];
    }
    array_unshift($params_ref, $types);
    call_user_func_array([$stmt, 'bind_param'], $params_ref);
}

if (isset($_POST['filterType'])) {
    $filterType = $_POST['filterType'];
    $player = isset($_POST['player']) ? $_POST['player'] : '';
    $searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';
    $selectedFilters = isset($_POST['selectedFilters']) ? $_POST['selectedFilters'] : [];
    $params = [];

    $conn = mysqli_connect("127.0.0.1", "Andrew", "Summer99", "data_schema");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if ($filterType == 'sport_id' || $filterType == 'set_name' || $filterType == 'season' || $filterType == 'color_variant' || $filterType == 'variant' || $filterType == 'number') {
        $query = "SELECT DISTINCT $filterType FROM Products WHERE $filterType IS NOT NULL";

        if (!empty($player)) {
            $query .= " AND player_name = ?";
            $params[] = $player;
        }

        if (!empty($searchTerm)) {
            $query .= " AND $filterType LIKE ?";
            $params[] = "%" . $searchTerm . "%";
        }

        if ($filterType == 'set_name') {
            $query .= " ORDER BY $filterType ASC";
            if (empty($searchTerm)) {
                $query .= " LIMIT 200";
            }
        } elseif ($filterType == 'season') {
            $query .= " ORDER BY $filterType DESC";
        } elseif ($filterType == 'variant' || $filterType == 'color_variant' || $filterType == 'number') {
            $query .= " ORDER BY $filterType ASC";
            if (empty($searchTerm)) {
                $query.= " LIMIT 200";
            }
        }

        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            die('mysqli error: '.mysqli_error($conn));
        }

        if (!empty($params)) {
            bindParams($stmt, $params);
        }

        if (!mysqli_stmt_execute($stmt)) {
            die('stmt error: '.mysqli_stmt_error($stmt));
        }

        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        $rowCount = mysqli_num_rows($result);
        echo '<div class="filter-section' . ($filterType == 'season' ? ' filter-section-year' : '') . '">';

        if ($rowCount > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $value = $row[$filterType];
                $nameClass = $filterType == 'sport_id' ? 'filter-sport-name' : ($filterType == 'season' ? 'filter-year' : 'filter-name');
                if ($value !== null) {
                    echo '<label class="filter-label">';
                    echo '<input class="filter-checkbox" type="checkbox" value="' . htmlspecialchars($value) . '">';
                    echo '<div class="filter-info">';
                    echo '  <span class="' . $nameClass . '">' . htmlspecialchars($value) . '</span>';
                    echo '</div>';
                    echo '</label>';
                }
            }
        } else {
            echo 'No Results Found.';
        }

        echo '</div>';
    } else {
        echo "Invalid filter type.";
    }
    mysqli_close($conn);
}
?>


