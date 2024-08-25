<?php

// just type in root directory of the project using terminal
// to run the usage-example.php, as below
// php examples/usage-example.php

require_once __DIR__ . '/../vendor/autoload.php';

use LOTR\LOTRSdk;

try {
    // Initialize the SDK with the API key
    $sdk = LOTRSdk::create('Wi99sev9c2yM1_wYZYc8');

    // Define filters and pagination for movies
    $filters = [
        ['key' => 'name', 'filter_type' => 'match', 'value' => 'The Return of the King'],
        ['key' => 'academyAwardWins', 'filter_type' => 'exists'],
        ['key' => 'budgetInMillions', 'filter_type'=> 'match', 'value' => '94']
    ];
    $limit = 5;
    $page = 1;

    // Set filters and pagination for movies using the client getter
    $sdk->getClient()->setFilters($filters);
    $sdk->getClient()->setPagination($limit, $page);

    // Fetch movies with filters and pagination
    $movies = $sdk->movies->getAllMovies();

    // Display the movies
    if (isset($movies['docs']) && count($movies['docs']) > 0) {
        foreach ($movies['docs'] as $movie) {
            echo "Movie Name: " . $movie['name'] . PHP_EOL;

            // Fetch and display quotes for each movie
            $movieId = $movie['_id'];
            $movieQuotes = $sdk->movies->getMovieQuotes($movieId);

            if (isset($movieQuotes['docs']) && count($movieQuotes['docs']) > 0) {
                echo "Quotes for Movie: " . $movie['name'] . PHP_EOL;
                foreach ($movieQuotes['docs'] as $quote) {
                    echo "- " . $quote['dialog'] . PHP_EOL;
                }
            } else {
                echo "No quotes found for this movie." . PHP_EOL;
            }
        }
    } else {
        echo "No movies found matching the filters." . PHP_EOL;
    }
    
    // fetch specific quotes
    // Define filters and pagination for quotes
    $quoteFilters = [
        ['key' => 'dialog', 'filter_type' => 'regex_match', 'value' => 'Why?']
    ];
    $quoteLimit = 10;
    $quotePage = 1;

    // Set filters and pagination for quotes using the client getter
    $sdk->getClient()->setFilters($quoteFilters);
    $sdk->getClient()->setPagination($quoteLimit, $quotePage);

    // Fetch quotes with filters and pagination
    $allQuotes = $sdk->quotes->getAllQuotes();

    // Display quotes
    if (isset($allQuotes['docs']) && count($allQuotes['docs']) > 0) {
        echo PHP_EOL . "Filtered Quotes (matching 'Why?'):" . PHP_EOL;
        foreach ($allQuotes['docs'] as $quote) {
            echo "- " . $quote['dialog'] . PHP_EOL;
        }
    } else {
        echo "No quotes found matching the filter." . PHP_EOL;
    }

    // a specific quote by ID
    $quoteId = '5cd96e05de30eff6ebcce7e9'; // Example quote ID
    $specificQuote = $sdk->quotes->getQuoteById($quoteId);
    
    // Display specific quote
    if (isset($specificQuote['docs'][0]['dialog'])) {
        echo PHP_EOL . "Specific Quote (ID: $quoteId): " . $specificQuote['docs'][0]['dialog'] . PHP_EOL;
    } else {
        echo "Quote not found." . PHP_EOL;
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
