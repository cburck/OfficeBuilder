<?php
/**
 * File System Handler for OfficeBuilder
 *
 * @uses       Zend_Loader_Autoloader_Resource
 * @uses       Zend_Cache
 * @package    OfficeBuilder_FileSystem
 * @subpackage Broker
 */

class OfficeBuilder_FileSystem_Broker
{
	const CACHE = 'cache';
	const CONFIGS = 'configs';
	const LOGS = 'logs';
	const SESSIONS = 'sessions';
	const TEMP = 'temp';

	static public $storageFolders = array(
		self::CACHE => 'cache',
		self::CONFIGS => 'configs',
		self::LOGS => 'logs',
		self::SESSIONS => 'sessions',
		self::TEMP => 'temp'
	);
	
	static public $cachedFolders = array();
	
	static public $hasLiveCache = false;

    private function __construct()
	{
        // STATIC SINGLETON
        return false;
    }

    /**
     * Initializes Zend Cache usage of all available filesystem paths.
	 *
     * @throws OfficeBuilder_Exception
     */
    static public function init()
	{
		if (!self::$hasLiveCache)
		{
			if (!defined('STORAGE_PATH'))
			{
				throw new OfficeBuilder_Exception('Storage path not defined.');
			}
			
			if (!is_writable(STORAGE_PATH))
			{
				if (!is_dir(STORAGE_PATH) && !mkdir(STORAGE_PATH))
				{
					throw new OfficeBuilder_Exception('Unable to create or write to storage path.');
				}
			}
			
			require_once('Zend/Registry.php');

			if (Zend_Registry::isRegistered('cache'))
			{
				$cache = Zend_Registry::get('cache');
                $cachedFolders = $cache->load('cachedFolders');

                if (is_array($cachedFolders))
				{
					self::$cachedFolders = $cachedFolders;
				}

				self::$hasLiveCache = true;
			}
		}
	}

    /**
     * Returns a registered broker path by given OfficeBuilder_FileSystem_Broker class constant.
	 *
     * @param string $pathKey
     * @return string
     */
    static public function getPath($pathKey)
	{
		self::init();
		
		if (count(self::$cachedFolders) > 0 && isset(self::$cachedFolders[$pathKey]))
		{
			return self::$cachedFolders[$pathKey];
		}
		else
		{
			$destPath = STORAGE_PATH . DIRECTORY_SEPARATOR . self::$storageFolders[$pathKey];
			self::_createPath($destPath);
			self::$cachedFolders[$pathKey] = $destPath . DIRECTORY_SEPARATOR;
			self::_saveCachedFolders();
			return self::$cachedFolders[$pathKey];
		}
	}

    /**
     * @param string $path
     * @return bool
     * @throws OfficeBuilder_Exception
     */
    static protected function _createPath($path)
	{
        if (!is_dir($path))
		{
            if (!mkdir($path, 0755, true))
			{
                throw new OfficeBuilder_Exception("Unable to access or create '$path' folder for site.");
            }
        }

        return true;
    }

    /**
     * @throws OfficeBuilder_Exception
     */
    static protected function _saveCachedFolders()
	{
		try
		{
			require_once('Zend/Registry.php');

			if (Zend_Registry::isRegistered('cache'))
			{
				$cache = Zend_Registry::get('cache');
				$cache->save(self::$cachedFolders, 'cachedFolders');
			}
		}
		catch (OfficeBuilder_Exception $e)
		{
			throw new OfficeBuilder_Exception('Unable to save cached FileSystem folders: '. $e->getMessage());
		}		
	}

    /**
     * Returns true if working from a development or testing environment
	 *
     * @return bool
     */
    static public function isDevEnv()
	{
        return in_array(APPLICATION_ENV, array('testing','development'));
    }
}
