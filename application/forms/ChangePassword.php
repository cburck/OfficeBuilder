<?php
/**
 * Form for changing user password
 *
 * @subpackage Form
 */

class OfficeBuilder_Form_ChangePassword extends OfficeBuilder_Form_Abstract
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

        $this->setAction('/index/index/');

        $this->setDecorators(array(
            array('ViewScript',
                array('viewScript' => '/index/forms/password.phtml'))));

        $this->addElement('password', 'password', array(
            'validators' => array(),
            'autocomplete' => 'off',
            //'id' => "password",
            "placeholder" => "New Passwords",
            'label' => 'Change Password'
        ));

        $this->addElement('password', 'password2', array(
            'label' => 'Confirm Password',
            'autocomplete' => 'off',
            //'id' => "password2",
            "placeholder" => "Confirm New Password"
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
        else
        {
            $confirmPasswordElement = $this->getElement('password2');
            $confirmPassword = $confirmPasswordElement->getValue();

            if ($password != $confirmPassword)
            {
                $isValid = false;
                $passwordElement->addError('Passwords do not match.');
                $confirmPasswordElement->addError('Passwords do not match.');
            }
        }

        return $isValid;
    }
}
