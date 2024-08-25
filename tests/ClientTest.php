<?php

namespace LOTR\Tests;
 
use LOTR\Client;
use LOTR\Authentication;
use LOTR\HttpService;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private $client;
    private $httpServiceMock;

    protected function setUp(): void
    {
        $auth = new Authentication('fake-api-key');
        $this->httpServiceMock = $this->createMock(HttpService::class); // Mock HttpService
        $this->client = new Client('https://the-one-api.dev/v2', $auth, $this->httpServiceMock);
    }

    public function testMakeRequestWithPagination()
    {
        // Mock the HttpService's request method
        $this->httpServiceMock->method('request')
            ->with('https://the-one-api.dev/v2/movie?limit=10&page=2')
            ->willReturn([
                'response' => '{"docs":[{"name":"The Lord of the Rings Series"},{"name":"The Hobbit Series"}]}',
                'httpCode' => 200
            ]);

        // Set pagination
        $this->client->setPagination(10, 2);

        // Make the request
        $result = $this->client->makeRequest('/movie');

        // Asserts
        $this->assertNotNull($result, 'The result should not be null!');
        $this->assertArrayHasKey('docs', $result, 'The "docs" key should be present in the result.');
        $this->assertCount(2, $result['docs'], 'There should be exactly 2 movies in the response.');
        $this->assertEquals('The Lord of the Rings Series', $result['docs'][0]['name'], 'The first movie name should be "The Lord of the Rings Series".');
    }

    public function testMakeRequestWithErrorResponse()
    {
        // Mock the HttpService's request method and get a http 500 error
        $this->httpServiceMock->method('request')
            ->with('https://the-one-api.dev/v2/movie?limit=10&page=2')
            ->willReturn([
                'response' => 'Internal Server Error',
                'httpCode' => 500 
            ]);

        // Set pagination
        $this->client->setPagination(10, 2);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API Error: 500 - Internal Server Error');

        // Make the request and expect an exception
        $this->client->makeRequest('/movie');
    }

    public function testMakeRequestWithEmptyResponse()
    {
        // Mock the HttpService's request method and return an empty response
        $this->httpServiceMock->method('request')
            ->with('https://the-one-api.dev/v2/movie?limit=10&page=2')
            ->willReturn([
                'response' => '{"docs":[]}',
                'httpCode' => 200
            ]);

        $this->client->setPagination(10, 2);


        $result = $this->client->makeRequest('/movie');

        // Assertions for an empty response
        $this->assertNotNull($result, 'The result should not be null!');
        $this->assertArrayHasKey('docs', $result, 'The "docs" key should still be present in the result.');
        $this->assertCount(0, $result['docs'], 'There should be no movies in the response.');
    }
}
