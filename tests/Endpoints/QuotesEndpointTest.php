<?php


namespace LOTR\Tests\Endpoints;

use LOTR\Client; 
use LOTR\Endpoints\QuotesEndpoint;
use PHPUnit\Framework\TestCase;


class QuotesEndpointTest extends TestCase
{
    private $quotesEndpoint;
    private $clientMock;

    protected function setUp(): void
    {
        // Create a mock 
        $this->clientMock = $this->createMock(Client::class);
        
        // Initialize QuotesEndpoint 
        $this->quotesEndpoint = new QuotesEndpoint($this->clientMock);
    }

    public function testGetAllQuotesWithFiltersAndPagination()
    {
        // Define the filters and pagination
        $filters = [
            ['key' => 'dialog', 'filter_type' => 'match', 'value' => 'Why?']
        ];
        $limit = 10;
        $page = 1;


        // Mock the makeRequest method to return a sample data
        $this->clientMock->method('makeRequest')
            ->with('/quote')
            ->willReturn([
                'docs' => [
                    ['dialog' => 'Give us that! Deagol my love'],
                    ['dialog' => 'Why?']
                ]
            ]);

        // Set filters and pagination before getAllQuotes
        $this->clientMock->setFilters($filters);
        $this->clientMock->setPagination($limit, $page);

        // Call getAllQuotes
        $quotes = $this->quotesEndpoint->getAllQuotes();

        // Asserts
        $this->assertNotNull($quotes, 'The quotes result is null');
        $this->assertArrayHasKey('docs', $quotes, 'The key "docs" is missing in the quotes result');
        $this->assertCount(2, $quotes['docs'], 'The number of quotes is not as expected');
        $this->assertEquals('Give us that! Deagol my love', $quotes['docs'][0]['dialog']);
        $this->assertEquals('Why?', $quotes['docs'][1]['dialog']);
    }

    public function testGetQuoteById()
    {
        $quoteId = '5cd96e05de30eff6ebcced61'; 
        $this->clientMock->method('makeRequest')
            ->with('/quote/' . $quoteId)
            ->willReturn([
                'dialog' => 'Why?'
            ]);

        // getQuoteById
        $quote = $this->quotesEndpoint->getQuoteById($quoteId);

        // Assert
        $this->assertNotNull($quote, 'The quote result is null');
        $this->assertEquals('Why?', $quote['dialog']);
    }

    public function testGetQuoteByIdInvalid()
    {
        // exception for invalid ID
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid id');
        $this->quotesEndpoint->getQuoteById('invalid-id');
    }
}