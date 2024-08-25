<?php

namespace LOTR;

//
use LOTR\Endpoints\MoviesEndpoint;
use LOTR\Endpoints\QuotesEndpoint;

class LOTRSdk
{
    private $client;

    // Endpoint variables
    public $movies;
    public $quotes;

    public function __construct(string $apiKey)
    {
        // Initialize the authentication, client
        $auth = new Authentication($apiKey);
        $httpService = new HttpService();
        $this->client = new Client(Config::BASE_URL, $auth, $httpService);

        // Initialize individual endpoints
        // we can add more endpoints later
        $this->movies = new MoviesEndpoint($this->client);
        $this->quotes = new QuotesEndpoint($this->client);
    }

    /**
     * Factory method to initialize the whole SDK
     *
     * @param string $apiKey
     * @return LOTRSdk
     */
    public static function create(string $apiKey): LOTRSdk
    {
        return new self($apiKey);
    }

    /**
     * Getter for the client instance
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
