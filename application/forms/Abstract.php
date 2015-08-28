<?php
/**
 * Form abstract
 *
 * @subpackage Form
 */

abstract class OfficeBuilder_Form_Abstract extends Zend_Form
{
    /**
     * @param null $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options = null);
    }

    /**
     * Initialize
     */
    public function init() {

        $this->setMethod('post');

        $this->setDisableLoadDefaultDecorators(true);

        $this->setElementDecorators(array('PrepareElements', 'ViewHelper', 'Errors'));
    }

    /**
     * Set viewscript.
     *
     * @param $path
     * @param null $params
     */
    protected function _setViewScript($path, $params = null)
    {
        $viewScriptOptions = array('viewScript' => $path);

        if (is_array($params))
        {
            $viewScriptOptions = array_merge($viewScriptOptions, $params);
        }

        $this->setDecorators(array(
            'PrepareElements',
            array('ViewScript', $viewScriptOptions),
        ));

        $this->setAttrib('enctype', 'application/x-www-form-urlencoded');
    }

    /**
     * Create the multi-options for a Zend_Form_Element_Multi from an array of objects.
     *
     * @param array $objects
     * @param string $valueProperty
     * @param string $descriptionProperty
     */
    protected function _getMultiOptionsFromObjects($objects, $valueProperty = 'id', $descriptionProperty = 'name')
    {
        $multiOptions = array();

        if (is_array($objects))
        {
            foreach ($objects as $currObject)
            {
                $multiOptions[$currObject->{$valueProperty}] = $currObject->{$descriptionProperty};
            }
        }

        return $multiOptions;
    }
}