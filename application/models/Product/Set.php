<?php
/**
 * Product Set
 * Contains collection of products, inventory, and purchased units.
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Product_Set extends OfficeBuilder_Model_Abstract
{

    protected $_products;
    protected $_productInventory;
    protected $_inventoryPurchasedUnits;
    protected $_userPurchases;

    /**
     * @param array $products
     * @param array $productInventory
     * @param array $inventoryPurchasedUnits
     */
    public function __construct(array $products, array $productInventory, array $inventoryPurchasedUnits)
    {
        $this->_products = $products;
        $this->_productInventory = $productInventory;
        $this->_inventoryPurchasedUnits = $inventoryPurchasedUnits;
        $this->_userPurchases = array();
    }

    /**
     * Returns array of all products.
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->_products;
    }

    /**
     * Returns true of product set has products, false otherwise.
     *
     * @return bool
     */
    public function hasProducts()
    {
        return (count($this->_products) > 0);
    }

    /**
     * Returns inventory for product.
     *
     * @param OfficeBuilder_Model_Product $product
     * @return array
     */
    public function getInventoryForProduct(OfficeBuilder_Model_Product $product)
    {
        return (array)$this->_productInventory[$product->getId()];
    }

    /**
     * Returns number items available for purchase for inventory.
     *
     * @param OfficeBuilder_Model_Inventory $inventory
     * @return int
     */
    public function getAvailableUnitsForInventory(OfficeBuilder_Model_Inventory $inventory)
    {
        return ($inventory->getUnits() - (int)$this->_inventoryPurchasedUnits[$inventory->getId()]);
    }

    /**
     * Sets purchases.
     *
     * @param array $purchases
     */
    public function setUserPurchases(array $purchases)
    {
        $this->_userPurchases = $purchases;
    }

    /**
     * Returns user purchase for inventory.
     *
     * @param OfficeBuilder_Model_Inventory $inventory
     * @return mixed
     */
    public function getUserPurchaseByInventory(OfficeBuilder_Model_Inventory $inventory)
    {
        return isset($this->_userPurchases[$inventory->getId()]) ? $this->_userPurchases[$inventory->getId()] : null;
    }

    /**
     * Get inventory IDs of all inventories in set.
     *
     * @return array
     */
    public function getInventoryIds()
    {
        return array_keys($this->_inventoryPurchasedUnits);
    }
}
		