<?php
  
namespace LOTR\Endpoints;

use LOTR\Client;

class QuotesEndpoint
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Factory method to instantiate QuotesEndpoint
     *
     * @param string $apiKey
     * @return QuotesEndpoint
     */
    public static function create(string $apiKey): QuotesEndpoint
    {
        $auth = new Authentication($apiKey);
        $httpService = new HttpService();
        $client = new Client(Config::BASE_URL, $auth, $httpService);

        return new QuotesEndpoint($client);
    }

    /**
     * Get all quotes method
     * @return array
     * @throws \Exception
     */
    public function getAllQuotes()
    {
    
        
        return $this->client->makeRequest('/quote');
    }

    /**
     * Get a single quote by ID
     * @param string $id
     * @return array
     * @throws \Exception
     */
    public function getQuoteById($id)
    {
        if (!ctype_xdigit($id)) {
            throw new \Exception('Invalid id');
        }
        return $this->client->makeRequest('/quote/' . $id);
    }
}
