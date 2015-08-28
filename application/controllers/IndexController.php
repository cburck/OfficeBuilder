<?php
/**
 * Index Bootstrap.
 *
 * Forces user to home directory.
 *
 * @subpackage Controller
*/
class IndexController extends OfficeBuilder_Controller
{
    /**
     * Init Zend Controller
     */
    public function init()
    {
        parent::init();
    }

    /**
     * User profile or home page.
     */
    public function indexAction()
    {
        //just output the view!
    }
}