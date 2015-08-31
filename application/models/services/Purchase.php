<?php
/**
 * Purchase Service Class.
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Service_Purchase extends OfficeBuilder_Model_Service_Abstract
{
    /**
     * Create a new OfficeBuilder_Model_Purchase record.
     *
     * @param OfficeBuilder_Model_Inventory $inventory
     * @param OfficeBuilder_Model_User $user
     * @param $units
     * @return OfficeBuilder_Model_Purchase
     */
    public function create(OfficeBuilder_Model_Inventory $inventory, OfficeBuilder_Model_User $user, $units)
    {
        $purchase = $this->getMapper()->create($inventory, $user);
        $purchase->setPpu($inventory->getPpu());
        $purchase->setUnits($units);

        return $purchase;
    }

    /**
     * Save an instance of OfficeBuilder_Model_Purchase
     *
     * @param OfficeBuilder_Model_Purchase $user
     * @param $data New values for the model's properties
     * @return OfficeBuilder_Model_Purchase
     */
    public function save(OfficeBuilder_Model_Purchase $purchase, $data = null)
    {
        if ($data)
        {
            if (isset($data['ppu']))
            {
                $purchase->setPpu($data['ppu']);
            }

            if (isset($data['units']))
            {
                $purchase->setUnits($data['units']);
            }
        }

        return $this->getMapper()->save($purchase);
    }

    /**
     * Return collection purchases for user.
     *
     * @param OfficeBuilder_Model_User $user
     * @return array
     */
    public function getPurchasesForUser(OfficeBuilder_Model_User $user)
    {
        return $this->getMapper()->getPurchasesForUser($user);
    }

    /**
     * Returns number of units purchased for inventory.
     *
     * @param OfficeBuilder_Model_Inventory $inventory
     * @return int
     */
    public function getPurchasedUnitsForInventory(OfficeBuilder_Model_Inventory $inventory)
    {
        return $this->getMapper()->getPurchasedUnitsForInventory($inventory);
    }

    /**
     * Returns array of purchases for inventory IDs and user.
     *
     * @param OfficeBuilder_Model_User $user
     * @param array $inventoryIds
     * @return mixed
     */
    public function getPurchasesForInventoryIdsAndUser(array $inventoryIds, OfficeBuilder_Model_User $user)
    {
        return $this->getMapper()->getPurchasesForInventoryIdsAndUser($inventoryIds, $user);
    }

    /**
     * @return OfficeBuilder_Model_Mapper_Purchase
     */
    public function _createMapper()
    {
        return $this->getMapper();
    }

    /**
     * Creates a mapper used by the service.
     *
     * @return OfficeBuilder_Model_Mapper_Purchase
     */
    public function getMapper()
    {
        return $this->_getMapperFactory()->getPurchase();
    }
}
