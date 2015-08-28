<?php
/**
 * Abstract Database Table class
 *
 * @subpackage Model
 */

abstract class OfficeBuilder_Model_DbTable_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * Setup caching of table meta-data for Zend DB
     *
     * @return bool
     */
    protected function _setupMetadata()
    {
        try
        {
            if ($this->metadataCacheInClass() && (Zend_Registry::isRegistered('dbmeta_'.$this->_name)))
            {
                $this->_metadata = Zend_Registry::get('dbmeta_'.$this->_name);

                return true;
            }
            else
            {
                $fromCache = parent::_setupMetadata();
                Zend_Registry::set('dbmeta_' . $this->_name, $this->_metadata);

                return $fromCache;
            }
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}