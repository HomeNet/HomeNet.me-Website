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
 * @package HomeNet
 * @subpackage Rooms
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_DbTableRow_Room extends Zend_Db_Table_Row_Abstract implements HomeNet_Model_Room_Interface {

    public function fromArray(array $array){

        foreach($array as $key => $value){
            if(array_key_exists($key, $this->_data)){
                $this->$key = $value;
            }

        }
    }

    public function toArray(){

        return parent::toArray();
/*
        $array = array();

        $array['id'] = $this->id;
        $array['house'] = $this->house;
        $array['region'] = $this->region;
        $array['name'] =  $this->name;
        $array['description'] = $this->description;
        $array['permissions'] = $this->permissions;

        return $array;*/
    }

    public function init() {
       // $this->fromArray($this->_data);
    }

    public function save(){
      //  $this->_data = $this->toArray();
        if (parent::save()) {
            return $this->_data['id'];
        }
    }
}