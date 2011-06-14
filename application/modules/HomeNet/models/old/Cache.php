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
 * @subpackage User
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Cacheold {

    public $user;

    /**
     * @var Zend_Cache_Core
     */
    private $_houseCache;

    /**
     * @var HomeNet_Model_Cache
     */
    static $instance;

    public $houses = array();

    /**
     * HomeNet_Model_Cache Singleton
     *
     * @return HomeNet_Model_Cache
     */

    static public function getInstance(){
        if(empty(self::$instance)){
            self::$instance =  new HomeNet_Model_Cache();
        }

        return self::$instance;
    }

    public function __construct() {

      $front = Zend_Controller_Front::getInstance();
      
      $manager = $front->getParam('bootstrap')
                       ->getResource('cachemanager');

      $this->_houseCache = $manager->getCache('homenet-houses');
        
    }

    public function getAll() {
//        $h = $this->getHouses();
//        if (empty($h)) {
//            return $h;
//        }
    }

    public function getUserHouseIds() {

        if (!isset($_SESSION['HomeNet']['Houses'])) {

            $user = new Zend_Session_Namespace('User');

            $table = new HomeNet_Model_DbTable_HouseUsers();
            $rows = $table->fetchAllbyUser($user->id);

            $_SESSION['HomeNet']['Houses'] = array();

            foreach ($rows as $row) {
                $_SESSION['HomeNet']['Houses'][] = $row->house;
            }
        }
        return $_SESSION['HomeNet']['Houses'];
    }

    public function getHouse($id) {

        if(isset($this->houses[$id])){
            return $this->houses[$id];
        }

        if (!$this->_houseCache->test($id)) {
            //Not found in Cache
            $table = new HomeNet_Model_DbTable_Houses();
            $row = $table->fetchRowById($id);
            if(empty($row)){
                throw new HomeNet_Model_Exception('Invalid House',404);
            }

            $house = $row->toArray();
            $this->houses[$id] = $house;
            $this->_houseCache->save($house,$id);
        } else {
            $house = $this->_houseCache->load($id);
        }

        return $house;
    }

    public function getHouses($ids) {

        $houses = array();

        if(empty($ids)){
            return $houses;
        }

        //die(debugArray($ids));
        //pull houses from cache
        foreach($ids as $key => $id){
            //check to see if it is already loaded
            if(isset($this->houses[$id])){
                $houses[$id] = $this->houses[$id];
                unset($ids[$key]);
            }
            //check cache nexted
            elseif ($this->_houseCache->test($id)) {
                $houses[$id] = $this->_houseCache->load($id);
                unset($ids[$key]);
            }
        }

        if(!empty($ids)){
            $table = new HomeNet_Model_DbTable_Houses();
            $rows = $table->fetchAllByIds($ids);
            if(empty($rows)){
                throw new HomeNet_Model_Exception('Invalid House',404);
            }

            foreach ($rows as $row) {

                $house = $row->toArray();

                $this->houses[$row->id] = $house;
                $this->_houseCache->save($house,$row->id);
                $houses[$row->id] = $house;
            }
        }

        return $houses;

    }


    public function getHousesRooms($ids) {
        //get existing house cache
        $houses = $this->getHouses($ids);

        foreach ($ids as $key => $id) {
            //check to see if it is already loaded
            if(isset($this->houses[$id]['Rooms'])){
                $houses[$id] = $this->houses[$id];
                unset($ids[$key]);
            }
            //check cache nexted
            elseif (isset($houses[$id]['Rooms'])) {
                //rooms has already be cached
                unset($ids[$key]);
            }
        }

        if(!empty($ids)){

            $service = new HomeNet_Model_RoomsServices();
            $rows = $table->fetchAllByHouses($ids);
            if(empty($rows)){
                throw new HomeNet_Model_Exception('Invalid House',404);
            }

            foreach ($rows as $row) {
                $houses[$row->house]['Rooms'][$row->id] = $row->toArray();
            }

            //update cache
            foreach ($ids as $id) {
                $this->houses[$id] = $houses[$id];
                $this->_houseCache->save($houses[$id], $id);
            }
        }

        return $houses;
    }

    public function getHouseRooms($id) {
        //get existing house cache
        $house = $this->getHouse($id);
        
        if(isset($house['Rooms'])){
            //rooms has already be cached
            return $house;
        }

        //get rooms
        $table = new HomeNet_Model_DbTable_Rooms();
        $rows = $table->fetchAllByHouse($id);
        if(empty($row)){
            throw new HomeNet_Model_Exception('Invalid House',404);
        }
        foreach($rows as $row){
            $house['Rooms'][$row->id] = $row->toArray();
        }

        //update cache
        $this->houses[$id] = $house;
        $this->_houseCache->save($house,$id);

        $this->houses[$id] = $house;



        return $house;
    }

    public function getRoom($room,$id) {
        //this assumes that a house has already been loaded
        /**
         * @todo add search stack for a room specfided with out a house.
         */
        $house = $this->getHouseRooms($id);
        if(empty($house['Rooms'][$room])){
            throw new HomeNet_Model_Exception('Invalid Room',404);
        }
        return $house['Rooms'][$room];
    }

 

    public function getNodesByHouse($house) {
        
    }

    public function getNodesByRoom($house) {

    }

    public function getDevicesByRoomId($house) {

    }

    public function getSubDevicesByRoomId($house) {

    }

    public function clearCacheByHouse($house) {
        unset($this->_houses[$house]);
        $this->_houseCache->remove($house);

    }

}

