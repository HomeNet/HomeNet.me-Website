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
 * @package Admin
 * @subpackage Menu_Item
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Admin_Form_MenuItem extends CMS_Form
{

    public function init()
    {
   /**
    * id 	menu 	parent 	order 	type 	route 	title 	visible
    */
        $this->setName('test');
       // $this->setElementsBelongTo('test');
    //controller will load parent items
        $parent = $this->createElement('select','parent');
    $parent->addMultiOption('None','');
//        $type->setMultiOptions(array('house' => 'House',
//                                     'apartment' => 'Apartment',
//                                     'condo' => 'Condo',
//                                     'other' => 'Other',
//                                     'na' => 'N/A'));
      $parent->setLabel('Parent: ');
        $parent->setRequired('true');
        $this->addElement($parent);
        
        
        
        $type = $this->createElement('ajaxSelect', 'type');
        $type->setLabel('Type: ');
        $type->setRequired('true');
        $url = $this->getView()->url(array('controller' => 'menu-item', 'action' => 'type-form'), 'admin');
       $type->setOption('url',$url);
       $type->setOption('update', '#landing');
      // $type->setOptions(array('url'=>'/test/test','update' => '#sub-field'));

        $options = array(
            Core_Model_Menu_Item::ROUTE   => 'Internal Route',
            Core_Model_Menu_Item::DIVIDER => 'Menu Divider',
            Core_Model_Menu_Item::TEXT => 'Plain Text',
            Core_Model_Menu_Item::URL => 'External URL'
        );
      $type->setAttrib('options', $options);
      $type->setValue(Core_Model_Menu_Item::ROUTE);
        $this->addElement($type);
        
        
        
        //This needs to be a convert from title special field
        $order = $this->createElement('text','order');
        $order->setLabel('Order: ');
        $order->setRequired('true');
        $order->addFilter('StripTags');
        $this->addElement($order);
        
        
        
        $active = $this->createElement('checkbox', 'active', array('uncheckedValue' => ""));
        $active->setLabel('Active: ');
        $this->addElement($active);


        $this->addDisplayGroup($this->getElements(), 'category', array('legend' => 'Menu Item'));
        
        $sub = new CMS_Form_SubFormDiv();
        $sub->setIsArray(false);
        $this->addSubForm($sub, 'landing');
        
        
        
        $this->attachSubForm($type->getValue());
        
        

        $this->addElement('hash', 'hash', array('salt' => 'unique'));
    
        
        
    }
    
    public function populate(array $values) {
        if(isset($values['type'])){
            $this->attachSubForm($values['type']);
        }
        parent::populate($values);
    }
    
    public function isValid($data) {
        if(empty($data['type'])){
            return false;
        }
        $this->attachSubForm($data['type']);
        return parent::isValid($data);
    }
    
    public function attachSubForm($id){

        //$id = $values['type'];
        if($this->getSubForm('options')){
            $this->removeSubForm('options');
        }
        
        $types = array(
            Core_Model_Menu_Item::ROUTE   => 'Route',
            Core_Model_Menu_Item::DIVIDER => 'Divider',
            Core_Model_Menu_Item::TEXT => 'Text',
            Core_Model_Menu_Item::URL => 'Url'
        );
        
        if(!array_key_exists($id, $types)){
            throw new Exception('Type: '.$id.' Not Found');
        }
        
        $class = 'Admin_Form_MenuItem'.$types[$id];
        
        $form = new $class();
        $form->setIsArray(true);
        $landing = $this->getSubForm('landing');
        $landing->addSubForm($form, 'options');
            
        
    }


}

