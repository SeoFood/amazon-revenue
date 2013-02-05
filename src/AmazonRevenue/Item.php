<?php
namespace AmazonRevenue;



class Item
{
    /**
     * @var string
     */
    private $_asin;

    /**
     * @var string
     */
    private $_eDate;

    /**
     * @var string
     */
    private $_type;

    /**
     * @var float
     */
    private $_price;

    /**
     * @var float
     */
    private $_commission;

    /**
     * @var string
     */
    private $_name;

    /**
     * @var integer
     */
    private $_quantity;

    /**
     * @var DateTime
     */
    private $_date;

    /**
     * @param float $commission
     */
    public function setCommission($commission)
    {
        $this->_commission = $commission;
    }

    /**
     * @return float
     */
    public function getCommission()
    {
        return $this->_commission;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->_date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * @param string $eDate
     */
    public function setEDate($eDate)
    {
        $this->_eDate = $eDate;
    }

    /**
     * @return string
     */
    public function getEDate()
    {
        return $this->_eDate;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->_price = $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->_price;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->_quantity = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->_quantity;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $asin
     */
    public function setAsin($asin)
    {
        $this->_asin = $asin;
    }

    /**
     * @return string
     */
    public function getAsin()
    {
        return $this->_asin;
    }
}
