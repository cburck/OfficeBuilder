<?php
/**
 * User model
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_User extends OfficeBuilder_Model_Abstract
{
    const ROLE_USER = 'User';
    const ROLE_ADMIN = 'Admin';

    protected $_firstName;
    protected $_lastName;
    protected $_isActive;
    protected $_email;
    protected $_role;
    protected $_favoriteColor;
    protected $_createdOn;

    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $favoriteColor
     * @param string $createdOn
     */
    public function __construct($id=null, $firstName=null, $lastName=null, $email=null, $favoriteColor=null, $isActive=null, $role=null, $createdOn=null)
    {
        $this->_id = $id;
        $this->_firstName = $firstName;
        $this->_lastName = $lastName;
        $this->_email = $email;
        $this->_role = $role;
        $this->_favoriteColor = $favoriteColor;
        $this->_isActive = $isActive;
        $this->_createdOn = $createdOn;
    }

    /**
     * @return null
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * @param null $role
     */
    public function setRole($role)
    {
        $this->_role = $role;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->_role == self::ROLE_ADMIN;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->_firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->_lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->_lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }

    /**
     * @return mixed
     */
    public function getFavoriteColor()
    {
        return $this->_favoriteColor;
    }

    /**
     * @param mixed $favoriteColor
     */
    public function setFavoriteColor($favoriteColor)
    {
        $this->_favoriteColor = $favoriteColor;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->_createdOn;
    }

    /**
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->_createdOn = $createdOn;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->_isActive;
    }

    /**
     * @param $isActive
     */
    public function setIsActive($isActive)
    {
        $this->_isActive = (bool)$isActive;
    }

    /**
     * Returns array of possible favorite colors
     */
    public static function getColorOptions()
    {
        return array('Invisible', 'Blue', 'Red', 'Green', 'Purple', 'Orange');
    }
}