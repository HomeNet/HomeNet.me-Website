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
abstract class Content_Model_Plugin_Element {

    protected $_value = null;
    protected $_options = array();
    public $isArray = false;

    public function __construct($config = array()) {
        if (isset($config['data'])) {

            if (($this->isArray == true) && is_string($config['data'])) {
                
                if(empty($config['data'])){
                    $config['data'] = array();
                } else {
                    $config['data'] = unserialize($config['data']);
                }
            }
            $this->_value = $config['data'];

//            if ($this->isArray) {
//                die(debugArray($this->_value));
//            }
        }
        if (isset($config['options'])) {
            $this->_options = $config['options'];
        }
    }
    
    public function __get($name) {

        
        
        if($this->isArray && is_array($this->_value) && array_key_exists($name, $this->_value)){
            return $this->_value[$name];
        } 
        return null;
    }

    public function __toString() {
        return  $this->render();
    }
    
    public function setValue($value){
        $this->_value = $value;
    }
    
    public function hasValue(){
        return !empty($this->_value);
    }

    /**
     * Get the value of the element
     * 
     * @param array $values 
     */
    public function getValue() {
        return $this->_value;
    }
    
    public function getSaveValue(){
        if(($this->isArray == true) && !empty($this->_value)){
            return serialize($this->getValue());
        }
        return $this->getValue();
   }
   
   public function getFormValue(){
       return $this->getValue();
   }

    /**
     * get any custom options for the setup of the field type
     * 
     * @return CMS_Sub_Form
     */
    function getSetupForm($options = array()) {
        $form = new CMS_Form_SubForm();

        return $form;
    }

    /**
     * Get the form for Inserting data
     * 
     * @param Content_Model_Field $field
     * @return CMS_Form_SubForm 
     */
    abstract function getElement(array $config, $options = array());
    
    public function save(Content_Model_Content $content){
        
    }
    
    public function delete(Content_Model_Content $content){
        
    }
    
    public function render(){
        return (string) $this->_value;
    }
}