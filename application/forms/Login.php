<?php
/**
 * Form to Login
 */
class OfficeBuilder_Form_Login extends OfficeBuilder_Form_Abstract
{
    /**
     * (non-PHPdoc)
     * @see library/Zend/Zend_Form#init()
     */
    public function init()
    {
        parent::init();

        $this->setAction('/auth/login');

        $this->setDecorators(array(
            array('ViewScript',
                array('viewScript' => '/auth/forms/login.phtml'))));

        $this->addElement('text', 'email', array(
            'label' => 'User',
            'required' => true,
            'autocorrect' => 'off',
            'autocapitalize' => 'off',
            'placeholder' => 'Username'
        ));

        $this->addElement('password', 'password', array(
            'label' => 'Password',
            'placeholder' => 'Password'
        ));

        $this->addElement('submit', 'submit', array(
            'label' => 'Login'
        ));
    }
}
