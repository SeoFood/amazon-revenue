<?php

class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterAndSetter()
    {
        $string     = 'abcdefghi';
        $float      = 1.1;
        $date       = new DateTime();
        $int        = 1;

        $item = new \AmazonRevenue\Item();
        $item->setAsin($string);
        $item->setCommission($float);
        $item->setDate($date);
        $item->setEDate($string);
        $item->setName($string);
        $item->setPrice($float);
        $item->setQuantity($int);
        $item->setType($string);

        $this->assertSame($item->getAsin(),         $string);
        $this->assertSame($item->getCommission(),   $float);
        $this->assertSame($item->getDate(),         $date);
        $this->assertSame($item->getEDate(),        $string);
        $this->assertSame($item->getName(),         $string);
        $this->assertSame($item->getPrice(),        $float);
        $this->assertSame($item->getQuantity(),     $int);
        $this->assertSame($item->getType(),         $string);
    }
}