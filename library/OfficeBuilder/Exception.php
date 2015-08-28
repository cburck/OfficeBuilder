<?php
/**
 * OfficeBuilder Exception Class and static handler.
 *
 * Wrapper for catching errors and reporting them.
 *
 * @category   OfficeBuilder
 * @package    OfficeBuilder_Application
 * @subpackage Log
 */

class OfficeBuilder_Exception extends ErrorException
{
	protected $_logged;
    
    static $_errorInfo = '';
    static $_errorEmail = 'chris@burck.com';
    static $_friendlyResponse = 'Looks like we messed something up...Customer Support has been notified and will address the issue and respond to you soon.';

    function __construct($message, $code = null, $severity = E_ERROR, $filename = null, $lineno = null, array $context = array())
    {  
        parent::__construct($message, $code, $severity, $filename, $lineno);
    }
    
    public function setLogged($logged)
    {
    	$this->_logged = (bool)$logged;
    }
    
    public function isLogged()
    {
    	return $this->_logged;
    }

    /**
     * Static exception catcher
     * Logs and appropriately emails custom errors.
     *
     * @param Exception $e
     * @return array
     */
    public static function catchException(Exception $e)  
    {
		// IF PASSED EXCEPTION IS OF EXCEPTION TYPE, DO SOME EXTRA CHECKS TO AVOID DUPLICATE LOGGING
		if ($e instanceof OfficeBuilder_Exception)
		{
			if ($e->isLogged() && in_array(APPLICATION_ENV, array('production', 'staging')))
			{
				if (in_array($e->getCode(), array(E_NOTICE, E_WARNING, E_STRICT))) return;
				return self::$_friendlyResponse;
			}
		}
               
        self::buildInfo($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), strip_tags($e->getTraceAsString()));
        self::logInfo();

        // DO NOT EMAIL NOTICES OR WARNING, LOG ENTRIES ARE SUFFICIENT
        if (in_array($e->getCode(), array(E_NOTICE, E_WARNING, E_STRICT) ) ) return;
        
        self::mailInfo();
        
        return self::$_friendlyResponse;
    }

    /**
     * Set OfficeBuilder_Exception::errorHandler() as error handling function.
     *
     * @param integer $error_types (optional) used to mask the triggering of the OfficeBuilder_Exception::errorHandler() function.
     * @return boolean
     */
    public static function setErrorHandler($error_types = E_ALL)
    {
    	// MUST BE CALLED BY SYSTEM TO ENABLE PHP ERROR EXCEPTION HANDLING
        set_error_handler('OfficeBuilder_Exception::errorHandler', (int)$error_types);

        return true;  
    }
     
    /**
     * Alias for OfficeBuilder_Exception::setErrorHandler()
     *
     * @see OfficeBuilder_Exception::setErrorHandler()
     */
    public static function handleErrors($error_types = E_ALL)
    {
        return self::setErrorHandler($error_types);
    }

    /**
     * Error handling function. Used by OfficeBuilder_Exception::setErrorHandler()
     * this is for trapping PHP Errors only and does not intercept standard or custom exceptions.
     *
     * @param int $errno Level of the error raised
     * @param string $errstr Error message
     * @param string $errfile Filename that the error was raised in
     * @param integer $errline Line number the error was raised at
     * @param array $context Array that points to the active symbol table at the
     * point the error occurred
     * @throws OfficeBuilder
     * @return void
     */
    public static function errorHandler($errno = E_ERROR, $errstr, $errfile, $errline, $context)
    {
    	// DO NOT SEND EMAILS OR LOGS ON NOTICES
		if ($errno === E_NOTICE) return true;

        // DO NOT LOG WARNINGS, ETC IN PRODUCTION ENVIRONMENT
        if ( in_array(APPLICATION_ENV, array('production'))
          && in_array($errno, array(E_WARNING, E_STRICT)) ) return true;
		
    	self::buildInfo($errno, $errstr, $errfile, $errline, $context);
    	self::logInfo();
    	
    	// DO NOT THROW EXCEPTION FOR WARNINGS
    	if (in_array($errno, array(E_WARNING, E_STRICT)) ) return true;
    	
    	self::mailInfo();
		
		$exception = new OfficeBuilder_Exception($errstr, 0, $errno, $errfile, $errline, $context);
		$exception->setLogged(true);
		
		throw $exception;
    }

    /**
     * prepares message based on system state and error info
     *
     * @param integer $errno Error Severity
     * @param string $errstr Error message
     * @param string $errfile Filename that the error was raised in
     * @param integer $errline Line number the error was raised at
     * @param array $context Array that points to the active symbol table at the point the error occurred
     * @return string
     */
    public static function buildInfo($errno, $errstr, $errfile, $errline, $context)
    {
		$userInfo = array();

		$errInfo = self::getErrorDesc($errno). "($errno) $errstr occurred at line $errline in $errfile";
		
		if (!empty($userInfo))
		{
			$xtraInfo = '';
			foreach($userInfo as $key => $info)
			{
				$xtraInfo .= "$key: $info\r\n";
			}
			$errInfo = $xtraInfo."\r\n".$errInfo;
		}

		self::$_errorInfo = $errInfo;
		
    	return $errInfo;
    }
    
    public static function buildInfoFromException($ex = null)
    {
    	if ($ex instanceof Exception)
		{
    		self::buildInfo($ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getPrevious());
    	}
    	else
		{
    		self::buildInfo(0, 'Unknown Exception', $_SERVER['PHP_SELF'], '0', null);
    	}
    }

    /**
     * creates a logging object and logs the _errorInfo data
     */
    public static function logInfo()
    {
    	if (!file_exists(LOG_PATH)) return false;
    	
    	if (!self::$_errorInfo) return false;
    	
		$logger = new OfficeBuilder_Log();
		$logger->setLogger(OfficeBuilder_Log::buildLogger('error'));
		
		$logger->err(self::$_errorInfo);
		
		unset($logger);
		
    	return true;
    }
    
    /**
     * emails _errorInfo to errors account
     */
	public static function mailInfo()
    {
		if (in_array(APPLICATION_ENV, array('development','testing') )) return;
		
		$mail = new Zend_Mail();
		$mail->setBodyText(self::$_errorInfo);
		$mail->addTo(self::$_errorEmail);
		$mail->setSubject('EXCEPTION CAUGHT on '.$_SERVER['HTTP_HOST']);
		$mail->setFrom('apache@'.$_SERVER['SERVER_NAME']);
		$mail->send();
	}
	
	public static function getErrorDesc($errno)
    {
		//define PHP error types
		$errorDesc = array (E_ERROR           => "Error",
				E_WARNING         => "Warning",
				E_PARSE           => "Parsing Error",
				E_NOTICE          => "Notice",
				E_CORE_ERROR      => "Core Error",
				E_CORE_WARNING    => "Core Warning",
				E_COMPILE_ERROR   => "Compile Error",
				E_COMPILE_WARNING => "Compile Warning",
				E_USER_ERROR      => "User Error",
				E_USER_WARNING    => "User Warning",
				E_USER_NOTICE     => "User Notice",
				E_STRICT          => "Runtime Notice",
				E_RECOVERABLE_ERROR => "Recoverable Error",
				E_DEPRECATED	  => "Deprecated",
				E_USER_DEPRECATED => "User Deprecated",
				E_ALL			  => "All Errors",
				1045			  => "DB Access Denied");
		
		if (in_array($errno, array_keys($errorDesc)))
		{
			return $errorDesc[$errno]." [$errno]: ";
		}
		else
		{
			return "[$errno]";
		}
	}

	/**
	 * Static method to call register shutdown function.
	 */
	public static function registerShutdownFunction()
    {
		register_shutdown_function('OfficeBuilder_Exception::shutdownFunction');
	}

	/**
	 * Cache fatal errors.
	 */
	public static function shutdownFunction()
    {
		$error = error_get_last();
		
		if (in_array(APPLICATION_ENV, array('production','staging')) && $error && in_array($error['type'], array(E_ERROR, E_USER_ERROR)))
		{
		    $body = 'Fatal Error: '.$error['message']."\n"
					. 'on line '.$error['line'].' of '.$error['file']."\n\n";
				
		    $reportItems = array('http_host','request_uri','remote_addr','request_method','http_user_agent');
		    foreach ($reportItems as $item)
			{
				$body .= "$item:\t\t".$_SERVER[strtoupper($item)]."\n";
			}
				
			if ($_SERVER['REQUEST_METHOD'] != 'GET')
			{
				$body .= "POST Data:\n";
		
				foreach ($_POST as $p => $pval) {
					$body .= "\t$p\t$pval\n";
				}
			}

			include_once('OfficeBuilder/Exception.php');

			mail(OfficeBuilder_Exception::$_errorEmail, 'FATAL ERROR on '.$_SERVER['HTTP_HOST'], $body);
		
			header("Location: /error.html");
			die;
		}
	}
}