<?php
/**
 * Copied from Zend Framework Memcache handler
 *
 * Custom session save handler for Memcache
 * Memcache session type sends the session.gc_lifetime to memcache which manages entry expiration on its own
 */

class OfficeBuilder_Session_SaveHandler_Memcache
    implements Zend_Session_SaveHandler_Interface
{
    const CACHE             = 'cache';
    const CACHE_KEY_PREFIX  = 'SESS_';
    const RETRY_USECONDS = 5000;
    const RETRY_LIMIT = 5;

    /**
     * The cache object
     *
     * @var Zend_Cache
     */
    protected $_cache = null;

    /**
     * Constructor
     *
     * $config is an instance of Zend_Config or an array of key/value pairs containing configuration options for
     * Zend_Session_SaveHandler_Cache.
     *
     * These are the configuration options for Zend_Session_SaveHandler_Cache
     *
     * cache             => (Zend_Cache) cache
     *
     * @param  Zend_Config|array $config User-provided configuration
     * @throws Zend_Session_SaveHandler_Exception
     * @return OfficeBuilder_Session_SaveHandler_Memcache
     */
    public function __construct($config)
    {
        if ($config instanceof Zend_Config)
        {
            $config = $config->toArray();
        }
        else if (!is_array($config))
        {
            /**
             * @see Zend_Session_SaveHandler_Exception
             */
            require_once 'Zend/Session/SaveHandler/Exception.php';

            throw new Zend_Session_SaveHandler_Exception(
                '$config must be an instance of Zend_Config or array of key/value pairs containing '
                . 'configuration options for Zend_Session_SaveHandler_DbTable and Zend_Db_Table_Abstract.');
        }

        /**
         * Although only one config option is supported, this code remains to allow future expansion
         * and for consistency with Zend_Session_SaveHandler_DbTable
         */
        foreach ($config as $key => $value) {
            do {
                switch ($key) {
                    case self::CACHE:
                        $this->setCache($value);
                        break;
                    default:
                        // unrecognized options passed to parent::__construct()
                        break 2;
                }
                unset($config[$key]);
            } while (false);
        }
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        Zend_Session::writeClose();
    }

    /**
     * Open Session
     *
     * @param string $save_path
     * @param string $name
     * @return boolean
     */
    public function open($save_path, $name)
    {
        //  // Ensures a clean shutdown of the session
        // Ref: http://www.php.net/manual/en/function.session-set-save-handler.php
        session_register_shutdown();
        return true;
    }

    /**
     * Close session
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        return $this->getCache()->load($this->_getCacheKey($id));
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     * @return boolean
     */
    public function write($id, $data)
    {
        // Save the session data
        // retry loop is to ensure session is not invalidated on write collision; last write always wins
        $retry = self::RETRY_LIMIT;
        while ($retry-- != 0 && !$rc = $this->getCache()->save($data, $this->_getCacheKey($id)))
        {
            usleep(self::RETRY_USECONDS);
        }

        return $rc;
    }

    /**
     * Destroy session
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this->getCache()->remove($this->_getCacheKey($id));
    }

    /**
     * Garbage Collection
     * This is unnecessary for memcache usage as maxlifetime is passed to memcache set commands;
     *   ie. memcache handles entry expiration on its own.
     * @param int $maxlifetime
     * @return true
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    public function getCache()
    {
        return $this->_cache;
    }

    public function setCache(Zend_Cache_Core $cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    protected function _getCacheKey($id)
    {
        return self::CACHE_KEY_PREFIX . $id;
    }
}