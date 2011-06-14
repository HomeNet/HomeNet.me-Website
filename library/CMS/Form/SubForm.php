<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Form
 *
 * @author mdoll
 */
class CMS_Form_SubForm extends CMS_Form {

     /**
     * Whether or not form elements are members of an array
     * @var bool
     */
    protected $_isArray = true;

    /**
     * Load the default decorators
     *
     * @return Zend_Form_SubForm
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
               //  ->addDecorator('HtmlTag', array('tag' => 'dl'))
                 ->addDecorator('Fieldset');
                // ->addDecorator('DtDdWrapper');
        }
        return $this;
    } 
}