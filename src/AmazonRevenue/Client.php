<?php
namespace AmazonRevenue;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use AmazonRevenue\Item;
use AmazonRevenue\Exception;

class Client
{
    /**
     * @var array
     */
    private $_hostNames = array(
        'DE' => 'partnernet.amazon.de'
    );

    /**
     * @var HttpClient
     */
    private $_client;

    /**
     * @var String
     */
    private $_username;

    /**
     * @var String
     */
    private $_password;

    /**
     * @var String
     */
    private $_host;

    /**
     * @var String
     */
    private $_country;

    /**
     * @var string
     */
    private $_associateTag;

    /**
     * @param string $username
     * @param string $password
     * @param string $country
     *
     * @throws Exception
     */
    public function __construct($username, $password, $country = 'DE')
    {
        $this->_username    = $username;
        $this->_password    = $password;
        $this->_country     = $country;

        if (!array_key_exists($country, $this->_hostNames)) {
            throw new Exception('No hostname for the given country available.');
        }
        $this->_host = $this->_hostNames[$country];

        $this->_client = new HttpClient('https://' . $this->_host . '/');
        $this->_client->setSslVerification(false, false, 0);
        $this->_client->setUserAgent('Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17', true);

        $cookiePlugin = new CookiePlugin(new ArrayCookieJar());
        $this->_client->addSubscriber($cookiePlugin);

        $this->_login();
    }

    private function _login()
    {
        try {
            // send a request before the login to accept session cookies
            $res = $this->_client->get('')->send();

            $body = $res->getBody(true);
            preg_match('~name="widgetToken" value="([^"]+)"~', $body, $widgetToken);
            preg_match('~name="sign_in" action=([^\s]+)~', $body, $formAction);

            // login
            $request = $this->_client->post(
                $formAction[1],
                null,
                array(
                     'username' => $this->_username,
                     'password' => $this->_password,
                     'action'   => 'sign-in',
                     'mode'     => 1,
                     'query'    => 'returl=/gp/associates/network/reports/report.html&retquery=',
                     'path'     => '/gp/associates/login/login.html',
                     'rememberMe' => false,
                     'widgetToken' => $widgetToken[1]

                )
            );
            $request->send();
        } catch (ServerErrorResponseException $e) {
            throw new Exception($e->getMessage());
        }

        $response = $this->_client->get('gp/associates/network/reports/report.html')->send();
        $body = $response->getBody(true);

        // always deactivate combine buttons
        if (strstr($body, 'name="combinedReports" checked="checked"')) {
            $this->_client->get('gp/associates/x-site/combinedReports.html')->send();
        }
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return array
     */
    public function getItems(\DateTime $start, \DateTime $end)
    {
        $xml = $this->_client->get($this->_getTransactionUrl($start, $end))
            ->send()
            ->xml();

        $items = array();
        foreach ($xml->Items->Item as $item) {
            $amazonItem = new Item();

            $amazonItem->setAsin((string)$item['ASIN']);
            $amazonItem->setEDate((string)$item['EDate']);
            $amazonItem->setType((string)$item['Type']);
            $amazonItem->setPrice((float)str_replace(',', '.', (string)$item['Price']));
            $amazonItem->setCommission((float)str_replace(',', '.', (string)$item['Earnings']));
            $amazonItem->setName((string)$item['Title']);
            $amazonItem->setQuantity((int)$item['Qty']);
            $amazonItem->setDate(new \DateTime((string)$item['Date']));

            $items[] = $amazonItem;
        }

        return $items;
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return string
     */
    private function _getTransactionUrl(\DateTime $start, \DateTime $end)
    {
        $url = 'gp/associates/network/reports/report.html?__mk_de_%s=ÅMÅZÕÑ&idbox_tracking_id=%s&reportType=earningsReport&program=all&preSelectedPeriod=monthToDate&periodType=exact&startDay=%d&startMonth=%d&startYear=%d&endDay=%d&endMonth=%d&endYear=%d&submit.download_XML.x=84&submit.download_XML.y=4&submit.download_XML=Bericht+herunterladen+(XML)';

        return sprintf($url,
            strtolower($this->_country),
            $this->_associateTag,
            $start->format('j'),
            $start->format('n')-1,
            $start->format('Y'),
            $end->format('j'),
            $end->format('n')-1,
            $end->format('Y'));
    }

    /**
     * @param $tag string
     */
    public function setAssociateTag($tag)
    {
        $this->_associateTag = $tag;
    }
}