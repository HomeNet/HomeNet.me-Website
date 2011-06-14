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
 * @subpackage House
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_House implements HomeNet_Model_House_Interface {

    public $id = null;
    public $status;
    public $url;
    public $name;
    public $description;
    public $location;
    public $gps;
    public $type;
    public $regions;
    public $settings;
    public $permissions;
    public $rooms;

    public function  __construct(array $config = array()) {
        if(isset($config['data'])){
            $this->fromArray($config['data']);
        }
    }

    public function toArray(){
       return get_object_vars($this);
    }

    public function fromArray($array) {
        $vars = get_object_vars($this);

        foreach($array as $key => $value){
            if(array_key_exists($key, $vars)){
                $this->$key = $value;
            }
        }
    }

   /**
     * @param int $id
     * @return HomeNet_Model_RoomInterface
     */
    public function getRoomById($id){

        if(!empty($this->rooms[$id])){
            return $this->rooms[$id];
        }

        $service = new HomeNet_Model_RoomsService();
        $room = $service->getRoomById($id);
        $this->rooms[$room->id] = $room;

        return $room;
    }

    public function getRooms(){

        if(!is_null($this->rooms)){
            return $this->rooms;
        }

        $service = new HomeNet_Model_RoomsService();
        $rooms = $service->getRoomsByHouse($this->id);
        $this->rooms = $rooms;

        return $rooms;
    }

    public function getSetting($setting){
        if(isset($this->settings[$setting])){
            return $this->settings[$setting];
        }
        return null;
    }

    public function setSetting($setting, $value){
        $this->settings[$setting] = $value;
    }

    public function clearSetting($setting){
        unset($this->settings[$setting]);
    }
}