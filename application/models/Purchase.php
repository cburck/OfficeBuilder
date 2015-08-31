<?php
/**
 * Purchase model
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Purchase extends OfficeBuilder_Model_Abstract
{
    /**
     * @return OfficeBuilder_Model_Inventory
     */
    protected $_inventory;

    /**
     * @return OfficeBuilder_Model_User
     */
    protected $_user;
    protected $_units;
    protected $_ppu;
    protected $_createdOn;

    /**
     * @param int $id
     * @param string $inventory
     * @param string $user
     * @param string $units
     * @param string $ppu
     * @param string $createdOn
     */
    public function __construct($id = null, $inventory = null, $user = null, $units = null, $ppu = null, $createdOn = null)
    {
        $this->_id = $id;
        $this->_inventory = $inventory;
        $this->_user = $user;
        $this->_units = $units;
        $this->_ppu = $ppu;
        $this->_createdOn = $createdOn;
    }

    /**
     * @return OfficeBuilder_Model_Inventory
     */
    public function getInventory()
    {
        return $this->_inventory;
    }

    /**
     * @param OfficeBuilder_Model_Inventory $inventory
     */
    public function setInventory(OfficeBuilder_Model_Inventory $inventory)
    {
        $this->_inventory = $inventory;
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
     * @return OfficeBuilder_Model_User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param OfficeBuilder_Model_User $user
     */
    public function setUser(OfficeBuilder_Model_User $user)
    {
        $this->_user = $user;
    }

    /**
     * Sort a collection of OfficeBuilder_Model_Purchase.
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
     * Ascending date comparison of two OfficeBuilder_Model_Purchases instances.
     *
     * @param OfficeBuilder_Model_Purchase $a
     * @param OfficeBuilder_Model_Purchase $b
     * @return int
     */
    public static function compareDatesAsc(OfficeBuilder_Model_Purchase $a, OfficeBuilder_Model_Purchase $b) {

        $aTime = strtotime($a->getCreatedOn());
        $bTime = strtotime($b->getCreatedOn());

        if ($aTime == $bTime)
        {
            return 0;
        }

        return ($aTime < $bTime) ? -1 : 1;
    }

    /**
     * Descending date comparison of two OfficeBuilder_Model_Purchases instances.
     *
     * @param OfficeBuilder_Model_Purchase $a
     * @param OfficeBuilder_Model_Purchase $b
     * @return int
     */
    public static function compareDatesDesc(OfficeBuilder_Model_Purchase $a, OfficeBuilder_Model_Purchase $b) {

        $aTime = strtotime($a->getCreatedOn());
        $bTime = strtotime($b->getCreatedOn());

        if ($aTime == $bTime)
        {
            return 0;
        }

        return ($aTime > $bTime) ? -1 : 1;
    }
}