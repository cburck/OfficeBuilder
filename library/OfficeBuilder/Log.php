<?php
/**
 * Generic nested logger.
 * Based on and extends Zend Framework Log (Zend_Log)
 *
 * @subpackage Log
 */

class OfficeBuilder_Log
{
    protected $_nested;
    protected $_enableCalledFromDebug;
    protected $_logger;

    public function __construct($processName = null)
    {
        if ($processName) $this->setLogger($this->buildLogger($processName));

        $this->_nested = 0;
        $this->_enableCalledFromDebug = in_array(APPLICATION_ENV, array('testing', 'development')); // boolean
    }

    private function _getCalledFrom()
    {
        if (!$this->_enableCalledFromDebug)
        {
            return '';
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2); // omits object and args, debug depth = 1
        return basename($backtrace[1]['file']) . ':' . $backtrace[1]['line'] . ' ';
    }

    public function getLogger()
    {
        return $this->_logger;
    }

    public function setLogger(Zend_Log $logger)
    {
        $this->_logger = $logger;
    }

    public function log($message)
    {
        $nesting = str_repeat('-', $this->_nested);
        if ($this->_logger)
        {
            $calledFrom = $this->_getCalledFrom();

            // FOR RELAYING LOGS
            if (is_array($message))
            {
                foreach ($message as $mi)
                {
                    $this->_logger->log($calledFrom . $nesting . ' ' . $mi, Zend_Log::INFO);
                }
            }
            else
            {
                $this->_logger->log($calledFrom . $nesting . ' ' . $message, Zend_Log::INFO);
            }
        }
    }

    public function err($message)
    {
        $calledFrom = $this->_getCalledFrom();

        $nesting = str_repeat('-', $this->_nested);
        if ($this->_logger)
        {
            $this->_logger->log($calledFrom . $nesting . ' ' . $message, Zend_Log::ERR);
        }
    }

    public function nest()
    {
        $this->_nested++;
        return $this;
    }

    public function unnest()
    {
        $this->_nested--;
        return $this;
    }

    public function resetNesting()
    {
        $this->_nested = 0;
        return $this;
    }

    public static function buildLogger($processName)
    {
        $logger = new Zend_Log();
        $fileWriter = new Zend_Log_Writer_Stream(LOG_PATH . '/'.$processName.'.log');
        $logger->addWriter($fileWriter);

        return $logger;
    }

    public function memProfile($line, $className = null)
    {
        $this->log("$className:$line MEM ".memory_get_usage());
    }
}