# Lord of the Rings PHP SDK
## Installation
### Requirements
* PHP 8.3 or higher
* Composer
* cURL extension (ext-curl)
* JSON extension (ext-json)
### Step 1: Install Dependencies
To install the dependencies for this project, make sure Composer is installed. Then, run the following command:
```
composer install
```
### Step 2: Configure the SDK
Initialize the SDK by passing your API key.
```
require_once __DIR__ . '/vendor/autoload.php';

use LOTR\LOTRSdk;

// Initialize the SDK with your API key
$sdk = LOTRSdk::create('your-api-key-here');
```
### Sample Usage
```
$filters = [
    ['key' => 'name', 'filter_type' => 'match', 'value' => 'The Return of the King'],
    ['key' => 'academyAwardWins', 'filter_type' => 'exists'],
    ['key' => 'budgetInMillions', 'filter_type' => '>=', 'value' => '94']
];

// Set filters and pagination
$sdk->getClient()->setFilters($filters);
$sdk->getClient()->setPagination(5, 1); // Limit 5, Page 1

// Fetch movies
$movies = $sdk->movies->getAllMovies();

// Display the result
if (!empty($movies['docs'])) {
    foreach ($movies['docs'] as $movie) {
        echo 'Movie Name: ' . $movie['name'] . PHP_EOL;
    }
} else {
    echo 'No movies found.' . PHP_EOL;
}
```

It's that easy. Also, there is a usage example file for more details and sample codes.  
Open the terminal, just type in following command in the root directory to execute the example file.
```
php examples/usage-example.php
```

### Available Endpoints  
There are only two endpoints for now, Movies and Quotes.
* Movies
```
$sdk->movies->getAllMovies(); // Fetch all movies
$sdk->movies->getMovieById($movieId); // Fetch a specific movie by ID
$sdk->movies->getMovieQuotes($movieId); // Fetch quotes for a specific movie
```
* Quotes
```
$sdk->quotes->getAllQuotes(); // Fetch all quotes
$sdk->quotes->getQuoteById($quoteId); // Fetch a specific quote by ID
```

### Unit Tests
The tests are located in the tests/ folder.  

* Run tests (Running all tests)
```
vendor/bin/phpunit
```
* Running specific test example
```
vendor/bin/phpunit tests/Endpoints/MoviesEndpointTest.php
```
