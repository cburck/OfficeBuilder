<?php
/**
 * Inventory Service Class
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Service_Inventory extends OfficeBuilder_Model_Service_Abstract
{
    /**
     * Create a new OfficeBuilder_Model_Inventory record.
     * 
     * @param OfficeBuilder_Model_Product $product
     * @return OfficeBuilder_Model_Inventory
     */
    public function create(OfficeBuilder_Model_Product $product)
    {
        return $this->getMapper()->create($product);
    }

    /**
     * Save an instance of OfficeBuilder_Model_Inventory
     *
     * @param OfficeBuilder_Model_Inventory $user
     * @param $data New values for the model's properties
     * @return OfficeBuilder_Model_Inventory
     */
    public function save(OfficeBuilder_Model_Inventory $inventory, $data = null)
    {
        if ($data)
        {
            if (isset($data['ppu']))
            {
                $inventory->setPpu($data['ppu']);
            }

            if (isset($data['units']))
            {
                $inventory->setUnits($data['units']);
            }

            if (isset($data['datetime']))
            {
                $inventory->setDateTime($data['datetime']);
            }
        }

        return $this->getMapper()->save($inventory);
    }

    /**
     * Returns collection of inventory on or after reference date.
     * Rounds down to the nearest hour.
     *
     * @param OfficeBuilder_Model_Product $product
     * @param DateTime $date
     * @return array
     */
    public function getInventoryForProductAfterDate(OfficeBuilder_Model_Product $product, DateTime $date)
    {
        return $this->getMapper()->getInventoryForProductAfterDate($product, $date);
    }

    /**
     * @return OfficeBuilder_Model_Mapper_Inventory
     */
    public function _createMapper()
    {
        return $this->getMapper();
    }

    /**
     * Creates a mapper used by the service.
     *
     * @return OfficeBuilder_Model_Mapper_Inventory
     */
    public function getMapper()
    {
        return $this->_getMapperFactory()->getInventory();
    }
}
