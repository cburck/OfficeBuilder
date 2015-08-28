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
}