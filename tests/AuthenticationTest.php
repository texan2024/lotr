<?php

namespace LOTR\Tests;

use LOTR\Authentication;
use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{
    public function testGetApiKey()
    {
        $auth = new Authentication('fake-api-key');
        $this->assertEquals('fake-api-key', $auth->getApiKey());
    }
}


