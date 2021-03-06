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
 * @subpackage Network
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Network_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_Network_MapperInterface
     */
    protected $_mapper;
    
    protected $_houseId;
 
    public function __construct($house = null) {
        $this->setHouseId($house);
    }
    
    public function setHouseId($house){
        if($house !== null){
            if (empty($house) || !is_numeric($house)) {
                throw new InvalidArgumentException('Invalid House ID');
            }
            $this->_houseId = $house;
        }
    }
    
    public function getHouseId(){
        if($this->_houseId === null){
            throw new InvalidArgumentException('Invalid House ID');
        }
        
        return $this->_houseId;
    }

    
    /**
     * @return HomeNet_Model_Network_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Network_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_Network_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
    /**
     * @return array
     */
    public function getArray(){
        return array(
            array('id'=>0, 'name'=>'Standalone (Arduino)', 'plugin'=>'Standalone' ),
            array('id'=>1, 'name'=>'RFM12B 915 mhz', 'plugin'=>'Rfm12b'),
            array('id'=>2, 'name'=>'RFM12B 868 mhz', 'plugin'=>'Rfm12b'),
            array('id'=>3, 'name'=>'RFM12B 434 mhz', 'plugin'=>'Rfm12b'),
            array('id'=>4, 'name'=>'Serial 232 *Placeholder*', 'plugin'=>'Todo'),
            array('id'=>5, 'name'=>'Serial 485 *Placeholder*', 'plugin'=>'Todo'),
            array('id'=>6, 'name'=>'Zigbee 2.4 ghz *Placeholder*', 'plugin'=>'ZigBee'),
            array('id'=>7, 'name'=>'Wifi *Placeholder*', 'plugin'=>'Todo'),
            array('id'=>8, 'name'=>'Ethernet *Placeholder*', 'plugin'=>'Todo'),
            );
    }

     protected function _getPlugin($object){

        if(empty($object->plugin)){
            throw new InvalidArgumentException('Missing Network Plugin');
        }
        
        $class = 'HomeNet_Plugin_Network_'.$object->plugin.'_Network';

        if(!class_exists($class,true)){
            throw new Exception('Network Plugin: '.$object->plugin.' Doesn\'t Exist');
        }

        return new $class(array('data' => $object->toArray()));
    }

    protected function _getPlugins($Networks){
        $objects = array();
        foreach($Networks as $object){
            $objects[] = $this->_getPlugin($object);
        }

        return $objects;
    }
    
    /**
     * Get Network objects by House ID
     * 
     * @param int $house House ID
     * @return HomeNet_Model_Network[] (HomeNet_Model_Network_Interface[])
     * @throws InvalidArgumentException
     */
    public function getObjects() {
        $results = $this->getMapper()->fetchObjectsByHouse($this->getHouseId(), HomeNet_Model_Network::STATUS_LIVE);

        return $this->_getPlugins($results);
    }

    /**
     * Get Network objects by Network ID
     * 
     * @param int $id Network ID
     * @return HomeNet_Model_Network (HomeNet_Model_Network_Abstract)
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Network ID');
        }

        $result = $this->getMapper()->fetchObjectByHouseId($this->getHouseId(), $id);
        
        if (empty($result)) {
            throw new NotFoundException('Network: ' . $id . ' Not Found', 404);
        }

        return $this->_getPlugin($result);
    }
  
    /**
     * Get Networks objects by House ID, NetworkType ID
     * 
     * @param int $type Type
     * @return int[]
     * @throws InvalidArgumentException
     */
    public function getObjectsByType($type) {

        if (!is_numeric($type)) {
            throw new InvalidArgumentException('Invalid Type');
        }
        
        $results = $this->getMapper()->fetchObjectsByHouseType($this->getHouseId(), $type);
  
        return $results;
    }
    
    /**
     * Get new Network object based on a NetworkType ID
     * 
     * @param type $id
     * @return driver 
     * @throws InvalidArgumentException
     */
    public function newObjectFromType($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Type');
        }

        $nmService = new HomeNet_Model_NetworkType_Service();
        $object = $nmService->getObjectById($id);

        if(empty($object->plugin)){
            throw new InvalidArgumentException('Missing Network Plugin');
        }
        
        $class = 'HomeNet_Plugin_Network_'.$object->plugin.'_Network';

        if(!class_exists($class, true)){
            throw new InvalidArgumentException('Network Plugin: '.$object->plugin.' Doesn\'t Exist');
        }

        return new $class(array('type' => $object));
    }
    
    /**
     * shortcut function for adding a new network
     *
     * @param int $house house id
     * @param int $type network type id
     * @param string $description
     * @param array $settings
     * @return HomeNet_Model_Network (HomeNet_Model_Network_Interface) 
     */
    public function add($type, $description = '',$settings=array()){
        $object = new HomeNet_Model_Network();
        $object->type = $type;
        $object->house = $this->getHouseId();
        $object->description = $description;
        $object->settings = $settings;
                
        return $this->create($object);
    }

    /**
     * Create a new Network
     * 
     * @param HomeNet_Model_Network_Interface|array $mixed
     * @return HomeNet_Model_Network (HomeNet_Model_Network_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
       
        if ($mixed instanceof HomeNet_Model_Network_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Network(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Network');
        }
        
//        if($object->status === null){
 //           $object->status = HomeNet_Model_Network::STATUS_LIVE;
//        }
        
        $result = $this->getMapper()->save($object);
        
        $object->id = $result->id; //patch to fix null id, $Network missing some parameters
//        if ($object->type == HomeNet_Model_Network::INTERNET) {
//
//            $this->getInternetMapper()->save($object);
//        }

//        $houseService = new HomeNet_Model_HousesService();
//        $house = $houseService->getHouseById($Network->house);
//        $houseService->clearCacheById($Network->house);
//
//        $types = array('house' => 'House',
//            'apartment' => 'Apartment',
//            'condo' => 'Condo',
//            'other' => '',
//            'na' => '');
//
//        $table = new HomeNet_Model_DbTable_Alerts();
//
//        //$table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new Network ' . $Network->name . ' to their ' . $types[$this->house->type] . ' ' . $this->house->name . ' to HomeNet', null, $id);
//        $table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new Network ' . $Network->name . ' to ' . $house->name . ' to HomeNet', null, $Network->id);


        return $object;
    }

    /**
     * Update an existing Network
     * 
     * @param HomeNet_Model_Network_Interface|array $mixed
     * @return HomeNet_Model_Network (HomeNet_Model_Network_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_Network_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Network(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Network');
        }

        $result = $this->getMapper()->save($object);

//        if ($object->type == HomeNet_Model_Network::INTERNET) {
//            $this->getInternetMapper()->save($object);
//        }

        /* @todo add message */
        //@todo determine if we need to return the actual result or will this do
        return $object;
    }

    /**
     * Delete a Network
     * 
     * @param HomeNet_Model_Network_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if ($mixed instanceof HomeNet_Model_Network_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Network(array('data' => $mixed));
        } elseif (is_numeric($mixed)) {
            $object = $this->getObjectbyId((int) $mixed);
        } else {
            throw new InvalidArgumentException('Invalid Network');
        }

        return $this->getMapper()->delete($object);
    }

    /**
     * Delete all Networks. Used for unit testing/Will not work in production 
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