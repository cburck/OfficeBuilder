<?php
/**
 * Purchase Model Inventory
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Mapper_Purchase extends OfficeBuilder_Model_Mapper_Abstract
{
    /**
     * Get the table used by this mapper.
     *
     * @return OfficeBuilder_Model_DbTable_Product
     */
    public function getTable()
    {
        return new OfficeBuilder_Model_DbTable_Purchase();
    }

    /**
     * Create a new instance of OfficeBuilder_Model_Purchase.
     * 
     * @param OfficeBuilder_Model_Inventory $inventory
     * @param OfficeBuilder_Model_User $user
     * @return OfficeBuilder_Model_Purchase
     */
    public function create(OfficeBuilder_Model_Inventory $inventory, OfficeBuilder_Model_User $user)
    {
        $purchase = new OfficeBuilder_Model_Purchase();
        $purchase->setInventory($inventory);
        $purchase->setUser($user);

        return $purchase;
    }

    /**
     * Save an instance of OfficeBuilder_Model_Purchase.
     *
     * This method handles creating new records as well as saving existing ones.
     * If the model does not have an id, a new record is created.
     *
     * @param OfficeBuilder_Model_Purchase $purchase Model to be saved
     * @param bool $suppressFetch Tell the method to not reload the data into the returned model
     * @return OfficeBuilder_Model_Purchase
     */
    public function save(OfficeBuilder_Model_Purchase $purchase, $suppressFetch=true)
    {
        $table = $this->getTable();

        $data = array(
            'inventory_id' => $purchase->getInventory()->getId(),
            'user_id' => $purchase->getUser()->getId(),
            'units' => (int)$purchase->getUnits(),
            'ppu' => (float)$purchase->getPpu()
        );

        return $this->_save($table, $purchase, $data);
    }

    /**
     * Return collection purchases for user.
     *
     * @param OfficeBuilder_Model_User $user
     * @return array
     */
    public function getPurchasesForUser(OfficeBuilder_Model_User $user)
    {
        $table = $this->getTable();
        $select = $table->select()->where('user_id = ?', $user->getId());

        return $this->_loadSet($table->fetchAll($select));
    }

    /**
     * Returns number of units purchased for inventory.
     *
     * @param OfficeBuilder_Model_Inventory $inventory
     * @return int
     */
    public function getPurchasedUnitsForInventory(OfficeBuilder_Model_Inventory $inventory)
    {
        $table = $this->getTable();
        $select = $table->select()
                        ->from('Purchases', array('units_purchased' => new Zend_Db_Expr('count(units)')))
                        ->where('inventory_id = ?', $inventory->getId());

        $result = $table->fetchRow($select);

        if ($result)
        {
            return $result['units_purchased'];
        }

        return 0;
    }

    /**
     * Returns array of purchases for inventory IDs and user.
     *
     * @param OfficeBuilder_Model_User $user
     * @param array $inventoryIds
     * @return array
     */
    public function getPurchasesForInventoryIdsAndUser(array $inventoryIds, OfficeBuilder_Model_User $user)
    {
        if (count($inventoryIds) < 1)
        {
            return array();
        }

        $table = $this->getTable();
        $select = $table->select()
                        ->where('user_id = ?', $user->getId())
                        ->where('inventory_id IN (?)', $inventoryIds);

        return $this->_loadSet($table->fetchAll($select));
    }

    /**
     * Load data from the database into a new model instance.
     *
     * @param $data
     * @param bool $loadRelated
     * @return OfficeBuilder_Model_Purchase
     */
    protected function _load($data, $loadRelated=true)
    {
        if (!is_array($data))
        {
            $data = $data->toArray();
        }

        //get related models
        $inventory = $this->_getMapperFactory()->getInventory()->getById($data['inventory_id']);
        $user = $this->_getMapperFactory()->getUser()->getById($data['user_id']);

        return new OfficeBuilder_Model_Purchase($data['id'], $inventory, $user, $data['units'], $data['ppu'], $data['created_on']);
    }
}
