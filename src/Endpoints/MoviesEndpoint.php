<?php
 
namespace LOTR\Endpoints;
  
use LOTR\Client;
 
class MoviesEndpoint
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Factory method to instantiate MoviesEndpoint
     *
     * @param string $apiKey
     * @return MoviesEndpoint
     */
    public static function create(string $apiKey): MoviesEndpoint
    {
        $auth = new Authentication($apiKey);
        $httpService = new HttpService();
        $client = new Client(Config::BASE_URL, $auth, $httpService);

        return new MoviesEndpoint($client);
    }

    /**
     * Get all movies 
     * @return array
     * @throws \Exception
     */
    public function getAllMovies()
    {
        return $this->client->makeRequest('/movie');
    }

    /**
     * Get a single movie by ID
     * @param string $id
     * @return array
     * @throws \Exception
     */
    public function getMovieById($id)
    {
        if (!ctype_xdigit($id)) {
            throw new \Exception('Invalid id');
        }
        return $this->client->makeRequest('/movie/' . $id);
    }

    /**
     * Get all quotes from a movie by movie ID
     * @param string $id
     * @return array
     * @throws \Exception
     */
    public function getMovieQuotes($movieId)
    {
        if (!ctype_xdigit($movieId)) {
            throw new \Exception('Invalid movie id');
        }
        return $this->client->makeRequest('/movie/' . $movieId . '/quote');
    }
}
