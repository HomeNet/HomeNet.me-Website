<?php

/*
 * Interface.php
 * 
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
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Content_Plugin_Element_Categories_Element  extends Content_Model_Plugin_Element  {

    
    public $isArray = true;
    
    /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
    function getSetupForm($options = array()){
        $form = parent::getSetupForm($options);
        $form->setLegend('Category Options');
        
        $set = $form->createElement('select', 'set');
        $set->setLabel('Set: ');
        $set->setRequired('true');
        
        $service = new Content_Model_CategorySet_Service();
        $results = $service->getObjects();

        $options = array();
        foreach($results as $value){
            $options[$value->id] = $value->title;
        }

        //$template->addMultiOption('None','');
        $set->setMultiOptions($options);
        $form->addElement($set);
        
        return $form;
    }
    
    /**
     * Get the form element to display
     * 
     * @param $config config of how object shoiuld be rendered
     * @return Zend
     */
    function getElement(array $config, $options = array()){
        
       // die(var_dump($this->_value));
        
        if(empty($options['set'])){
            throw new InvalidArgumentException('Missing Set Id');
        }
        $service = new Content_Model_Category_Service();
        $objects = $service->getObjectsBySet($options['set']);
        $options = array();
        foreach($objects as $value){
            $options[$value->id] = $value->title;
        }
        
        $element = new Zend_Form_Element_MultiCheckbox($config); 
        $element->setMultiOptions($options);
      //  echo debugArray($this->_value);
     //   die(debugArray($config));
      $element->setValue($this->_value);
        return $element;
    }
    
    /**
     * Parse the Subform and return the value to be stored in the database
     * 
     * @param array $values 
     */
    function getValue($values = array()){
        
    }
}