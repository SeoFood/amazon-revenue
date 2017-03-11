<?php

/**
 * Retrieve all items at the given time (from => to) for a specific tag.
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

$amazon = new \SeoFood\AmazonRevenue\Client(getenv('USERNAME'), getenv('PASSWORD'));

// optional set a store if you have multiple partnernet accounts.
$amazon->setStore(getenv('STORE'));

// set the tag you want to get the items
$amazon->setAssociateTag(getenv('TAG'));

// crawl the items
$items = $amazon->getItems(new \DateTime('-1 week'), new \DateTime('today'));

// output all items
foreach ($items as $item) {
    /* @var $item SeoFood\AmazonRevenue\Item */
    echo sprintf(
        "%sx %s, Commission: %f" . PHP_EOL,
        $item->getQuantity(),
        $item->getName(),
        $item->getCommission()
    );
}
