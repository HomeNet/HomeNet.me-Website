<?php

/*
 * HouseMapperCache.php
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
 * @package HomeNet
 * @subpackage House
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_House_MapperCache {

    static $_houses;

    protected $_houseCache;

    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_mapper;

    public function __construct(HomeNet_Model_House_MapperInterface $mapper) {
        $this->_mapper = $mapper;

        if (empty($this->_houseCache)) {

            $front = Zend_Controller_Front::getInstance();

            $manager = $front->getParam('bootstrap')
                            ->getResource('cachemanager');

            $this->_houseCache = $manager->getCache('homenet-houses');

        }
    }

    public function fetchHouseById($id) {
        if(isset(self::$_houses[$id])){
            return self::$_houses[$id];
        }

        if (!$this->_houseCache->test($id)) {
            //Not found in Cache
            $row = $this->_mapper->fetchHouseById($id);
            if(empty($row)){
                return array();
            }

            $house = $row;//->toArray();
            self::$_houses[$id] = $house;
            $this->_houseCache->save($house,$id);
        } else {
            $house = $this->_houseCache->load($id);
        }

        return $house;
    }

    public function fetchHousesByIds(array $ids) {
        $houses = array();

        //die(debugArray($ids));
        //pull houses from cache
        foreach($ids as $key => $id){
            //check to see if it is already loaded
            if(isset(self::$_houses[$id])){
                $houses[$id] = self::$_houses[$id];
                unset($ids[$key]);
            }
            //check cache nexted
            elseif ($this->_houseCache->test($id)) {
                $houses[$id] = $this->_houseCache->load($id);
                unset($ids[$key]);
            }
        }

        if(!empty($ids)){

            $rows = $this->_mapper->fetchHousesByIds($ids);
            if(empty($rows)){
                return $houses;
            }

            foreach ($rows as $row) {

                $house = $row;

                self::$_houses[$row->id] = $house;
                $this->_houseCache->save($house,$row->id);
                $houses[$row->id] = $house;
            }
        }

        return $houses;
    }

    public function fetchHouseByIdWithRooms($id) {
         //get existing house cache
        $house = $this->fetchHouseById($id);

        if(isset($house->rooms)){
            //rooms has already be cached
            return $house;
        }

        //get rooms
        $rows = $this->_mapper->fetchHouseByIdWithRooms($id);
        
        foreach($rows as $row){
            $house->rooms[$row->id] = $row;
        }

        //update cache
        self::$_houses[$id] = $house;
        $this->_houseCache->save($house,$id);

        return $house;
    }

    public function fetchHousesByIdsWithRooms($ids) {      

        //get existing house cache
        $houses = $this->fetchHousesByIds($ids);

        foreach ($ids as $key => $id) {
            //check to see if it is already loaded
            if(!empty(self::$_houses[$id]->rooms)){
                $houses[$id] = self::$_houses[$id];
                unset($ids[$key]);
            }
            //check cache nexted
            elseif (isset($houses[$id]->rooms)) {
                //rooms has already be cached
                unset($ids[$key]);
            }
        }

        if(!empty($ids)){

            $rows = $this->_mapper->fetchHousesByIdsWithRooms($ids);
            if(empty($rows)){
                return $houses;
            }

            foreach ($rows as $row) {
                $houses[$row->id] = $row;
                self::$_houses[$id] = $houses[$row->id];
                $this->_houseCache->save($houses[$row->id], $row->id);
            }
        }

        return $houses;
    }

    public function save(HomeNet_Model_House_Interface $house) {
        $row = $this->_mapper->save($house);
        if(!is_null($house->id)){
            $this->clearCacheById($house->id);
        }

        return $row;
    }

    public function delete(HomeNet_Model_House_Interface $house) {
        $this->_mapper->delete($house);
        $this->clearCacheById($house->id);
    }

    public function clearCacheById($id) {
        unset(self::$_houses[$id]);
        $this->_houseCache->remove($id);
    }

}