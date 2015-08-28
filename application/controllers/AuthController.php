<?php
/**
 * Auth Controller
 * 
 * Logs users in and out.
 *
 */
class AuthController extends OfficeBuilder_Controller
{
    /**
     * @var OfficeBuilder_Model_Service_Factory
     */
    protected $_serviceFactory;

    /**
     * Init method for auth controller
     */
    public function init()
    {
        parent::init();

        //service factory
        $this->_serviceFactory = OfficeBuilder_Model_Service_Factory::getFactory();
    }

    /**
     * Index placeholder that forwards to login action.
     */
    public function indexAction()
    {
        $this->forward('login');
    }

    /**
     * Prompt user for login credentials.
     */
    public function loginAction()
    {
        //setup
        $auth = Zend_Auth::getInstance();

        //default state
        $this->view->loginFailed = false;

        $authenticated = false;

        $loginForm = new OfficeBuilder_Form_Login();

        //PROCESS LOGIN FORM
        if ($this->_request->isPost())
        {
            $params = $this->_request->getParams();
            if ($loginForm->isValid($params))
            {
                $db = Zend_Db_Table::getDefaultAdapter();
                $adapter = new Zend_Auth_Adapter_DbTable(
                    $db, 'Users', 'email', 'password', 'SHA1(?)'
                );

                $adapter->setIdentity($loginForm->getValue('email'));
                $adapter->setCredential($loginForm->getValue('password'));
                $result = $auth->authenticate($adapter);

                //TEST AUTHENTICATION
                $authenticated = $result->isValid();
            }

            //show failed message if something didn't work
            if (!$authenticated)
            {
                $this->view->loginFailed = true;
            }
        }

        if ($authenticated)
        {
            //GET ACTUAL USER MODEL
            $UserRow = $adapter->getResultRowObject('id');
            $user = $this->_serviceFactory->getUser()->getById($UserRow->id);

            if (!$user || !$user->isActive())
            {
                $auth->clearIdentity();
                $this->view->loginFailed = true;
            }
            else
            {
                //Regenerate the session id to ensure a clean session and timeout
                Zend_Session::regenerateId();

                //STORE USER MODEL IN SESSION
                $session = new Zend_Session_Namespace('ob');
                $session->user = $user;

                if (!empty($reroute) && $reroute != '/' && $reroute != '/auth/login')
                {
                    $this->redirect($reroute);
                }
                else
                {
                    $this->redirect('/');
                }
            }
        }

        $this->view->loginForm = $loginForm;
    }

    /**
     * Log user out of the system and reset session.
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();

        $session = new Zend_Session_Namespace('ob');
        $session->unsetAll();

        Zend_Session::destroy(true);

        $this->redirect('/');
    }
}
