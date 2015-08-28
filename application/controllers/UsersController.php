<?php
/**
 * Users Controller
 *
 * @subpackage Controller
 */
class UsersController extends OfficeBuilder_Controller
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
            ->addActionContext('set-favorite-color','json')
            ->initContext();
    }

    /**
     * Doesn't do anything, yet.
     */
    public function indexAction()
    {
        //send user home
        $this->redirect('/');
    }

    /**
     * Signup for account
     */
    public function signupAction()
    {

    }

    /**
     * AJAX handler for setting user's favorite color.
     * Outputs JSON per context switch.
     */
    public function setFavoriteColorAction()
    {
        $out = new stdClass();

        try
        {
            //get params
            $id = $this->_request->getParam('id');
            $favoriteColor = $this->_request->getParam('favorite_color');

            //lookup user
            $user = $this->_serviceFactory->getUser()->getById($id);

            //validate and save
            if ($user && in_array($favoriteColor, OfficeBuilder_Model_User::getColorOptions()))
            {
                //update color
                $user->setFavoriteColor($favoriteColor);

                //save
                $this->_serviceFactory->getUser()->save($user);

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
}