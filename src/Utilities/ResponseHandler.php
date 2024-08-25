<?php

namespace LOTR\Utilities;   

class ResponseHandler 
{
    public static function handleResponse($response, $httpCode)
    {
        if ($httpCode >= 400) {
            throw new \Exception('API Error: ' . $httpCode . ' - ' . $response);
        }

        return json_decode($response, true);
    }
}