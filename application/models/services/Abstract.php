<?php
/**
 * Abstract Service class.
 *
 * @subpackage Model
 */

abstract class OfficeBuilder_Model_Service_Abstract
{
    protected $_identityMap = array();
    protected $_mapper = null;

    /**
     * Retrieve a single instance of a model by its id, or an array of model objects for an array of ids.
     *
     * This uses a local cache.
     *
     * @see OfficeBuilder_Model_Service_Abstract::getById
     *
     * @param $id
     * @return OfficeBuilder_Model_Abstract
     */
    final function getById($id)
    {
        if (!is_array($id) && isset($this->_identityMap[$id]))
        {
            return $this->_identityMap[$id];
        }
        else
        {
            $model = $this->getMapper()->getById($id);

            if (!is_array($id))
            {
                $this->_identityMap[$id] = $model;
            }

            return $model;
        }
    }

    /**
     * Returns an existing mapper or creates a new one when needed.
     */
    public function getMapper()
    {
        if (!$this->_mapper)
        {
            $this->_mapper = $this->_createMapper();
        }

        return $this->_mapper;
    }

    /**
     * Creates a mapper used by the service.
     *
     * @return OfficeBuilder_Model_Mapper_Abstract
     */
    abstract protected function _createMapper();

    /**
     *
     * resets the internal object cache
     */
    public function resetIdentityCache()
    {
        unset($this->_identityMap);
        $this->_identityMap = array();
    }

    /**
     * @return OfficeBuilder_Model_Service_Factory
     */
    protected function _getFactory() {

        return OfficeBuilder_Model_Service_Factory::getFactory();
    }

    /**
     * @return OfficeBuilder_Model_Mapper_Factory
     */
    protected function _getMapperFactory() {

        return OfficeBuilder_Model_Mapper_Factory::getFactory();
    }
}
