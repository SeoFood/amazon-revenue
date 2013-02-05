<?php

class ClientTest extends \PHPUnit_Framework_TestCase
{
   public function testClient()
   {
        try {
            new \AmazonRevenue\Client('test', 'test');
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false);
        }
   }
}