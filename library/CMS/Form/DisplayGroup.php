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
class CMS_Form_DisplayGroup extends Zend_Form_DisplayGroup {


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
            $this->addDecorator('Description', array('escape'=>false,'tag'=>'span'))
            ->addDecorator('FormElements')
               //  ->addDecorator('HtmlTag', array('tag' => 'dl'))
                 ->addDecorator('Fieldset');
            
                // ->addDecorator('DtDdWrapper');
        }
        return $this;
    } 
}