<?php
namespace AmazonRevenue;
use AmazonRevenue\Exception;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        try {
            throw new Exception('test');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }
}