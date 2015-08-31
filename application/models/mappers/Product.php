<?php
/**
 * Product Model Inventory
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Mapper_Product extends OfficeBuilder_Model_Mapper_Abstract
{
    /**
     * Get the table used by this mapper.
     *
     * @return OfficeBuilder_Model_DbTable_Product
     */
    public function getTable()
    {
        return new OfficeBuilder_Model_DbTable_Product();
    }

    /**
     * Create a new instance of OfficeBuilder_Model_Product
     *
     * @return OfficeBuilder_Model_Product
     */
    public function create()
    {
        $product = new OfficeBuilder_Model_Product();

        return $product;
    }

    /**
     * Save an instance of OfficeBuilder_Model_Product
     *
     * This method handles creating new records as well as saving existing ones.
     * If the model does not have an id, a new record is created.
     *
     * @param OfficeBuilder_Model_Product $product Model to be saved
     * @param bool $suppressFetch Tell the method to not reload the data into the returned model
     * @return OfficeBuilder_Model_Product
     */
    public function save(OfficeBuilder_Model_Product $product, $suppressFetch=true)
    {
        $table = $this->getTable();

        $data = array(
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'type' => $product->getType(),
            'category' => $product->getCategory()
        );

        return $this->_save($table, $product, $data);
    }

    /**
     * Return collection products by category.
     *
     * @return array
     */
    public function getProductsByCategory($category)
    {
        $table = $this->getTable();
        $select = $table->select()->where('category = ?', $category);

        return $this->_loadSet($table->fetchAll($select));
    }

    /**
     * Load data from the database into a new model instance.
     *
     * @param $data
     * @param bool $loadRelated
     * @return OfficeBuilder_Model_Product
     */
    protected function _load($data, $loadRelated=true)
    {
        if (!is_array($data))
        {
            $data = $data->toArray();
        }

        return new OfficeBuilder_Model_Product($data['id'], $data['name'], $data['description'], $data['category'], $data['type'], $data['created_on']);
    }
}
