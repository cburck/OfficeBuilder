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
     * Signup for account.
     */
    public function signupAction()
    {
        //setup
        $userService = $this->_serviceFactory->getUser();

        //change password form
        $signupForm = new OfficeBuilder_Form_Signup();

        if ($this->_request->isPost())
        {
            $params = $this->_request->getParams();

            if ($signupForm->isValid($params))
            {
                //create user
                $user = $userService->create();
                $user = $userService->save($user, $params);
                $userService->updatePassword($user, $params['password']);

                //log user in
                $auth = Zend_Auth::getInstance();

                $adapter = new Zend_Auth_Adapter_DbTable(
                    Zend_Db_Table::getDefaultAdapter(), 'Users', 'email', 'password', 'SHA1(?)'
                );

                $adapter->setIdentity($params['email']);
                $adapter->setCredential($params['password']);
                $auth->authenticate($adapter);

                $this->session->user = $user;

                $this->redirect('/index/index/create/1');
            }
            else
            {
                $this->view->errorMsg = 'Please check form for errors and try again.';
            }
        }

        $this->view->signupForm = $signupForm;
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