<?php
/**
 * OfficeBuilder Controller Abstract.
 *
 * @uses       Zend_Loader_Autoloader_Resource
 * @subpackage Controller
 */

class OfficeBuilder_Controller extends Zend_Controller_Action
{
    //SESSION TIMEOUT RESET IN SECONDS
    const SESSION_TIMEOUT = 28800;  // 8 hours

    /**
     * Session.
	 *
     * @var Zend_Session_Namespace
     */
	protected $session;	
	
	/**
	 * Currently logged in user.
	 *
	 * THIS CAN BE NULL IF NO USER IS ACTIVE.
	 *
	 * @var $_user OfficeBuilder_Model_User
	 */
	protected $_user;

    /**
     * Pre-instantiated service factory.
	 *
     * @var OfficeBuilder_Model_Service_Factory
     */
    protected $_serviceFactory;

	/**
	 * Last URI user visited.
	 */
    protected $_previousUri;

    /**
     * (non-PHPdoc)
     * @see library/Zend/Controller/Zend_Controller_Action#init()
     */
	public function init() 
	{
		$this->session = new Zend_Session_Namespace('ob');
		$this->_user = $this->session->user;
		
		//service setup
		$this->_serviceFactory = OfficeBuilder_Model_Service_Factory::getFactory();

        $this->_initSessionTimeout();
		
		//INIT VIEW
		if (!$this->_request->isXmlHttpRequest()) 
		{
			$this->_initView();
		}
	}

    /**
     * Resets the Session Timeout based on activity
     */
    protected function _initSessionTimeout() 
	{
		$this->session->setExpirationSeconds(self::SESSION_TIMEOUT);
	}

	/**
	 * Forces updated of logged in user.
	 * Used when user model is updated.
	 *
	 * @param OfficeBuilder_Model_User $user
	 */
	protected function updateUser(OfficeBuilder_Model_User $user)
	{
		$this->_user = $user;
		$this->session->user = $user;
	}
	
	/**
	 * Setups up standard view helps.
	 */
	protected function _initView()
	{
		$this->view->controller = $this->getRequest()->getControllerName();
		$this->view->action = $this->getRequest()->getActionName();
		$this->view->user = $this->_user;

		$this->_initCss();
		$this->_initjQuery();

		$this->view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
		$this->view->addHelperPath('OfficeBuilder/View/Helper/', 'OfficeBuilder_View_Helper');

		// INITIALIZE THE USERS MENU
		$this->_initNavigation();

		// INCLUDE FLASH MESSAGES FROM SESSION IF THEY ARE SET
		if ($this->session->flashQueue) 
		{
			if (!is_array($this->view->flashQueue)) 
			{
				$this->view->flashQueue = array();
			}

			// Merge session flash messages to view flash queue
			$this->view->flashQueue = array_merge($this->view->flashQueue, $this->session->flashQueue);

			// clear session flash queue messages
			$this->session->flashQueue = array();
		}

		if (count($this->session->flashHideQueue) > 0) 
		{
			if ($this->view->flashQueue) 
			{
				foreach ($this->view->flashQueue as $hash => $flash) 
				{
					if (in_array($hash, array_keys($this->session->flashHideQueue)) && $this->session->flashHideQueue[$hash] > 1) 
					{
						$this->view->flashQueue[$hash]->hide = true;
					}
				}
			}
		}
	}
	
	/**
	 * Initialize the users navigation menu based on their role.
	 */
	protected function _initNavigation() {
		
		$controllerName = $this->getRequest()->getControllerName();
		$actionName = $this->getRequest()->getActionName();
		
		// DETERMINE THE ACTIVE TAB
		$activeTab = $controllerName;

		// BUILD NAVIGATION
		$this->view->nav = new Zend_Navigation();

		// INITIALIZE BREADCRUMBS  	 	 
		$this->view->breadcrumbs = new Zend_Navigation(); 
	}
	
	/**
	 * Initializes the CSS for controller.
	 */
	protected function _initCss() 
	{
		// CONTROLLER MAY ADD THEIR OWN SPECIFIC STYLE SHEETS
		$this->view->headLink()->appendStylesheet('/css/appstyle.css'.'?'.OfficeBuilder_Version::APP, 'all', false);
		$this->view->headLink()->appendStylesheet('/css/plugins.css'.'?'.OfficeBuilder_Version::APP, 'all', false);
		
		if (file_exists(getcwd() . '/css/controllers/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getControllerName() . '.css'))
		{
			$this->view->headLink()->appendStylesheet('/css/controllers/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getControllerName() . '.css'.'?'.OfficeBuilder_Version::APP, 'screen,projection,print', false);
		}
			
		if (file_exists(getcwd() . '/css/controllers/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName() . '.css'))
		{
			$this->view->headLink()->appendStylesheet('/css/controllers/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName() . '.css'.'?'.OfficeBuilder_Version::APP, 'screen,projection,print', false);
		}
	}
	
	/**
	 * Initializes the JS for controller.
	 */
	protected function _initJQuery()
	{
		ZendX_JQuery::enableView($this->view);
		
		$this->view->jQuery()
                ->enable()
                ->uiEnable()
                ->setLocalPath('/js/jquery-1.4.4.min.js')
                ->setUiLocalPath('/js/jquery-ui-1.8.24.custom.min.js')
                ->addStylesheet('/css/jquery/css/smoothness/jquery-ui-1.8.24.custom.css');
		
		if (file_exists(getcwd() . '/js/controllers/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getControllerName() . '.js'))
		{
			$this->view->jQuery()->addJavascriptFile('/js/controllers/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getControllerName() . '.js'.'?'.OfficeBuilder_Version::APP);
		}
		
		if (file_exists(getcwd() . '/js/controllers/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName() . '.js'))
		{
			$this->view->jQuery()->addJavascriptFile('/js/controllers/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName() . '.js'.'?'.OfficeBuilder_Version::APP);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Action::preDispatch()
	 */
    public function preDispatch() 
	{
       	$this->_previousUri = $this->session->previousUri;
    	$this->ignorePreviousUri(false);
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch() 
	{
    	if (!$this->getRequest()->isXmlHttpRequest() && !$this->getRequest()->isPost() && !$this->session->ignorePreviousUri) 
		{
        	$this->session->previousUri = $this->getRequest()->getRequestUri();
    	}
    }
	
	/**
	 * Simple wrapper in case re-initializing the view causes problems.
	 */
	public function reinitView() 
	{
		$this->_initView();
	}
	
	/**
	 * Add a page level message to queue for display to the user via layout view. 
	 * 
	 * @param string $message The messages content.
	 * @param string $type The type of message you are sending to determine it's style.
	 * @param bool $useSession whether to pass the message via session flashQueue.
	 * @param bool $hide set to hide message after first time using hash
	 */
	public function flash($message, $type, $useSession = false, $hide = false)
	{
		$flashMsg = new stdClass();
		
		$flashHash = hash('crc32b',$type.$message);
		
		$flashMsg->message = $message;
		$flashMsg->type = $type;
		$flashMsg->hide = false;
		
		if (!is_array($this->session->flashHideQueue))
		{
			$this->session->flashHideQueue = array();
		}
		
		if ($useSession)
		{
			if (!is_array($this->session->flashQueue)) $this->session->flashQueue = array();
			$this->_addFlash($flashMsg, $this->session->flashQueue, $flashHash);
		}
		else
		{
			if (!is_array($this->view->flashQueue)) $this->view->flashQueue = array();
			$this->_addFlash($flashMsg, $this->view->flashQueue, $flashHash);
		}
		
		if ($hide && (in_array($flashHash, array_keys($this->session->flashQueue)) || in_array($flashHash, array_keys($this->view->flashQueue))))
		{
			if (!isset($this->session->flashHideQueue[$flashHash]))
			{
				$this->session->flashHideQueue[$flashHash] = 0;
			}
				
			$this->session->flashHideQueue[$flashHash]++;
		}
	}

	/**
	 * Adds messages to queue to preserve uniqueness.
	 *
	 * @param $msg
	 * @param $queue
	 * @param $hash
	 */
	protected function _addFlash($msg, &$queue, $hash)
	{
		foreach ($queue as $q)
		{
			if ($msg->message == $q->message)
				return;
		}
		
		$queue[$hash] = $msg;
	}

	/**
	 * Clears ALL flash messages.
	 */
	public function clearAllFlash()
	{
		$this->clearSessionFlash();
		$this->clearViewFlash();
	}

	/**
	 * Clears flash messages from session.
	 */
	public function clearSessionFlash()
	{
		$this->session->flashQueue = array();
	}

	/**
	 * Clears flash messages from view.
	 */
	public function clearViewFlash()
	{
		$this->view->flashQueue = array();
	}

	/**
	 * Returns the active user
	 * 
	 * @return OfficeBuilder_Model_User
	 */
	public function getUser() 
	{
		return $this->_user;
	}
	
    public function getPreviousUri()
	{
    	return $this->_previousUri;
    }
    
    public function ignorePreviousUri($ignore = true) 
	{
    	$this->session->ignorePreviousUri = $ignore;
    }

    public function redirectToSelf() 
	{
    	$this->redirect($this->getRequest()->getRequestUri());
    }

    public function redirectHome()
	{
		//does nothing, right now
    }
}
