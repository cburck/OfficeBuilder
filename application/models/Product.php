<?php
/**
 * Product model
 *
 * @subpackage Model
 */

class OfficeBuilder_Model_Product extends OfficeBuilder_Model_Abstract
{
    //hardcoded categories
    const PRODUCT_CATEGORY_OFFICE = 'office';
    const PRODUCT_CATEGORY_PINGPONG = 'pingpong';
    const PRODUCT_CATEGORY_WINDOW = 'window';

    //types
    const PRODUCT_TYPE_DAYTIME = 'DateTime';
    const PRODUCT_TYPE_DAY = 'Day';
    const PRODUCT_TYPE_YEAR = 'Year';

    protected $_name;
    protected $_description;
    protected $_category;
    protected $_type;
    protected $_createdOn;

    /**
     * @param int $id
     * @param string $name
     * @param string $description
     * @param string $category
     * @param string $type
     * @param string $createdOn
     */
    public function __construct($id = null, $name = null, $description = null, $category = null, $type = null, $createdOn = null)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_description = $description;
        $this->_category = $category;
        $this->_type = $type;
        $this->_createdOn = $createdOn;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param null|string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return null|string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param null|string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @return null|string
     */
    public function getCategory()
    {
        return $this->_category;
    }

    /**
     * @param null|string $category
     */
    public function setCategory($category)
    {
        $this->_category = $category;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @return null|string
     */
    public function getCreatedOn()
    {
        return $this->_createdOn;
    }

    /**
     * @param null|string $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->_createdOn = $createdOn;
    }

    /**
     * @return array
     */
    public static function getCategories()
    {
        return array(self::PRODUCT_CATEGORY_PINGPONG => 'Ping Pong',
                     self::PRODUCT_CATEGORY_WINDOW  => 'Window Seats',
                     self::PRODUCT_CATEGORY_OFFICE  => 'Offices');
    }
}