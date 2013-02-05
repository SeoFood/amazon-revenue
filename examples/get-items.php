<?php

/**
 * Retrieve all items at the given time (from => to) for a specific tag
 */

require_once 'vendor/autoload.php';

$amazon = new \AmazonRevenue\Client('my@email.com', 'my_password');

// set the tag you want to get the items
$amazon->setAssociateTag('mytag-21');

// crawl the items
$items = $amazon->getItems(new \DateTime('-1 week'), new \DateTime('today'));

// output all items
foreach ($items as $item) {
    /* @var $item \AmazonRevenue\Item */
    echo sprintf(
        "%sx %s, Commission: %f" . PHP_EOL,
        $item->getQuantity(),
        $item->getName(),
        $item->getCommission()
    );
}