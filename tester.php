<!DOCTYPE html>
<html>
<head>
    <title>Product Search</title>
    <script src="https://kit.fontawesome.com/700da87b78.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"> 

</head>
<body>
    <div class="content">
        <form method="post" class="search-form" id="search-form">
            <div class="search-bar">
                <input type="text" name="player" id="player" placeholder="Search for a Player...">
                <button type="submit" class="search-icon"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <div id="sport-inline-filter" class="inline-filter"></div> <!-- New div for sport_id inline filters -->
        <div class="main-container">
            <div class="sidebar">
                <h3 style="display: none;">Selected Filters</h3>
                <div id="selected-filters"></div>
                <h3>Sport</h3>
                <div class="filter-section" id="sport-filter"></div>
                <hr class="filter-divider">
                <h3>Set</h3>
                <input type="text" id="setSearch" placeholder="Search sets...">
                <div class="filter-section" id="set-name-filter"></div> 
                <hr class="filter-divider">
                <h3>Year</h3>
                <input type="text" id="yearSearch" placeholder="Search Years...">
                <div class="filter-section" id="year-filter"></div>
                <hr class="filter-divider">
                <h3>Variant</h3>
                <input type="text" id="variantSearch" placeholder="Search Variants...">
                <div class="filter-section" id="variant-filter"></div>
                <hr class="filter-divider">
                <h3>Color Variant</h3>
                <input type="text" id="color-variantSearch" placeholder="Search Color Variants...">
                <div class="filter-section" id="color-variant-filter"></div>
                <hr class="filter-divider">
                <h3>Card Number</h3>
                <input type="text" id="numberSearch" placeholder="Search Numbers...">
                <div class="filter-section" id="number-filter"></div>
            </div>

            <div id="search-results"></div>
        </div>
    </div>

    <button class="toggle-button">Filter</button>

    <div class="loading-spinner" style="display: none;"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="index.js"></script>

</body>
</html>