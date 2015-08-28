<?php
/**
 * Abstract Model
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Abstract
{
    protected $_id;

    /**
     * Standard getter for model's ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Standard setter for model's ID
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * Magic getter for protected properties.
     *
     * @param mixed $property
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists(get_class($this),'_'.$property))
        {
            return $this->{'_' . $property};
        }

        return null;
    }
}
