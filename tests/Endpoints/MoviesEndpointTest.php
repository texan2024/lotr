<?php

namespace LOTR\Tests\Endpoints;


use LOTR\Client;
use LOTR\Endpoints\MoviesEndpoint;
use PHPUnit\Framework\TestCase;

class MoviesEndpointTest extends TestCase
{
    private $moviesEndpoint;
    private $clientMock;

    protected function setUp(): void
    {
        // Create a mock 
        $this->clientMock = $this->createMock(Client::class);
        
        // Initialize MoviesEndpoint 
        $this->moviesEndpoint = new MoviesEndpoint($this->clientMock);
    }

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

    public function testGetMovieQuotes()
    {
        $movieId = '5cd95395de30eff6ebccde5d'; // Sample movie ID

        $this->clientMock->method('makeRequest')
            ->with('/movie/' . $movieId . '/quote')
            ->willReturn([
                'docs' => [
                    ['dialog' => 'Give us that! Deagol my love'],
                    ['dialog' => 'Why?']
                ]
            ]);

        // getMovieQuotes by movie-id
        $quotes = $this->moviesEndpoint->getMovieQuotes($movieId);

        // Asserts
        $this->assertNotNull($quotes, 'The quotes result is null.');
        $this->assertArrayHasKey('docs', $quotes, 'The key "docs" is missing in the quotes result.');
        $this->assertCount(2, $quotes['docs'], 'The number of quotes is not as expected.');
        $this->assertEquals('Give us that! Deagol my love', $quotes['docs'][0]['dialog']);
        $this->assertEquals('Why?', $quotes['docs'][1]['dialog']);
    }
}
