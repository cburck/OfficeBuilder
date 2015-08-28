<?php
/**
 * Error Controller.
 *
 * Shows cached error messages to user.
 *
 * @subpackage Controller
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * Init method for error controller
     */
    public function init()
    {
        // SWITCH TO A BASIC LAYOUT
        $this->_helper->layout()->setLayout('error');

        //context switch
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('error', array('xml', 'json'))->initContext();
    }

    /**
     * Redirect to error/
     */
    public function indexAction()
    {
        $this->redirect('/error/error', true);
    }

    /**
     * Default method called by dispatcher in front controller.
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type)
        {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                //404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                $this->view->response_code = 404;
                break;

            default:
                //application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                $this->view->response_code = 500;

                if ($errors->exception)
                {
                    include_once('OfficeBuilder/Exception.php');
                    OfficeBuilder_Exception::catchException($errors->exception);
                }

                break;
        }

        //conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true)
        {
            $this->view->exceptionMessage = sprintf("Exception '%s' with message '(%s) %s' in %s:%d",
                get_class($errors->exception),
                $errors->exception->getCode(),
                $errors->exception->getMessage(),
                $errors->exception->getFile(),
                $errors->exception->getLine());

            $this->view->exceptionTrace = $errors->exception->getTraceAsString();
            $this->view->exception = $errors->exception;
            $this->view->request = $errors->request;
        }
    }

    /**
     * Get logger (to log errors)
     */
    protected function _getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');

        if (!$bootstrap->hasResource('Log'))
        {
            return false;
        }

        $log = $bootstrap->getResource('Log');

        return $log;
    }
}