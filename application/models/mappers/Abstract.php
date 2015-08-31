<?php
/**
 * Abstract Mapper class.
 *
 * @subpackage Model
 */

abstract class OfficeBuilder_Model_Mapper_Abstract
{
    /**
     * database model
     */
    protected $_db;

    /**
     * @var OfficeBuilder_Model_Service_Abstract
     */
    protected static $_serviceFactory;

    /**
     * @var OfficeBuilder_Model_Mapper_Abstract
     */
    protected static $_mapperFactory;

    protected $_identityMap = array();

    /**
     * Retrieve a single instance of a model by its id, or an array of model objects for an array of ids.
     *
     * This uses a local cache.
     *
     * @see OfficeBuilder_Model_Service_Abstract::getById
     * @param $id
     * @return OfficeBuilder_Model_Abstract
     */
    final function getById($id)
    {
        if (!is_array($id) && isset($this->_identityMap[$id]))
        {
            return $this->_identityMap[$id];
        }

        $table = $this->getTable();

        if (is_array($id))
        {
            $returnObjs = array();

            if (empty($id))
            {
                return $returnObjs;
            }

            $uncachedIds = array();
            foreach ($id as $currId)
            {
                if (is_numeric($currId))
                {
                    $uncachedIds[$currId] = $currId;
                }
            }

            if (count($uncachedIds) > 0)
            {
                $select = $this->getSelect();
                $select->where($table->info('name').'.id IN (?)', $uncachedIds);
                $resultSet = $table->fetchAll($select);
                $returnObjs = $this->_loadSet($resultSet);
            }

            return $returnObjs;
        }
        else
        {
            if ($id === NULL || $id == '' || !is_numeric($id))
            {
                return NULL;
            }

            $select = $this->getSelect();

            $select->where($table->info('name').'.id = ?', $id);

            $result = $table->fetchRow($select);

            if ($result)
            {
                $object = $this->_load($result);
                $this->_identityMap[$id] = $object;

                return $object;
            }

            return NULL;
        }
    }

    /**
     * Delete a specific model row.
     *
     * @param mixed $model
     */
    public function delete($model)
    {
        $table = $this->getTable();

        $db = $this->getDb();

        return $table->delete('id = ' . $db->quote($model->getId()));
    }

    /**
     * This method allows sub-classes to define their own select statements, which lets them use information across tables.
     *
     *  @return Zend_Db_Table_Select
     */
    public function getSelect()
    {
        return $this->getTable()->select();
    }

    /**
     * Inserts or updates a mode.
     *
     * @param OfficeBuilder_Model_DbTable_Abstract $table
     * @param OfficeBuilder_Model_Abstract|object $model
     * @param array $data
     * @return OfficeBuilder_Model_Abstract
     */
    protected function _save(OfficeBuilder_Model_DbTable_Abstract $table, OfficeBuilder_Model_Abstract $model, $data)
    {
        if ($model->getId())
        {
            //possibly set created here to get around timezone issues

            //UPDATE
            $table->update($data, 'id = ' . $table->getDefaultAdapter()->quote($model->getId()));

            //force reload if getById called again
            if (isset($this->_identityMap[$model->getId()]))
            {
                unset($this->_identityMap[$model->getId()]);
            }
        }
        else
        {
            //INSERT A NEW RECORD
            $id = $table->insert($data);

            $model->setId($id);
        }

        return $model;
    }

    /**
     * Helper method to load data into multiple models in a set.
     *
     * @param mixed $set
     * @param bool $loadRelated
     * @return array
     */
    protected function _loadSet($set, $loadRelated=true)
    {
        $results = array();

        foreach ($set as $currData)
        {
            $model = $this->_load($currData, $loadRelated);
            $results[$currData['id']] = $model;
        }

        return $results;
    }

    /**
     * Helper method to load standard database class.
     */
    public function getDb()
    {
        if (!$this->_db)
        {
            $this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
        }

        return $this->_db;
    }

    /**
     * Helper method to release the database class from memory.
     */
    public function releaseDb()
    {
        unset($this->_db);
    }

    /**
     * Helper to return a service factory.
     *
     * @return OfficeBuilder_Model_Service_Factory
     */
    protected static function _getServiceFactory()
    {
        if (!self::$_serviceFactory)
        {
            self::$_serviceFactory = OfficeBuilder_Model_Service_Factory::getFactory();
        }

        return self::$_serviceFactory;
    }

    /**
     * Helper to return a mapper factory.
     *
     * @return OfficeBuilder_Model_Mapper_Factory
     */
    protected static function _getMapperFactory()
    {
        if (!self::$_mapperFactory)
        {
            self::$_mapperFactory = OfficeBuilder_Model_Service_Factory::getFactory();
        }

        return self::$_mapperFactory;
    }
}