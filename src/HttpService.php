<?php
namespace LOTR;

class HttpService
{
    /**
     * Makes an HTTP request using cURL library
     *
     * @param string $url
     * @param array $headers
     * @return array ['response' => $response, 'httpCode' => $httpCode]
     */
    public function request($url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['response' => $response, 'httpCode' => $httpCode];
    }
}
