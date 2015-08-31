<?php
/**
 * Products Controller
 *
 * @subpackage Controller
 */
class ProductsController extends OfficeBuilder_Controller
{
    /**
     * Init Zend Controller
     */
    public function init()
    {
        parent::init();

        //set context switches
        $contextSwitch = $this->_helper->getHelper('contextSwitch');

        $contextSwitch
            ->addActionContext('set-inventory-units','json')
            ->addActionContext('purchase','json')
            ->initContext();
    }

    /**
     * View products and purchase by category.
     */
    public function indexAction()
    {
        //setup
        $productService = $this->_serviceFactory->getProduct();

        //get hard-coded categories
        //@todo move into database/model/mapper/service
        $categories = OfficeBuilder_Model_Product::getCategories();
        $categoryIndexes = array_keys($categories);

        //determine category to display - by request or default
        $category = $this->getRequest()->getParam('category');
        if (!in_array($category, $categoryIndexes))
        {
            $category = $categoryIndexes[0];
        }

        //get product set for category
        $productSet = $productService->getProductSetForCategoryAndDate($category, new DateTime('now'));

        //get user purchases
        if ($this->_user)
        {
            $productService->loadUserPurchasesIntoProductSet($productSet, $this->_user);
        }

        $this->view->productSet = $productSet;
        $this->view->categoryLabel = $categories[$category];
    }

    /**
     * AJAX handler for purchasing something in inventory.
     */
    public function purchaseAction()
    {
        $out = new stdClass();

        try
        {
            //setup
            $purchaseService = $this->_serviceFactory->getPurchase();

            //check if user is logged in
            if (!$this->_user)
            {
                throw new Exception('Please login to purchase.');
            }

            //get params
            $inventoryId = $this->_request->getParam('inventory_id');

            //lookup inventory
            $inventory = $this->_serviceFactory->getInventory()->getById($inventoryId);
            if (!$inventory)
            {
                throw new Exception('Inventory not found or not valid.');
            }

            //check if item is sold out
            if (($inventory->getUnits() - (int)$purchaseService->getPurchasedUnitsForInventory($inventory)) < 1)
            {
                throw new Exception('Sorry. Item sold out.');
            }

            //see if user purchased this item
            $purchases = $purchaseService->getPurchasesForInventoryIdsAndUser(array($inventory->getId()), $this->_user);
            if ($purchases)
            {
               throw new Exception('Item already purchased. You can only make a purchase once.');
            }
            else
            {
                //purchase one item from inventory
                $purchase = $purchaseService->create($inventory, $this->_user, 1);
                $purchaseService->save($purchase);

                //get new inventory units
                $out->inventoryUnits = ($inventory->getUnits() - (int)$purchaseService->getPurchasedUnitsForInventory($inventory));
                $out->error = false;
            }
        }
        catch (Exception $e)
        {
            $out->error = true;
            $out->errorMsg = $e->getMessage();
        }

        $this->view->response = $out;
    }

    /**
     * AJAX handler for updating inventory units.
     * @todo Move to inventory controller if inventory editor developed.
     */
    public function setInventoryUnitsAction()
    {
        $out = new stdClass();

        try
        {
            //check if user can change units
            if (!$this->_user || !$this->_user->isAdmin())
            {
                throw new Exception('You are not authorized to change units.');
            }

            //get params
            $inventoryId = $this->_request->getParam('inventory_id');
            $units = $this->_request->getParam('units');

            //lookup inventory
            $inventory = $this->_serviceFactory->getInventory()->getById($inventoryId);
            if (!$inventory)
            {
                throw new Exception('Inventory not found or not valid.');
            }

            //update units
            $inventory->setUnits($units);
            $this->_serviceFactory->getInventory()->save($inventory);

            $out->error = false;
        }
        catch (Exception $e)
        {
            $out->error = true;
            $out->errorMsg = $e->getMessage();
        }

        $this->view->response = $out;
    }
}