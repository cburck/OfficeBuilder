<?php
/**
 * Inventory model
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Inventory extends OfficeBuilder_Model_Abstract
{
    /**
     * @var OfficeBuilder_Model_Product
     */
    protected $_product;
    protected $_units;
    protected $_ppu;
    protected $_dateTime;
    protected $_createdOn;

    /**
     * @param int $id
     * @param string $product
     * @param string $units
     * @param string $ppu
     * @param string $dateTime
     * @param string $createdOn
     */
    public function __construct($id = null, $product = null, $units = null, $ppu = null, $dateTime = null, $createdOn = null)
    {
        $this->_id = $id;
        $this->_product = $product;
        $this->_units = $units;
        $this->_ppu = $ppu;
        $this->_dateTime= $dateTime;
        $this->_createdOn = $createdOn;
    }

    /**
     * @return OfficeBuilder_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * @param OfficeBuilder_Model_Product
     */
    public function setProduct(OfficeBuilder_Model_Product $product)
    {
        $this->_product = $product;
    }

    /**
     * @return null|string
     */
    public function getCreatedOn()
    {
        return $this->_createdOn;
    }

    /**
     * @param null|string $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->_createdOn = $createdOn;
    }

    /**
     * @return null|string
     */
    public function getDateTime()
    {
        return $this->_dateTime;
    }

    /**
     * @param null|string $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->_dateTime = $dateTime;
    }

    /**
     * @return null|string
     */
    public function getPpu()
    {
        return $this->_ppu;
    }

    /**
     * @param null|string $ppu
     */
    public function setPpu($ppu)
    {
        $this->_ppu = $ppu;
    }

    /**
     * @return null|string
     */
    public function getUnits()
    {
        return $this->_units;
    }

    /**
     * @param null|string $units
     */
    public function setUnits($units)
    {
        $this->_units = $units;
    }

    /**
     * Returns human-readable description of this inventory's period.
     *
     * @return string
     */
    public function getPeriodDescription()
    {
        $datetime = new DateTime($this->getDateTime());

        switch ($this->_product->getType())
        {
            case OfficeBuilder_Model_Product::PRODUCT_TYPE_DAYTIME:
                //add one hour to defined time
                $plusOneHour = clone $datetime;
                $plusOneHour->add(new DateInterval('PT1H'));

                return $datetime->format('n/j/Y @ ga').' to '.$plusOneHour->format('ga');
                break;
            case OfficeBuilder_Model_Product::PRODUCT_TYPE_DAY:
                return $datetime->format('n/j/Y').' - All Day';
                break;
            default:
                return 'One Year';
                break;
        }
    }

    /**
     * Sort a collection of OfficeBuilder_Model_Inventory.
     *
     * @param array $collection
     * @param string $dir
     */
    public static function sortByDate(&$collection, $dir = 'asc')
    {
        if ($dir == 'desc')
        {
            uasort($collection, array(__CLASS__, 'compareDatesDesc'));
        }
        else
        {
            uasort($collection, array(__CLASS__, 'compareDatesAsc'));
        }
    }

    /**
     * Ascending date comparison of two OfficeBuilder_Model_Inventory instances.
     *
     * @param OfficeBuilder_Model_Inventory $a
     * @param OfficeBuilder_Model_Inventory $b
     * @return int
     */
    public static function compareDatesAsc(OfficeBuilder_Model_Inventory $a, OfficeBuilder_Model_Inventory $b) {

        $aTime = strtotime($a->getDateTime());
        $bTime = strtotime($b->getDateTime());

        if ($aTime == $bTime)
        {
            return 0;
        }

        return ($aTime > $bTime) ? -1 : 1;
    }

    /**
     * Descending date comparison of two OfficeBuilder_Model_Inventory instances.
     *
     * @param OfficeBuilder_Model_Inventory $a
     * @param OfficeBuilder_Model_Inventory $b
     * @return int
     */
    public static function compareDatesDesc(OfficeBuilder_Model_Inventory $a, OfficeBuilder_Model_Inventory $b) {

        $aTime = strtotime($a->getDateTime());
        $bTime = strtotime($b->getDateTime());

        if ($aTime == $bTime)
        {
            return 0;
        }

        return ($aTime < $bTime) ? -1 : 1;
    }
}