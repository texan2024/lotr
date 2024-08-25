# LOTR PHP SDK Design

## 1. Purpose
The LOTR (Lord of the Rings) PHP SDK is designed to provide developers with an easy-to-use SDK for interacting with the LOTR API. It abstracts the complexities of making HTTP requests, handling authentication, and processing responses from the API, while offering additional features -  filtering, pagination, and error handling.

## 2. Architecture
The LOTR PHP SDK is structured to separate concerns across different components. The core architecture is built around the following key components:

### 2.1 LOTRSdk Class
This is the main entry point to the SDK. It initializes the required components, such as the Client, MoviesEndpoint, and QuotesEndpoint. It uses a factory method (create) and easily instantiates by passing the API key as a parameter.

Responsibilities:  
* Instantiates Client to handle the HTTP requests and authentication.  
* Provides access to specific API endpoints like MoviesEndpoint and QuotesEndpoint.  
* Provides a public getter for the Client to allow setting filters and pagination.

### 2.2 Client Class
The Client class is responsible for making actual HTTP requests to the LOTR API, including:

* Setting bearer authentication headers with the API key.
* Supporting pagination and filtering.
* Constructing URLs dynamically based on filters, pagination, and endpoint paths.

Key Methods:  
* setFilters(): Applies filters in a structured format (e.g., key, filter_type, value).
* setPagination(): Handles the limit, page, and offset for paginated requests.
* makeRequest(): Sends the API request, handles retries, and processes the response.
* buildQueryParameters(): Constructs query strings based on filters and pagination.

### 2.3 Endpoints
Each specific LOTR API endpoint has a corresponding class. The SDK currently supports only two endpoints: MoviesEndpoint and QuotesEndpoint.

Responsibilities:  
* Each endpoint class encapsulates logic for interacting with specific LOTR API resources.
* Endpoints communicate through the Client class and manage resource-specific paths and response handling.

Example Methods:  
* MoviesEndpoint::getAllMovies(): Fetches all movies.
* MoviesEndpoint::getMovieQuotes(): Fetches all quotes from a specific movie by the movie's ID.
* QuotesEndpoint::getAllQuotes(): Fetches all quotes.
* QuotesEndpoint::getQuoteById(): Fetches a specific quote by its ID.

### 2.4 HttpService Class
The HttpService class is responsible for low-level HTTP communication. 

* Making GET requests.
* Setting headers.
* Returning structured responses with both HTTP status codes and data.

### 2.5 Utilities
ResponseHandler can handle and parse the API's responses, check for HTTP errors, and extract relevant data for the SDK.

## 3. Key Features

### 3.1 Authentication
* The SDK uses an API key for authentication, passed during the initialization of the LOTRSdk class.
* The Authentication class passes the API key to the Client class for authorization headers.

### 3.2 Filtering
* The SDK supports a flexible filtering system with various filter types.
* Filters are passed as an array with keys such as key, filter_type, and value.
* Supported filter types include:
  - match: Exact value match (name=The Hobbit).
  - !=: Negated match (name!=The Hobbit).
  - include: Values inclusion (dialog=Why?,Deagol!!).
  - exclude: Values exclusion (dialog!=Why?,Deagol!!).
  - exists: Existence of a field (academyAwardWins).
  - regex_match: Regex match (dialog=/why/i).
  - comparison operators: Greater than, less than, etc. (budgetInMillions>=100).

### 3.3 Pagination
* The SDK provides built-in support for paginated API responses.
* Developers can specify pagination parameters (limit, page, and offset).

### 3.4 Error Handling
* The SDK throws exceptions when HTTP errors occurred.
* It handles HTTP 429 Too Many Requests errors with retry logic.

### 3.5 Testable and Extendable
* The SDK uses PHPUnit to be easily testable.
* Mocking is supported for the Client class, allowing unit tests to work without making actual API calls.
* Endpoint classes and the Client class are extendable, adding new endpoints are easy.

### 4. Usage Examples
Initialize the SDK:

```
$sdk = LOTRSdk::create('your-api-key');
```

Fetch Movies with Filters and Pagination:
```
$filters = [
    ['key' => 'name', 'filter_type' => 'match', 'value' => 'The Return of the King'],
    ['key' => 'budgetInMillions', 'filter_type' => '>=', 'value' => 100],
];
$sdk->getClient()->setFilters($filters);
$sdk->getClient()->setPagination(10, 1);

$movies = $sdk->movies->getAllMovies();
```

Fetch Quotes with a Regex Filter:
```
$quoteFilters = [
    ['key' => 'dialog', 'filter_type' => 'regex_match', 'value' => 'Why?']
];
$sdk->getClient()->setFilters($quoteFilters);
$sdk->getClient()->setPagination(10, 1);

$quotes = $sdk->quotes->getAllQuotes();
```

Fetch Specific Quote by ID:
```
$quoteId = '5cd96e05de30eff6ebcce7e9';
$quote = $sdk->quotes->getQuoteById($quoteId);
```

## 5. Testing

The SDK includes unit tests that use PHPUnit.

* Mocking: Mocking the Client and HttpService classes to simulate HTTP requests without actually calling the API.
* Asserts: Verifying that the SDK correctly processes data and handles errors.
* Endpoint Tests: Testing that each endpoint class correctly interacts with the Client.

Example Test Case for Movies Endpoint:
```
public function testGetAllMoviesWithFiltersAndPagination()
{
    // filters and pagination
    $filters = [
        ['key' => 'name', 'filter_type' => 'match', 'value' => 'The Hobbit Series']
    ];
    $limit = 10;
    $page = 2;

    // Mock the makeRequest method 
    $this->clientMock->method('makeRequest')
        ->with('/movie')
        ->willReturn([
            'docs' => [
                ['name' => 'The Lord of the Rings Series'],
                ['name' => 'The Hobbit Series']
            ]
        ]);

    // Set filters and pagination before getAllMovies
    $this->clientMock->setFilters($filters);
    $this->clientMock->setPagination($limit, $page);

    // Call getAllMovies
    $movies = $this->moviesEndpoint->getAllMovies();

    // Asserts
    $this->assertNotNull($movies, 'The movies result is null.');
    $this->assertArrayHasKey('docs', $movies, 'The key "docs" is missing in the movies result.');
    $this->assertCount(2, $movies['docs'], 'The number of movies is not as expected.');
    $this->assertEquals('The Hobbit Series', $movies['docs'][1]['name']);
}
```