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
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * @package HomeNet
 * @subpackage Component
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Component_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_ComponentMapper_Interface
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_Component_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Component_MapperDbTable();
        }
        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_Component_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_Component_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
     protected function _getPlugin($object){

        if(empty($object->plugin)){
            throw new InvalidArgumentException('Missing Component Plugin');
        }
        
        $class = 'HomeNet_Plugin_Component_'.$object->plugin.'_Component';

        if(!class_exists($class,true)){
            throw new Exception('Component Plugin: '.$object->plugin.' Doesn\'t Exist');
        }

        return new $class(array('data' => $object->toArray()));
    }

    protected function _getPlugins($objects){
        $plugins = array();
        foreach($objects as $object){
            $plugins[] = $this->_getPlugin($object);
        }

        return $plugins;
    }

    /**
     * Get Components by id
     * 
     * @param int $id
     * @return HomeNet_Model_Component (HomeNet_Model_Component_Interface)
     * @throw InvalidArgumentException
     * @throw NotFoundException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Component ' . $id . ' Not Found', 404);
        }
        return $this->_getPlugin($result);
    }

    /**
     * Get Components by device id
     * 
     * @param int $house
     * @return HomeNet_Model_Component[] (HomeNet_Model_Component_Interface[])
     * @throw InvalidArgumentException
     */
    public function getObjectsByDevice($device) {
        if (empty($device) || !is_numeric($device)) {
            throw new InvalidArgumentException('Invalid Device Id');
        }
        $results = $this->getMapper()->fetchObjectsByDevice((int) $device);
        
        if (empty($results)) {
            return array();
        }

        $array = array();

        foreach ($results as $component) {
            $array[$component->id] = $component;
        }

        return $this->_getPlugins($array);
    }

    /**
     * Get Components by room id
     * 
     * @param int $house
     * @return HomeNet_Model_Component[] (HomeNet_Model_Component_Interface[])
     * @throw InvalidArgumentException
     */
    public function getObjectsByRoom($room) {
        if (empty($room) || !is_numeric($room)) {
            throw new InvalidArgumentException('Invalid Room Id');
        }
        $results = $this->getMapper()->fetchObjectsByRoom((int) $room);

        if (empty($results)) {
            return array();
        }

        $array = array();

        foreach ($results as $component) {
            $array[$component->id] = $component;
        }

        return $this->_getPlugins($array);
    }

//    public function getModelsByIds($ids){
//        $smService = new HomeNet_Model_ComponentModel_Service();
//        $m = $smService->getObjectsByIds($ids);
//        $models = array();
//        foreach($m as $value){
//            $models[$value->id] = $value;
//        }
//
//        $components = array();
//        foreach($ids as $key => $value){
//            $driver = $models[$value]->driver;
//            $component = new $driver(array('model'=>$models[$value]));
//             $component->order = $key;
//             $components[] = $component
//        }
//
//        return $components;
//
//    }

    /**
     * Create a new Component
     * 
     * @param HomeNet_Model_Component_Interface|array $mixed
     * @return HomeNet_Model_Component (HomeNet_Model_Component_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_Component_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Component(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Component');
        }

        $result = $this->getMapper()->save($object);

//        $houseService = new HomeNet_Model_HousesService();
//        $house = $houseService->getHouseById($component->house);
//        $houseService->clearCacheById($component->house);
//        $types = array('house' => 'House',
//            'apartment' => 'Apartment',
//            'condo' => 'Condo',
//            'other' => '',
//            'na' => '');
        // $table = new HomeNet_Model_DbTable_Alerts();
        //$table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new room ' . $component->name . ' to their ' . $types[$this->house->type] . ' ' . $this->house->name . ' to HomeNet', null, $id);
        // $table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new room ' . $component->name . ' to ' . $house->name . ' to HomeNet', null, $component->id);

        return $result;
    }

    /**
     * Update an existing Component
     * 
     * @param HomeNet_Model_Component_Interface|array $mixed
     * @return HomeNet_Model_Component (HomeNet_Model_Component_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_Component_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Component(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Component');
        }

        $result = $this->getMapper()->save($object);

        //$houseService = new HomeNet_Model_HousesService();
        //$houseService->clearCacheById($this->house);

        return $result;
    }

    /**
     * Delete a Component
     * 
     * @param HomeNet_Model_Component_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if ($mixed instanceof HomeNet_Model_Component_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Component(array('data' => $mixed));
        } elseif (is_numeric($mixed)) {
            $object = $this->getObjectbyId((int) $mixed);
        } else {
            throw new InvalidArgumentException('Invalid Component');
        }

        $result = $this->getMapper()->delete($object);

        //$houseService = new HomeNet_Model_HousesService();
        //$houseService->clearCacheById($this->house);

        return $result;
    }

    /**
     * Delete all Components. Used for unit testing/Will not work in production 
     *
     * @return boolean Success
     * @throws NotAllowedException
     */
    public function deleteAll() {
        if (APPLICATION_ENV == 'production') {
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }

}