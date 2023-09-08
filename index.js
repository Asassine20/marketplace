$(document).ready(function () {
    // Initial fetching
    fetchFilters('sport_id', '#sport-filter');
    fetchFilters('sport_id', '#sport-inline-filter');
    fetchFilters('set_name', '#set-name-filter');
    fetchFilters('season', '#year-filter');
    fetchFilters('color_variant', '#color-variant-filter');
    fetchFilters('variant', '#variant-filter');
    fetchFilters('number', '#number-filter');

    // Event listener for form submission
    $('#search-form').on('submit', function(event) {
        event.preventDefault();
        const player = $('#player').val();
        search(player);
        fetchFilters('sport_id', '#sport-filter', player);
        fetchFilters('set_name', '#set-name-filter', player);
        fetchFilters('season', '#year-filter', player);
        fetchFilters('color_variant', '#color-variant-filter', player);
        fetchFilters('variant', '#variant-filter', player);
        fetchFilters('number', '#number-filter', player);
    });

    // Function to display selected filters
    function displaySelectedFilters() {
        let selectedFiltersHtml = '';
        const selectedSports = getCheckedFilters('#sport-filter');
        const selectedSetNames = getCheckedFilters('#set-name-filter');
        const selectedYears = getCheckedFilters('#year-filter');
        const selectedColorVariants = getCheckedFilters('#color-variant-filter');
        const selectedVariants = getCheckedFilters('#variant-filter');
        const selectedCardNumbers = getCheckedFilters('#number-filter');

        const totalSelectedFilters = selectedSports.length + selectedSetNames.length + selectedYears.length + selectedColorVariants.length + selectedVariants.length + selectedCardNumbers.length;

        if (totalSelectedFilters > 0) {
            $("div.sidebar > h3:first-child").show();
            selectedFiltersHtml += generateSelectedFiltersHtml(selectedSports, 'sport');
            selectedFiltersHtml += generateSelectedFiltersHtml(selectedSetNames, 'set_name');
            selectedFiltersHtml += generateSelectedFiltersHtml(selectedYears, 'season');
            selectedFiltersHtml += generateSelectedFiltersHtml(selectedColorVariants, 'color_variant');
            selectedFiltersHtml += generateSelectedFiltersHtml(selectedVariants, 'variant');
            selectedFiltersHtml += generateSelectedFiltersHtml(selectedCardNumbers, 'number');
        } else {
            $("div.sidebar > h3:first-child").hide();
        }
        
        $('#selected-filters').html(selectedFiltersHtml);
    }

    // Generate HTML for selected filters
    function generateSelectedFiltersHtml(filtersArray, filterType) {
        return filtersArray.map(function (filterValue) {
            return `<span class="selected-filter" data-filter-type="${filterType}" data-filter-value="${filterValue}">${filterValue} <span class="remove-filter">x</span></span>`;
        }).join('');
    }

    // Function to get checked filters
    function getCheckedFilters(selector) {
        return $(`${selector} input:checked`).map(function() { return $(this).val(); }).get();
    }

    // Mapping from sport_id to its top sets
    const topSetsMapping = {
        'Baseball': ['Topps', 'Bowman', 'Upper Deck', 'Bowman Chrome', 'Topps Chrome', 'Topps Heritage'],
        'Basketball': ['Hoops', 'Select', 'Prizm', 'Donruss'],
        'Football': ['Prizm', 'Select', 'Mosaic', 'Donruss', 'Score'],
        'Hockey': ['O-Pee-Chee', 'Upper Deck', 'Score', 'MVP', 'Parkhurst'],
    };


    // Dropdown toggle on button click
    $('#sport-inline-filter').on('click', '.sport-button', function() {
        const dropdown = $(this).next('.inline-sport-dropdown');
        dropdown.css('pointer-events', 'auto');
        dropdown.focus();
    });

    $('#sport-inline-filter').on('click', '.shop-all', function() {
        const sportId = $(this).data('sport-id');
        fetchSearchResults('', sportId);
    });

    // Close dropdown when it loses focus
    $('#sport-inline-filter').on('blur', '.inline-sport-dropdown', function() {
        $(this).css('pointer-events', 'none');
    });

    // Event listeners for checkbox changes
    $('#sport-filter, #set-name-filter, #year-filter, #variant-filter, #color-variant-filter, #number-filter').on('change', 'input[type="checkbox"]', function() {
        search();
        displaySelectedFilters();
    });

    // Remove selected filters when clicked
    $('#selected-filters').on('click', '.remove-filter', function() {
        const filterType = $(this).parent().data('filter-type');
        const filterValue = $(this).parent().data('filter-value');

        $(`#${filterType}-filter input[value="${filterValue}"]`).prop('checked', false);
        displaySelectedFilters();
        search();
    });

    // Search function
    function search(player) {
        if (typeof player === 'undefined') {
            player = $('#player').val();
        }
        const selectedSports = getCheckedFilters('#sport-filter');
        const selectedSetNames = getCheckedFilters('#set-name-filter');
        const selectedYears = getCheckedFilters('#year-filter');
        const selectedColorVariants = getCheckedFilters('#color-variant-filter');
        const selectedVariants = getCheckedFilters('#variant-filter');
        const selectedCardNumbers = getCheckedFilters('#number-filter');
        fetchSearchResults(player, selectedSports, selectedSetNames, selectedYears, selectedColorVariants, selectedVariants, selectedCardNumbers, 1);
    }
    function attachSearchEventListener(inputId, filter, filterElementId, minSearchTermLength) {
        $(inputId).on('input', function() {
            const searchTerm = $(this).val();
            const player = $('#player').val();
            const searchQuery = searchTerm.length >= minSearchTermLength ? searchTerm : '';
            fetchFilters(filter, filterElementId, player, searchQuery);
        });
    }

    attachSearchEventListener('#setSearch', 'set_name', '#set-name-filter', 4);
    attachSearchEventListener('#yearSearch', 'season', '#year-filter', 4);
    attachSearchEventListener('#variantSearch', 'variant', '#variant-filter', 4);
    attachSearchEventListener('#color-variantSearch', 'color_variant', '#color-variant-filter', 3);
    attachSearchEventListener('#numberSearch', 'number', '#number-filter', 1);




    // Fetch filters function
    function fetchFilters(filterType, targetElement, player = '', searchTerm = '') {
        $.ajax({
            url: 'filters.php',
            method: 'POST',
            data: { filterType: filterType, player: player, searchTerm: searchTerm },
            dataType: 'html',
            success: function (response) {
                if (targetElement ==='#sport-inline-filter') {
                    const cleanedResponse = cleanSportInlineFilterHtml(response);
                    $(targetElement).html(cleanedResponse);
                } else {
                    $(targetElement).html(response);
                }
            },
            error: function(error) {
                console.log("Error: ", error);
            }
        });
    }

    function cleanSportInlineFilterHtml(rawHtml) {
        let dropdownsHtml = '';
        const parser = new DOMParser();
        const doc = parser.parseFromString(rawHtml, 'text/html');
        doc.querySelectorAll('input[type="checkbox"]').forEach(function(el) {
            const value = el.value;
            const label = el.nextSibling.textContent.replace(/\(\d+\)/, '').trim();
            dropdownsHtml += `<div class="inline-sport-dropdown-container"><button class="sport-button">${label}<i class="fa fa-angle-down" aria-hidden="true"></i></button>`;
            dropdownsHtml += `<div class="submenu"><div class="top-sets">`;
            dropdownsHtml += '<h4>Top Sets</h4>';
            const topSets = topSetsMapping[label] || [];
            topSets.forEach(set => {
                dropdownsHtml += `<p class="set-name-clickable" data-set-name="${set}">${set}</p>`;
            });
            dropdownsHtml += `</div><button class="shop-all" data-sport-id="${value}">Shop All</button></div></div>`;
        });
        return dropdownsHtml;
    }


    // New code to handle set name click events
    $('#sport-inline-filter').on('click', '.set-name-clickable', function() {
        const setName = $(this).data('set-name');
        // Uncheck all checkboxes in set-name-filter
        $('#set-name-filter input[type="checkbox"]').prop('checked', false);
        // Check only the checkbox corresponding to the clicked set name
        $(`#set-name-filter input[value="${setName}"]`).prop('checked', true);
        search();
        displaySelectedFilters();
    });

    // Fetch search results function
    function fetchSearchResults(player, sport_id = '', set_name = '', selectedYears = '', selectedColorVariants = '', selectedVariants = '', selectedCardNumbers = '', page = 1) {
        $('.loading-spinner').show();
        $.ajax({
            url: 'search.php',
            method: 'POST',
            data: { player: player, sport_id: sport_id, set_name: set_name, season: selectedYears, color_variant: selectedColorVariants, number: selectedCardNumbers, variant: selectedVariants, page: page },
            dataType: 'html',
            success: function (response) {
                $('#search-results').html(response);
                $('.loading-spinner').hide();
            },
            error: function () {
                $('.loading-spinner').hide();
            }
        });
    }

    const toggleButton = document.querySelector(".toggle-button");
    const sidebar = document.querySelector(".sidebar");

    // Initialize the sidebar display state based on initial window size
    if (window.innerWidth <= 899) {
        sidebar.style.display = "none";
    } else {
        sidebar.style.display = "block";
    }

// Update the sidebar and inline filter display state when the window is resized
window.addEventListener("resize", function() {
    // Existing logic for sidebar
    if (window.innerWidth <= 899) {
        sidebar.style.display = "none";
    } else {
        sidebar.style.display = "block";
    }

    // New logic for inline-filter
    const inlineFilter = document.getElementById("sport-inline-filter");
    if (window.innerWidth <= 1300) {
        inlineFilter.style.display = "none";
    } else {
        inlineFilter.style.display = "flex";
        }
    });

    
    toggleButton.addEventListener("click", function() {
        if (sidebar.style.display === "none" || sidebar.style.display === "") {
            sidebar.style.display = "block";
        } else {
            sidebar.style.display = "none";
        }
    });


    // Initialize search from URL parameters
    var urlParams = new URLSearchParams(window.location.search);
    var player = urlParams.get('player');
    var page = urlParams.get('page') || 1;

    if (player) {
        $('#player').val(player);
        fetchSearchResults(player, '', '', '', page);
    }
});

