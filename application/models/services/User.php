<?php
/**
 * User Service Class
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Service_User extends OfficeBuilder_Model_Service_Abstract
{

    /**
     * Create a new OfficeBuilder_Model_User record
     */
    public function create()
    {
        $mapper = $this->getMapper();
        return $mapper->create();
    }

    /**
     * Save an instance of OfficeBuilder_Model_User
     *
     * @param OfficeBuilder_Model_User $user
     * @param $data New values for the model's properties
     * @return OfficeBuilder_Model_User
     */
    public function save(OfficeBuilder_Model_User $user, $data = null)
    {
        if ($data)
        {
            if (isset($data['first_name']))
            {
                $user->setFirstName($data['first_name']);
            }

            if (isset($data['last_name']))
            {
                $user->setLastName($data['last_name']);
            }

            if (isset($data['is_active']))
            {
                $user->setIsActive($data['is_active']);
            }

            if (isset($data['email']))
            {
                $user->setEmail($data['email']);
            }

            if (isset($data['favorite_color']))
            {
                $user->setFavoriteColor($data['favorite_color']);
            }
        }

        return $this->getMapper()->save($user);
    }

    /**
     * Updates a user's password.
     *
     * @param OfficeBuilder_Model_User $user
     * @param string $password
     * @param bool $hashed
     */
    public function updatePassword(OfficeBuilder_Model_User $user, $password)
    {
        return $this->getMapper()->updatePassword($user, $password);
    }

    /**
     * @return OfficeBuilder_Model_Mapper_User
     */
    public function _createMapper()
    {
        return $this->getMapper();
    }

    /**
     * Creates a mapper used by the service.
     *
     * @return OfficeBuilder_Model_Mapper_User
     */
    public function getMapper()
    {
        return $this->_getMapperFactory()->getUser();
    }
}
