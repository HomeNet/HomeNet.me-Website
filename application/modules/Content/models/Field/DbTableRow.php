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
class Content_Model_Field_DbTableRow extends Zend_Db_Table_Row_Abstract implements Content_Model_Field_Interface {

//    public $rooms;

    public function fromArray(array $array){

        foreach($array as $key => $value){
            if(array_key_exists($key, $this->_data)){
                $this->$key = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray(){
        return parent::toArray();
    }

    public function init(){
        
        $this->locked = (bool)$this->locked;
       // $this->edit_name =  (bool)$this->edit_name;
        $this->required = (bool)$this->required;
        $this->visible = (bool)$this->visible;

        $this->uncompress();
    }
    
    public function uncompress(){
        if(is_string($this->filters)){
            $this->filters = unserialize($this->filters);
        }

        if(is_string($this->validators)){
            $this->validators = unserialize($this->validators);
        }
        if(is_string($this->attributes)){
            $this->attributes = unserialize($this->attributes);
        }
        
         if(is_string($this->options)){
            $this->options = unserialize($this->options);
        }
    }
    
    public function compress(){
        if(is_array($this->filters)){
            $this->filters = serialize($this->filters);
        }

        if(is_array($this->validators)){
            $this->validators = serialize($this->validators);
        }
        
        if(is_array($this->attributes)){
            $this->attributes = serialize($this->attributes);
        }
         if(is_array($this->options)){
            $this->options = serialize($this->options);
        }
        
        if(empty($this->value)){
            $this->value = '';
        }
    }

    public function save(){
      $this->compress();
        if (parent::save()) {
            $this->uncompress();
            return $this;
        }
    }

//    public function getSetting($setting){
//        if(isset($this->settings[$setting])){
//            return $this->settings[$setting];
//        }
//        return null;
//    }
//
//    public function setSetting($setting, $value){
//        if($this->settings === null){
//            $this->settings = array($setting => $value);
//            return;
//        }
//        //die(debugArray($this->settings));
//
//        $this->settings = array_merge($this->settings,array($setting => $value));
//    }
//
//    public function clearSetting($setting){
//        unset($this->settings[$setting]);
//    }

}

