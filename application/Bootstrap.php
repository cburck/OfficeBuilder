<?php
/**
 * Application bootstrap
 *
 * @category   OfficeBuilder
 * @subpackage Bootstrap
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected $_cacheExists;

    /**
     * Some filesystem constants from the ENV.
     */
	public function _initConstants()
    {
		defined('STORAGE_PATH') || define('STORAGE_PATH', getenv('STORAGE_PATH') ? getenv('STORAGE_PATH') : realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'data'));

        // Define current working directory (useful to know at times)
		defined('CWD') || define('CWD', getcwd() . '/');

		//set LOG_PATH now
		require_once('OfficeBuilder/FileSystem/Broker.php');
		defined('LOG_PATH') || define('LOG_PATH', OfficeBuilder_FileSystem_Broker::getPath(OfficeBuilder_FileSystem_Broker::LOGS));

        //timezone
        date_default_timezone_set('America/Chicago');
	}
	
    /**
	 * Initialize auto-loading for the application.
	 */
	public function _initLoaders()
    {
		$loader = Zend_Loader_Autoloader::getInstance();
		
		// TELLS THE LOADER TO LOOK IN /library/OfficeBuilder
		$loader->registerNamespace('OfficeBuilder_');
		
		$loader->setFallbackAutoloader(true);
		
		// TELLS THE LOADER HOW TO AUTOLOAD APPLICATION RESOURCES IN /application
		$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => 'OfficeBuilder',
            'basePath'  => APPLICATION_PATH));
		
		$resourceLoader->addResourceTypes(array(
            'dbtable' => array(
                'namespace' => 'Model_DbTable',
                'path'      => 'models/dbTables',
            ),
            'mappers' => array(
                'namespace' => 'Model_Mapper',
                'path'      => 'models/mappers',
            ),
            'form'    => array(
                'namespace' => 'Form',
                'path'      => 'forms',
            ),
            'model'   => array(
                'namespace' => 'Model',
                'path'      => 'models',
            ),
            'plugin'  => array(
                'namespace' => 'Plugin',
                'path'      => 'plugins',
            ),
            'service' => array(
                'namespace' => 'Model_Service',
                'path'      => 'models/services',
            ),
            'viewhelper' => array(
                'namespace' => 'View_Helper',
                'path'      => 'views/helpers',
            ),
            'viewfilter' => array(
                'namespace' => 'View_Filter',
                'path'      => 'views/filters',
            )));
	}

    /**
     * Initialize Error Handling
     */
    public function _initErrorHandling()
    {
        if (in_array(APPLICATION_ENV, array('testing','development')))
        {
            error_reporting(E_ALL);
            ini_set('display_errors', 'on');
            Zend_Loader_Autoloader::getInstance()->suppressNotFoundWarnings(false);
        }

        OfficeBuilder_Exception::registerShutdownFunction();
        OfficeBuilder_Exception::setErrorHandler();
    }

	/**
	 * Sets the initial routes.
	 */
    public function _initRoutes()
    {
    	$this->bootstrap('FrontController');
    }

    /**
     * Sets up caching.
     */
    public function _initCache()
    {
        if (defined('NOCACHE') && NOCACHE == true)
        {
            return;
        }

        $cacheDir = OfficeBuilder_FileSystem_Broker::getPath(OfficeBuilder_FileSystem_Broker::CACHE);
    	$this->_cacheExists = file_exists($cacheDir);
    	$cacheDomain = 'OB';

    	if ($this->_cacheExists)
        {
            $frontendOpts = array(	'lifetime' => 28800,
    				'automatic_serialization' => true,
    				'ignore_user_abort' => true,
    				'cache_id_prefix' => $cacheDomain );

            $backendType = 'File';
            $backendOpts  = array('cache_dir' => $cacheDir, 'automatic_cleaning_factor' => 0 );

            $coreCache = Zend_Cache::factory('Core', $backendType, $frontendOpts, $backendOpts);

    		Zend_Registry::set('cache', $coreCache);
    	}
    }

	/**
	 * Initialize Database resource.
	 */
	public function _initDb()
    {
		$this->bootstrap('multidb');
		
		$dbResource = $this->getPluginResource('multidb');

		Zend_Registry::set("appdb", $dbResource->getDb('appdb'));
	}

    /**
     * Setup and register controller plugins
     */
    public function _initPlugins()
    {
    	$this->bootstrap('FrontController');
    	
    	$auth = Zend_Auth::getInstance();

    	$this->frontController->registerPlugin(new OfficeBuilder_Controller_Plugin_Auth($auth));
    }

    /**
     * Initializes a Zend_Logger.
     *
     * NOTE: Since the method is protected it behaves as a resource method and is accessible from the bootstrap instance via getResource('Log')
     *
     * @return Zend_Log
     */
    protected function _initLog()
    {
    	if (!file_exists(LOG_PATH))
        {
            // Avoids errors if there is no log path
            Zend_Registry::set('logger', new Zend_Log_Writer_Null());

            return;
        }

    	$stream = @fopen(LOG_PATH.'error.log', 'a', false);

        if ($stream)
        {
            $logger = new Zend_Log();
            $writer = new Zend_Log_Writer_Stream($stream);

            $logger->addWriter($writer);
            Zend_Registry::set('logger', $logger);

            return $logger;
        }
    }

    /**
     * Initialize Session Handler
     */
    public function _initCoreSession()
    {
        if (defined('NOSESSION') && NOSESSION == 'true')
        {
            return;
        }
        
        Zend_Session::setOptions(array('save_path' => OfficeBuilder_FileSystem_Broker::getPath(OfficeBuilder_FileSystem_Broker::SESSIONS)));

    	$this->bootstrap('session');

    	Zend_Session::start(array('throw_startup_exceptions' => true));
        
        //ENSURE a clean shutdown of the session, regardless of session handler
        session_register_shutdown();
    }
}
