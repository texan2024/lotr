<?php


namespace LOTR;

class Client
{
    private $apiBaseUrl;
    private $auth;
    private $headers = [];
    private $filters = [];
    private $limit = null;
    private $page = null;
    private $offset = null;
    private $httpService;

    public function __construct($apiBaseUrl, Authentication $auth, HttpService $httpService)
    {
        $this->apiBaseUrl = $apiBaseUrl;
        $this->auth = $auth;
        $this->httpService = $httpService;
        $this->headers = [
            'Authorization: Bearer ' . $auth->getApiKey(),
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    }

    /**
     * Set pagination parameters
     */
    public function setPagination($limit = null, $page = null, $offset = null)
    {
        $this->limit = $limit;
        $this->page = $page;
        $this->offset = $offset;
    }

    /**
     * Set filters for request with query strings
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Build query parameters in proper format
     */
    private function buildQueryParameters()
    {
        $params = [];

        // Handle pagination
        if (!empty($this->limit)) {
            $params['limit'] = $this->limit;
        }
        if (!empty($this->page)) {
            $params['page'] = $this->page;
        }
        if (!empty($this->offset)) {
            $params['offset'] = $this->offset;
        }

        // Handle filters based on filter types
        foreach ($this->filters as $filter) {
            if (isset($filter['key']) && isset($filter['filter_type'])) {
                $key = $filter['key'];
                $value = $filter['value'] ?? null;  // access value only if it exists

                switch ($filter['filter_type']) {
                    case 'match':
                        if ($value !== null) {
                            $params[$key] = $value;  
                        }
                        break;
                    case '!=':
                        if ($value !== null) {
                            $params[$key . '!'] = $value;  
                        }
                        break;
                    case 'include':
                        if ($value !== null) {
                            $params[$key] = implode(',', (array)$value);
                        }
                        break;
                    case 'exclude':
                        if ($value !== null) {
                            $params[$key . '!'] = implode(',', (array)$value);
                        }
                        break;
                    case 'exists':
                        $params[$key] = '';  //No value needed for "exists"
                        break;
                    case 'not_exists':
                        $params['!' . $key] = '';  // No value needed for "not_exists"
                        break;
                    case 'regex_match':
                        if ($value !== null) {
                            $params[$key] = '/' . $value . '/i';  // Assign regex directly
                        }
                        break;
                    case 'regex_not_match':
                        if ($value !== null) {
                            $params[$key . '!'] = '/' . $value . '/i';  
                        }
                        break;
                    case '>':
                    case '<':
                    case '>=':
                    case '<=':
                        if ($value !== null) {
                            $params[$key . $filter['filter_type']] = $value;  
                        }
                        break;
                }
            }
        }

        // Use http_build_query to build the query string
        // PHP_QUERY_RFC3986 ensures spaces are encoded as %20 instead of +, and
        // safely encode other special characters as well
        return $params ? '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986) : '';
    }




    /**
     * Makes an API request.
     *
     * @param string $endpoint
     * @param int $retry
     * @return array
     * @throws \Exception
     */
    public function makeRequest($endpoint, $retry = 0)
    {
        $url = $this->apiBaseUrl . $endpoint . $this->buildQueryParameters();
        $result = $this->httpService->request($url, $this->headers);
        $response = $result['response'];
        $httpCode = $result['httpCode'];

        if ($httpCode === 429 && $retry < 10) {
            sleep(10);
            return $this->makeRequest($endpoint, $retry + 1);
        } elseif ($httpCode >= 400) {
            throw new \Exception('API Error: ' . $httpCode . ' - ' . $response);
        }

        return \LOTR\Utilities\ResponseHandler::handleResponse($response, $httpCode);
    }
}
