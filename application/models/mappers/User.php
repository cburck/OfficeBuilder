<?php
/**
 * User Model Mapper
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Mapper_User extends OfficeBuilder_Model_Mapper_Abstract
{
    /**
     * Get the table used by this mapper.
     *
     * @return OfficeBuilder_Model_DbTable_User
     */
    public function getTable()
    {
        return new OfficeBuilder_Model_DbTable_User();
    }

    /**
     * Create a new instance of OfficeBuilder_Model_User
     *
     * @return OfficeBuilder_Model_User
     */
    public function create()
    {
        $user = new OfficeBuilder_Model_User();
        $user->setRole(OfficeBuilder_Model_User::ROLE_USER);
        $user->setIsActive(true);

        return $user;
    }

    /**
     * Save an instance of OfficeBuilder_Model_User
     *
     * This method handles creating new records as well as saving existing ones.
     * If the model does not have an id, a new record is created.
     *
     * @param OfficeBuilder_Model_User $user Model to be saved
     * @param bool $suppressFetch Tell the method to not reload the data into the returned model
     * @return OfficeBuilder_Model_User
     */
    public function save(OfficeBuilder_Model_User $user, $suppressFetch=true)
    {
        $table = $this->getTable();

        $data = array(
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'email' => $user->getEmail(),
            'is_active' => (int)$user->isActive(),
            'role' => $user->getRole(),
            'fav_color' => $user->getFavoriteColor()
        );

        return $this->_save($table, $user, $data);
    }

    /**
     * Return collection of all users.
     */
    public function getUsers()
    {
        $table = $this->getTable();
        $select = $table->select();

        return $this->_loadSet($table->fetchAll($select));
    }

    /**
     * Updates a user's password.
     *
     * @param OfficeBuilder_Model_User $user
     * @param string $password
     * @param bool $hashed
     */
    public function updatePassword(OfficeBuilder_Model_User $user, $password, $hashed=false)
    {
        $table = $this->getTable();

        return $table->update(
            array('password' => ($hashed) ? ($password) : (sha1($password))),
                  'id = ' . $table->getDefaultAdapter()->quote($user->getId())
        );
    }

    /**
     * Load data from the database into a new model instance.
     *
     * @param $data
     * @param bool $loadRelated
     * @return OfficeBuilder_Model_User
     */
    protected function _load($data, $loadRelated=true)
    {
        if (!is_array($data))
        {
            $data = $data->toArray();
        }

        return new OfficeBuilder_Model_User($data['id'], $data['first_name'], $data['last_name'], $data['email'], $data['fav_color'], $data['is_active'], $data['role'], $data['created_on']);
    }
}
