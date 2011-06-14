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
class CMS_Form extends Zend_Form {

    protected $_defaultDisplayGroupClass = 'CMS_Form_DisplayGroup';

    public function  __construct($options = null) {
        $this->addPrefixPath('ZendX_JQuery_Form_Decorator', 'ZendX/JQuery/Form/Decorator', 'decorator')
             ->addPrefixPath('ZendX_JQuery_Form_Element', 'ZendX/JQuery/Form/Element', 'element')
             ->addElementPrefixPath('ZendX_JQuery_Form_Decorator', 'ZendX/JQuery/Form/Decorator', 'decorator')
             ->addDisplayGroupPrefixPath('ZendX_JQuery_Form_Decorator', 'ZendX/JQuery/Form/Decorator')

             ->addPrefixPath('CMS_Form_Decorator', 'CMS/Form/Decorator', 'decorator')
             ->addPrefixPath('CMS_Form_Element', 'CMS/Form/Element', 'element');


        parent::__construct($options);
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

        $type = strtolower($type);

        $skip = array('submit', 'hash', 'hidden');

        $ui = array('colorPicker','datePicker','autoComplete');

        $other = array('multicheckbox', 'select',  'radios', 'pickColor');//

        if (!in_array($type, $skip)) { //in_array($type, $other)

            $element->addDecorator(array('labelEnd' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true));

            if(in_array($type, $ui)){
                $this->addDecorator('UiWidgetElement');
            } else {
                $element->addDecorator('ViewHelper', array());
            }
            
            $element->addDecorator('Errors');
            // ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
            $element->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-input'));

            $element->addDecorator('Label', array());
            $element->addDecorator(array('labelStart' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-element', 'openOnly' => true));
        }
//        elseif (!in_array($type, $skip)) {
//
//            $element->setDecorators(array(array('ViewScript', array(
//                        'viewScript' => '_element.phtml',
//                        'class' => 'form-element'
//                ))));
//        }
        else {
            $element->setDecorators(array('ViewHelper'));
        }



        //array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'checkbox'))


        return $element;
    }

    public function setView(Zend_View_Interface $view = null)
    {
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

    /*
      public function addDisplayGroup(array $elements, $name, $options = null)
      {

      $settings  = array('disableLoadDefaultDecorators' => true);

      if(is_array($options)){
      $settings = array_merge($settings, $options);

      }

      $group = parent::addDisplayGroup($elements, $name, $settings);

      $group->addDecorator('FormElements');
      $group->addDecorator('Fieldset');

      return $group;
      }
     */
}