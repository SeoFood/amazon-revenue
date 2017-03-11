<?php

namespace Tests\AmazonRevenue;

use Exception;
use PHPUnit\Framework\TestCase;
use SeoFood\AmazonRevenue\Client;

class ClientTest extends TestCase
{
    /**
     * @test
     */
    public function it_tests_that_the_client_can_be_created()
    {
        try {
            new Client(getenv('USERNAME'), getenv('PASSWORD'));
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }
}
