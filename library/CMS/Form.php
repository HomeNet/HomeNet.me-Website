<?php

/*
 * Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 *
 * This file is part of HomeNet.
 *
 * HomeNet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * HomeNet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package CMS
 * @subpackage Form
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class CMS_Form extends Zend_Form {

    protected $_defaultDisplayGroupClass = 'CMS_Form_DisplayGroup';

    public function __construct($options = null) {
        $this->addPrefixPath('ZendX_JQuery_Form_Decorator', 'ZendX/JQuery/Form/Decorator', 'decorator')
                ->addPrefixPath('ZendX_JQuery_Form_Element', 'ZendX/JQuery/Form/Element', 'element')
                ->addElementPrefixPath('ZendX_JQuery_Form_Decorator', 'ZendX/JQuery/Form/Decorator', 'decorator')
                ->addDisplayGroupPrefixPath('ZendX_JQuery_Form_Decorator', 'ZendX/JQuery/Form/Decorator')
                ->addPrefixPath('CMS_Form_Decorator', 'CMS/Form/Decorator', 'decorator')
                ->addPrefixPath('CMS_Form_Element', 'CMS/Form/Element', 'element');


        parent::__construct($options);
        
        $this->getElement('hash');
        if(APPLICATION_ENV != 'testing'){
            //if(empty($this->getElement('hash'))){
                //$this->addElement('hash', 'hash', array('salt' => 'unique'));
            //}
        }
    }

    public function loadDefaultDecorators() {

        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $this->setDecorators(array(
            'FormElements',
            //'Fieldset',
            'Form'
        ));
    }

    public function createElement($type, $name, $options = null) {

        $settings = array('disableLoadDefaultDecorators' => false);

        if (is_array($options)) {
            $settings = array_merge($settings, $options);
        }

        $element = parent::createElement($type, $name, $settings);

        $element = $this->applyDefaultDecorators($element);


        //array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'checkbox'))


        return $element;
    }
    
    public function applyDefaultDecorators(Zend_Form_Element $element){
        
        $class = get_class($element);
        
        $type = strtolower(trim(strrchr($class, "_"),'_'));
        

        $skip = array('submit', 'hash', 'hidden');

        $ui = array('colorPicker', 'datePicker', 'autoComplete');

        $other = array('multicheckbox', 'select', 'radios', 'pickColor');
        
        // $wide = array('textarea','ajaxgallery');
        //
        //die(debugArray($element->getDecorators()));

        if (!in_array($type, $skip)) { //in_array($type, $other)
            $element->addDecorator(array('labelEnd' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true));

            if (in_array($type, $ui)) {
                $this->addDecorator('UiWidgetElement');
          //  } elseif (in_array($type, $other)) {
           //     $this->addDecorator('HtmlTag', array('tag' => 'div'));
            }elseif (strstr($type, 'js')) {
                $element->addDecorator('JsHelper');
            } else {
                $element->addDecorator('ViewHelper', array());
            }
            
            $suffix = '';
            if(isset($element->wide)  && ($element->wide == true)){
               $suffix .='-wide';
            }

            $element->addDecorator('Errors');
            // ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
            $element->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-input'.$suffix));

            $element->addDecorator('Label', array());
            
            $element->addDecorator(array('labelStart' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-element'.$suffix, 'openOnly' => true));
        }

        else {
            $element->setDecorators(array('ViewHelper'));
        }

        return $element;
    }

    public function setView(Zend_View_Interface $view = null) {
        if (null !== $view) {
            if (false === $view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper')) {
                $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
            }
            if (false === $view->getPluginLoader('helper')->getPaths('CMS_View_Helper')) {
                $view->addHelperPath('CMS/View/Helper', 'CMS_View_Helper');
            }
        }
        return parent::setView($view);
    }

    public function getValues($suppressArrayNotation = false) {
        // return parent::getValues($suppressArrayNotation);
        //$values = parent::getValues($suppressArrayNotation);
        $values = array();

//        if ($this->isArray()) {
//            $eBelongTo = $this->getElementsBelongTo();
//        }

        foreach ($this->getElements() as $key => $element) {
            if (!$element->getIgnore()) {
                $values[$key] = $element->getValue();
            }
        }

        foreach ($this->getSubForms() as $key => $subForm) {

            if ($subForm->isArray() && ($suppressArrayNotation == false)) {
                $values[$key] = $subForm->getValues($suppressArrayNotation);
            } else {
                $values = array_merge($values, $subForm->getValues($suppressArrayNotation));
            }
        }

        return $values;
    }

}