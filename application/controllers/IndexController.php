<?php
/**
 * Index Bootstrap.
 *
 * Forces user to home directory.
 *
 * @subpackage Controller
*/
class IndexController extends OfficeBuilder_Controller
{
    /**
     * Init Zend Controller
     */
    public function init()
    {
        parent::init();
    }

    /**
     * User profile or home page.
     */
    public function indexAction()
    {
        //setup
        $purchasesService = $this->_serviceFactory->getPurchase();

        //get purchases
        if ($this->_user)
        {
            //get purchases
            $this->view->purchases = $purchasesService->getPurchasesForUser($this->_user);

            //sort by date DESC
            OfficeBuilder_Model_Purchase::sortByDate($this->view->purchases, 'desc');

            //change password form
            $passwordForm = new OfficeBuilder_Form_ChangePassword();

            if ($this->_request->isPost())
            {
                $params = $this->_request->getParams();

                if ($passwordForm->isValid($params))
                {
                    $this->_serviceFactory->getUser()->updatePassword($this->_user, $params['password']);
                    $this->view->passwordMsg = 'Successfully updated password.';
                }
                else
                {
                    $this->view->passwordError = 'Error updating password. Please try again.';
                }
            }

            $this->view->passwordForm = $passwordForm;
        }
    }
}