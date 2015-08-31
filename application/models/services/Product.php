<?php
/**
 * Product Service Class
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Service_Product extends OfficeBuilder_Model_Service_Abstract
{
    /**
     * Create a new OfficeBuilder_Model_Product record.
     *
     * @return OfficeBuilder_Model_Product
     */
    public function create()
    {
        return $this->getMapper()->create();
    }

    /**
     * Save an instance of OfficeBuilder_Model_Product
     *
     * @param OfficeBuilder_Model_Product $user
     * @param $data New values for the model's properties
     * @return OfficeBuilder_Model_Product
     */
    public function save(OfficeBuilder_Model_Product $product, $data = null)
    {
        if ($data)
        {
            if (isset($data['name']))
            {
                $product->setName($data['name']);
            }

            if (isset($data['description']))
            {
                $product->setDescription($data['description']);
            }

            if (isset($data['type']))
            {
                $product->setType($data['type']);
            }

            if (isset($data['category']))
            {
                $product->setType($data['category']);
            }
        }

        return $this->getMapper()->save($product);
    }

    /**
     * Return collection products by category.
     *
     * @return array
     */
    public function getProductsByCategory($category)
    {
        return $this->getMapper()->getProductsByCategory($category);
    }

    /**
     * Gets product set model that describes a set of products and related inventory.
     *
     * @param $category
     * @param DateTime $date
     * @return OfficeBuilder_Model_Product_Set
     */
    public function getProductSetForCategoryAndDate($category, DateTime $date)
    {
        //get products
        $products = $this->getProductsByCategory($category);
        $productInventory = array();
        $inventoryPurchasedUnits = array();

        //get inventory for products
        foreach ($products as $currProduct)
        {
            //get product inventory
            $productInventory[$currProduct->getId()] = $this->_getFactory()->getInventory()->getInventoryForProductAfterDate($currProduct, $date);

            //get inventory purchased units
            foreach ($productInventory[$currProduct->getId()] as $currInventory)
            {
                $inventoryPurchasedUnits[$currInventory->getId()] = $this->_getFactory()->getPurchase()->getPurchasedUnitsForInventory($currInventory);
            }
        }

        return new OfficeBuilder_Model_Product_Set($products, $productInventory, $inventoryPurchasedUnits);
    }

    /**
     * Loads user purchases into product set.
     *
     * @param OfficeBuilder_Model_Product_Set $productSet
     * @param OfficeBuilder_Model_User $user
     */
    public function loadUserPurchasesIntoProductSet(OfficeBuilder_Model_Product_Set $productSet, OfficeBuilder_Model_User $user)
    {
        $purchasesService = $this->_getFactory()->getPurchase();

        //get purchases for user
        $purchases = $purchasesService->getPurchasesForInventoryIdsAndUser($productSet->getInventoryIds(), $user);

        //group purchases by inventory_id
        $purchasesByInventoryId = array();
        foreach ($purchases as $currPurchase)
        {
            $purchasesByInventoryId[$currPurchase->getInventory()->getId()] = $currPurchase;
        }

        //load user purchases into product set
        $productSet->setUserPurchases($purchasesByInventoryId);
    }

    /**
     * @return OfficeBuilder_Model_Mapper_Product
     */
    public function _createMapper()
    {
        return $this->getMapper();
    }

    /**
     * Creates a mapper used by the service.
     *
     * @return OfficeBuilder_Model_Mapper_Product
     */
    public function getMapper()
    {
        return $this->_getMapperFactory()->getProduct();
    }
}
