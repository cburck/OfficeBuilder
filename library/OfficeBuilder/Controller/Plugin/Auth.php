<?php
/**
 * Controller plugin to verify a user is logged in.
 *
 * @subpackage Controller
 */

class OfficeBuilder_Controller_Plugin_Auth
    extends Zend_Controller_Plugin_Abstract
{
    /**
     * The instance of Zend_Auth for request.
     *
     * @var Zend_Auth
     */
    protected $_auth = null;

    /**
     * @param $auth Zend_Auth instance
     */
    public function __construct(Zend_Auth $auth)
    {
        $this->_auth = $auth;
    }

    /**
     * (non-PHPdoc)
     * @see library/Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract#preDispatch($request)
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        //set storage as session
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('ob'));

        //get session
        $session = new Zend_Session_Namespace('ob');
        $user = null;

        //fetch user model from database
        if ($session->user instanceof OfficeBuilder_Model_User)
        {
            $user = OfficeBuilder_Model_Service_Factory::getFactory()->getUser()->getById($session->user->getId());
        }

        //if auth identity is bad, user no longer exists, or user is no longer active: clear out user (log them out)
        if (!$this->_auth->hasIdentity() || !$user || !$user->isActive())
        {
            //make sure session user is cleared
            $session->user = null;
            $this->_auth->clearIdentity();

            /**
             * UNCOMMENT FOLLOWING TO FORCE REDIRECTION TO AUTH CONTROLLER
             */
            /*
            $reroute = NULL;
            $uri = $request->getRequestUri();
            if ($uri != '/' || $uri != '/auth/login')
                $reroute = $uri;

            // NOT AUTHENTICATED, FORWARD TO LOGIN FORM
            $request->setModuleName('default');
            $request->setControllerName('auth');
            $request->setActionName('login');
            $request->setParams(array('reroute'=>$reroute));
            */
        }
        else
        {
            //refresh user in session
            $session->user = $user;
        }
    }
}
