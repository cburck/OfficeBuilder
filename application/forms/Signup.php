<?php
/**
 * Form for signing up
 *
 * @subpackage Form
 */

class OfficeBuilder_Form_Signup extends OfficeBuilder_Form_Abstract
{
    /**
     * @param null $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        parent::init();

        $this->setAction('/users/signup/');

        $this->setDecorators(array(
            array('ViewScript',
                array('viewScript' => '/users/forms/signup.phtml'))));

        $this->addElement('text', 'first_name', array(
            'validators' => array(),
            'autocomplete' => 'off',
            "placeholder" => "First Name",
            'label' => 'First Name',
            'required' => true
        ));

        $this->addElement('text', 'last_name', array(
            'validators' => array(),
            'autocomplete' => 'off',
            "placeholder" => "Last Name",
            'label' => 'First Name',
            'required' => true
        ));

        $this->addElement('text', 'email', array(
            'validators' => array(new Zend_Validate_EmailAddress(),
                                  new Zend_Validate_Db_NoRecordExists(array(
                                      'table'   => 'Users',
                                      'field'   => 'email',
                                      'adapter' => Zend_Db_Table::getDefaultAdapter()
                                  ))),
            'autocomplete' => 'off',
            "placeholder" => "Email Address",
            'label' => 'Email',
            'required' => true
        ));

        $this->addElement('select', 'favorite_color', array(
            'autocomplete' => 'off',
            "placeholder" => "Favorite Color",
            'label' => 'Favorite Color',
            'multiOptions' => array_combine(OfficeBuilder_Model_User::getColorOptions(), OfficeBuilder_Model_User::getColorOptions()),
            'required' => true
        ));

        $this->addElement('password', 'password', array(
            'validators' => array(),
            'autocomplete' => 'off',
            "placeholder" => "Password",
            'label' => 'Password'
        ));
    }

    /**
     * @param array $data
     * @return bool
     * @throws Zend_Form_Exception
     */
    public function isValid($data)
    {
        $isValid = parent::isValid($data);

        $passwordElement = $this->getElement('password');
        $password = $passwordElement->getValue();

        if (strlen($password) < 4)
        {
            $isValid = false;
            $passwordElement->addError('Password must be more than 3 characters');
        }

        return $isValid;
    }
}
