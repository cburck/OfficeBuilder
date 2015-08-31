<?php
/**
 * Service Factory.
 *
 * Used me to get service objects.
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Service_Factory
{
    const USER = 'User';
    const INVENTORY = 'Inventory';
    const PRODUCT = 'Product';
    const PURCHASE = 'Purchase';
    const RETURNS = 'Returns';

    private static $_factory;

    protected $_cache = array();

    /**
     * @return OfficeBuilder_Model_Service_Factory
     */
    public static function getFactory()
    {
        if (!self::$_factory)
        {
            self::$_factory = new OfficeBuilder_Model_Service_Factory();
        }

        return self::$_factory;
    }

    private function __construct()
    {
        //SINGLETON
    }

    /**
     * Create instance of the requested service type.
     *
     * @param string $type
     * @throws Exception
     * @return OfficeBuilder_Model_Service_Abstract
     */
    public function getService($type)
    {
        if (isset($this->_cache[$type]))
        {
            return $this->_cache[$type];
        }
        else
        {
            $targetClass = 'OfficeBuilder_Model_Service_' . $type;

            if (class_exists($targetClass))
            {
                $factory = new $targetClass;
            }
            else
            {
                throw new OfficeBuilder_Exception('Failed creating ' . $targetClass . ' service. No such class.');
            }

            $this->_cache[$type] = $factory;

            return $factory;
        }
    }

    /**
     * @return OfficeBuilder_Model_Service_User
     */
    public function getUser()
    {
        return $this->getService(self::USER);
    }
    /**
     * @return OfficeBuilder_Model_Service_Product
     */
    public function getProduct()
    {
        return $this->getService(self::PRODUCT);
    }

    /**
     * @return OfficeBuilder_Model_Service_Inventory
     */
    public function getInventory()
    {
        return $this->getService(self::INVENTORY);
    }

    /**
     * @return OfficeBuilder_Model_Service_Purchase
     */
    public function getPurchase()
    {
        return $this->getService(self::PURCHASE);
    }

    /**
     * @return OfficeBuilder_Model_Service_Returns
     */
    public function getReturns()
    {
        return $this->getService(self::RETURNS);
    }
}