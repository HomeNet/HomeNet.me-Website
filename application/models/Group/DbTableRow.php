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
 * @package Core
 * @subpackage Group
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Group_DbTableRow extends Zend_Db_Table_Row_Abstract implements Core_Model_Group_Interface {

//    public $rooms;
/**
 *
 * @param array $array 
 */
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
        $this->uncompress();
        if(isset($this->visible)){
            $this->visible = (bool)$this->visible;
        }
    }
    
    public function uncompress(){
        if(isset($this->settings) && is_string($this->settings)){
            $this->settings = unserialize($this->settings);
        }

//        if(is_string($this->permissions)){
//            $this->permissions = unserialize($this->permissions);
//        }
    }
    
    public function compress(){
        if(isset($this->settings) && is_array($this->settings)){
            $this->settings = serialize($this->settings);
        }
//
//        if(is_array($this->permissions)){
//            $this->permissions = serialize($this->permissions);
//        }
    }

    public function save(){
      $this->compress();
        if (parent::save()) {
            $this->uncompress();
            return $this;
        }
    }

    public function getSetting($setting){
        if(isset($this->settings[$setting])){
            return $this->settings[$setting];
        }
        return null;
    }

    public function setSetting($setting, $value){
        if($this->settings === null){
            $this->settings = array($setting => $value);
            return;
        }
        //die(debugArray($this->settings));

        $this->settings = array_merge($this->settings,array($setting => $value));
    }

    public function clearSetting($setting){
        unset($this->settings[$setting]);
    }
    
    public function getRoleId(){
        return 'g'.(string) $this->id;
    }

}

