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
 * @package Content
 * @subpackage Field
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Form_Field extends CMS_Form
{
    private $_section;
    
    private $elements2 = null;
    public function __construct($section) {
        $this->_section = $section;
        parent::__construct();
    }
    
    public function init()
    {  
        $this->setIsArray(false);
        
    //controller will load parent items
        $service = new Core_Model_Reflection_Service();
        $configs = $service->getPluginConfigsByModuleType('Content','Element');
        
        //var_dump($configs);
        //exit;
        
        $options = array('' => 'Select One');
        
        foreach($configs as $key => $value){
            $options[$key] = $value->name;
        }
        
        $this->elements2 = $options;
        
        $type = $this->createElement('JsSelect','element');
        $type->setLabel('Element: ');
//        $type->setMultiOptions(array('Text' => 'Text Field',
//                                     'Textarea' => 'Text Area',
//                                     'Select' => 'Select',
//                                     'MultiCheckboxes' => 'CheckBoxes',
//                                     'Radio' => 'Radio List'));
                $url = $this->getView()->url(array('controller' => 'field', 'action' => 'element-form'), 'content-admin');
       $type->setParam('url',$url);
       $type->setParam('update', '#landing');

//        $options = array(
//            Core_Model_Menu_Item::ROUTE   => 'Internal Route',
//            Core_Model_Menu_Item::DIVIDER => 'Menu Divider',
//            Core_Model_Menu_Item::TEXT => 'Plain Text',
//            Core_Model_Menu_Item::URL => 'External URL'
//        );
      $type->setParam('options', $options);
      $this->addElement($type);
        
        
//        $set = $this->createElement('select','set');
//        
//        $service = new Content_Model_FieldSet_Service();
//        $results = $service->getObjectsBySection($this->_section);
//        
//        $array = array();
//        foreach($results as $set2){
//            $array[$set2->id] = $set2->title;
//        }

//        $set->setMultiOptions($array);
//        $set->setLabel('Field Set: ');
//        $set->setRequired('true');
//        $this->addElement($set);
        
        
        
        $label = $this->createElement('text','label');
        $label->setLabel('Label: ');
        $label->setDescription('This is the label that will show up in the form interface');
        $label->setRequired('true');
        $label->addFilter('StripTags');
        $this->addElement($label);
        
        //use url fiedl type to format nice system name
        $name = $this->createElement('JsSlug','name');
        $name->setLabel('System Name: ');
        $name->setDescription('This is the name that will be used in templates');
        $name->setRequired('true');
        $name->addFilter('StringToLower');
        $name->setParam('source','#label');
        $name->setParam('separator','_');
        $this->addElement($name);
        //@todo check for resevred names
        
//        $value = $this->createElement('text','value');
//        $value->setLabel('Value: ');
//        //$value->setRequired('true');
//        $value->addFilter('StripTags');
//        $this->addElement($value);
        

        
        
        $instructions = $this->createElement('textarea','description');
        $instructions->setLabel('Instructions: ');
        $instructions->addFilter('StripTags');
        $instructions->setAttrib('rows','3');
        $instructions->setAttrib('cols','20');
        $this->addElement($instructions);
        
      //  $required = $this->createElement('checkbox','required');
      //  $required->setLabel('Required: ');
      //   $required->addMultiOption('1','required');
      //  $this->addElement($required);
        
        $required = $this->createElement('checkbox', 'required',array('uncheckedValue' => ""));
        $required->setLabel('Required: ');
        $this->addElement($required);
        
        
//        $order = $this->createElement('text','order');
//        $order->setLabel('Order: ');
//        $order->setRequired('true');
//        $order->addFilter('StripTags');
//        $this->addElement($order);
        
        $service = new Content_Model_FieldSet_Service();
        $sets = $service->getObjectsBySectionWithFields($this->_section);
        
        $order = $this->createElement('select', 'location');
        $order->setLabel('Set/Order: ');
        $order->setRequired('true');
      //  die('<pre>'.print_r($sets, 1));
        $options = array();
        foreach($sets as $set){
            $options[$set->title] = array($set->id.'.0' => 'First');
            foreach($set->fields as $value){
                $options[$set->title][$set->id.'.'.($value->order+1)] = 'After '.$value->label;
            }
        }
        
        $order->setMultiOptions($options);
        $this->addElement($order);


        $this->addDisplayGroup($this->getElements(), 'field', array('legend' => 'Section Field'));
        
        $sub = new CMS_Form_SubFormDiv();
        $sub->setIsArray(false);
        $this->addSubForm($sub, 'landing');
        
      //  $this->attachSubForm($type->getValue());
    }
     public function populate(array $values) {
        if(isset($values['element'])){
            $this->attachSubForm($values['element']);
        }
        
        if(isset($values['type'])){
            if($values['type'] < Content_Model_Field::USER){
                $type = $this->getElement('name');
               $type->setIgnore(true);
              // $type->setAttrib('disabled', true);
                
                $element = $this->getElement('element');
                $element->setIgnore(true);
              //  $element->setAttrib('disabled', true);
                $element->setParam('update', '#none');
            }
        }
        
        parent::populate($values);
    }
    
    public function isValid($values) {
       
        if(empty($values['element'])){
            return false;
        }
        
        if(isset($values['type'])){
            if($values['type'] < Content_Model_Field::USER){
                $type = $this->getElement('name');
                $type->setIgnore(true);
              //  $type->setAttrib('disabled', true);
                
                 
                $element = $this->getElement('element');
             //   $element->setIgnore(true);
              //  $element->setAttrib('disabled', true);
               // $element->setOption('update', '#none');
            }
        }
        
        $this->attachSubForm($values['element']);
        return parent::isValid($values);
    }
    
    public function attachSubForm($id){
        
        $id = ucfirst($id);
        
//$id = $values['type'];
        if($this->getSubForm('options')){
            $this->removeSubForm('options');
        }
        
       
        
        if(!array_key_exists($id, $this->elements2)){
            throw new Exception('Element: '.$id.' Not Found');
        }
        
        $class = 'Content_Plugin_Element_'.$id.'_Element';
        
        $element = new $class();
        $form = $element->getSetupForm();
        $form->setIsArray(true);
        $landing = $this->getSubForm('landing');
        $landing->addSubForm($form, 'options');
            
        
    }


}

