<?php

namespace SeoFood\AmazonRevenue;

use Goutte\Client as HttpCrawler;
use GuzzleHttp\Client as HttpClient;

class Client
{
    /**
     * @var array
     */
    private $hostNames = [
        'DE' => 'partnernet.amazon.de'
    ];

    /**
     * @var HttpCrawler
     */
    private $client;

    /**
     * @var String
     */
    private $username;

    /**
     * @var String
     */
    private $password;

    /**
     * @var String
     */
    private $host;

    /**
     * @var String
     */
    private $country;

    /**
     * @var string
     */
    private $associateTag;

    /**
     * @param string $username
     * @param string $password
     * @param string $country
     *
     * @throws Exception
     */
    public function __construct($username, $password, $country = 'DE')
    {
        $this->username = $username;
        $this->password = $password;
        $this->country = $country;

        if (! array_key_exists($country, $this->hostNames)) {
            throw new Exception('No hostname for the given country available.');
        }
        $this->host = 'https://' . $this->hostNames[$country] . '/';

        $ua = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17';
        $this->client = new HttpCrawler;
        $this->client->setClient(new HttpClient([
            'cookies' => true,
            'debug' => false,
            'headers' => [
                'User-Agent' => $ua,
                'Accept-Language' => 'de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4'
            ]
        ]));

        $this->login();
    }

    private function login()
    {
        // send a request before the login to accept session cookies
        $crawler = $this->client->request('GET', $this->host);

        // login
        $form = $crawler->filter('form[name="sign_in"]')->form();
        $form['username'] = $this->username;
        $form['password'] = $this->password;
        $form['rememberMe'] = false;

        $this->client->submit($form);

        $this->deactivateCombinedReports();
    }

    /**
     * @param $id
     */
    public function setStore($id)
    {
        $crawler = $this->client->request('GET', $this->host . 'gp/associates/network/reports/report.html');

        $form = $crawler->filter('form[name="idbox_store_id_form"]')->form();
        $form['idbox_store_id'] = $id;
        $this->client->submit($form);

        $this->deactivateCombinedReports();
    }
    
    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return array
     */
    public function getItems(\DateTime $start, \DateTime $end)
    {
        $xml = $this->client->request('GET', $this->_getTransactionUrl($start, $end));

        $items = [];
        $xml->filter('Items Item')->each(function ($item) use (&$items) {
            $amazonItem = new Item;

            $amazonItem->setAsin((string)$item->attr('ASIN'));
            $amazonItem->setEDate((string)$item->attr('EDate'));
            $amazonItem->setType((string)$item->attr('LinkType'));
            $amazonItem->setPrice((float)str_replace(',', '.', (string)$item->attr('Price')));
            $amazonItem->setCommission((float)str_replace(',', '.', (string)$item->attr('Earnings')));
            $amazonItem->setName((string)$item->attr('Title'));
            $amazonItem->setQuantity((int)$item->attr('Qty'));
            $amazonItem->setDate(new \DateTime((string)$item->attr('Date')));

            $items[] = $amazonItem;
        });

        return $items;
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return string
     */
    private function _getTransactionUrl(\DateTime $start, \DateTime $end)
    {
        $url = $this->host . 'gp/associates/network/reports/report.html?__mk_de_%s=ÅMÅZÕÑ&idbox_tracking_id=%s&reportType=earningsReport&program=all&preSelectedPeriod=monthToDate&periodType=exact&startDay=%d&startMonth=%d&startYear=%d&endDay=%d&endMonth=%d&endYear=%d&submit.download_XML.x=84&submit.download_XML.y=4&submit.download_XML=Bericht+herunterladen+(XML)';

        return sprintf($url,
            strtolower($this->country),
            $this->associateTag,
            $start->format('j'),
            $start->format('n') - 1,
            $start->format('Y'),
            $end->format('j'),
            $end->format('n') - 1,
            $end->format('Y'));
    }

    /**
     * @param $tag string
     */
    public function setAssociateTag($tag)
    {
        $this->associateTag = $tag;
    }

    /**
     */
    private function deactivateCombinedReports()
    {
        $crawler = $this->client->request('GET', $this->host . 'gp/associates/network/reports/report.html');

        // always deactivate combine buttons
        if ($crawler->filter('input[name="combinedReports"]')->attr('checked') === 'checked') {
            $form = $crawler->filter('form[name="idbox_combined_reports_form"]')->form();
            $form['combinedReports'] = false;
            $this->client->submit($form);
        }
    }
}
