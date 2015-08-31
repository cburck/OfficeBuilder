<?php
/**
 * User Model Inventory
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Mapper_Inventory extends OfficeBuilder_Model_Mapper_Abstract
{
    /**
     * Get the table used by this mapper.
     *
     * @return OfficeBuilder_Model_DbTable_Inventory
     */
    public function getTable()
    {
        return new OfficeBuilder_Model_DbTable_Inventory();
    }

    /**
     * Create a new instance of OfficeBuilder_Model_Inventory
     *
     * @param OfficeBuilder_Model_Product $product
     * @return OfficeBuilder_Model_Inventory
     */
    public function create(OfficeBuilder_Model_Product $product)
    {
        $inventory = new OfficeBuilder_Model_Inventory();
        $inventory->setProduct($product);

        return $inventory;
    }

    /**
     * Save an instance of OfficeBuilder_Model_Inventory
     *
     * This method handles creating new records as well as saving existing ones.
     * If the model does not have an id, a new record is created.
     *
     * @param OfficeBuilder_Model_Inventory $inventory Model to be saved
     * @param bool $suppressFetch Tell the method to not reload the data into the returned model
     * @return OfficeBuilder_Model_Inventory
     */
    public function save(OfficeBuilder_Model_Inventory $inventory, $suppressFetch=true)
    {
        $table = $this->getTable();

        $data = array(
            'product_id' => $inventory->getProduct()->getId(),
            'units' => (int)$inventory->getUnits(),
            'ppu' => (float)$inventory->getPpu(),
            'datetime' => $inventory->getDateTime()
        );

        return $this->_save($table, $inventory, $data);
    }

    /**
     * Return collection of inventory for product after date.
     * Rounds down to the nearest hour.
     *
     * @param OfficeBuilder_Model_Product $product
     * @param DateTime $date
     * @return array
     */
    public function getInventoryForProductAfterDate(OfficeBuilder_Model_Product $product, DateTime $date)
    {
        $table = $this->getTable();
        $select = $table->select()->where('product_id = ?', $product->getId());

        //handle datetime based on type of product
        switch ($product->getType())
        {
            case OfficeBuilder_Model_Product::PRODUCT_TYPE_DAY:
                $select->where('datetime >= ?', $date->format('Y-m-d 00:00:00'));
                break;

            case OfficeBuilder_Model_Product::PRODUCT_TYPE_DAYTIME:
                $select->where('datetime >= ?', $date->format('Y-m-d H:00:00'));
                break;

            default:
                $select->where('datetime >= ?', $date->format('Y-m-d H:i:s'));
                break;
        }

        return $this->_loadSet($table->fetchAll($select));
    }

    /**
     * Load data from the database into a new model instance.
     *
     * @param $data
     * @param bool $loadRelated
     * @return OfficeBuilder_Model_Inventory
     */
    protected function _load($data, $loadRelated=true)
    {
        if (!is_array($data))
        {
            $data = $data->toArray();
        }

        //get related models
        $product = $this->_getMapperFactory()->getProduct()->getById($data['product_id']);

        return new OfficeBuilder_Model_Inventory($data['id'], $product, $data['units'], $data['ppu'], $data['datetime'], $data['created_on']);
    }
}
