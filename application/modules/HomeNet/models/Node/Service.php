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
 * @subpackage Node
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Node_Service {
    
    /**
     * Get Node Types
     * 
     * @return array 
     */
    public static function getTypes() {
        return array(1 => 'Wireless Sensor Node', 2 => 'Wired Base Station', 3 => 'Internet Node');
    }
    
    
    

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_Node_MapperInterface
     */
    protected $_mapper;
    
    public function __construct($house = null) {
        $this->setHouseId($house);
    }
    
    public function setHouseId($house){
        $this->getMapper()->setHouseId($house);
    }
    
    public function getHouseId(){
       return $this->getMapper()->getHouseId();
    }

    /**
     * @return HomeNet_Model_Node_MapperInterface
     */
    public function getMapper() {

        if ($this->_mapper === null) {
            $this->_mapper = new HomeNet_Model_Node_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_Node_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    protected function _getPlugin($object) {

        if (empty($object->plugin)) {
            throw new InvalidArgumentException('Missing Node Plugin');
        }

        $class = 'HomeNet_Plugin_Node_' . $object->plugin . '_Node';

        if (!class_exists($class, true)) {
            throw new Exception('Node Plugin: ' . $object->plugin . ' Doesn\'t Exist');
        }

        return new $class(array('data' => $object->toArray()));
    }

    protected function _getPlugins($nodes) {
        $objects = array();
        foreach ($nodes as $object) {
            $objects[] = $this->_getPlugin($object);
        }

        return $objects;
    }

    /**
     * @return array
     */
    public function getObjectsByNetwork($network) {
         if (empty($network) || !is_numeric($network)) {
            throw new InvalidArgumentException('Invalid Network');
        }

        return $this->getMapper()->fetchObjectsByNetwork($network);
    }
    
    /**
     * @return array
     */
    public function getObjectByNetworkAddress($network, $address) {
         if (empty($network) || !is_numeric($network)) {
            throw new InvalidArgumentException('Invalid Network');
        }

        return $this->getMapper()->fetchObjectByNetworkAddress($network, $address);
    }

    /**
     * Get Node by id
     * 
     * @param int $id
     * @return HomeNet_Model_Node (HomeNet_Model_Node_Abstract)
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Node');
        }

        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Node: ' . $id . ' Not Found', 404);
        }

//        if ($result->type == HomeNet_Model_Node::INTERNET) {
//            $internet = $this->getInternetMapper()->fetchObjectById($id);
//
//            $result->fromArray($internet->toArray());
//        }

        return $this->_getPlugin($result);
    }

    /**
     * Get Nodes by house id
     * 
     * @return HomeNet_Model_Node[] (HomeNet_Model_Node_Interface[])
     * @throws InvalidArgumentException
     */
    public function getObjects() {

        $results = $this->getMapper()->fetchObjects(HomeNet_Model_Node::STATUS_LIVE);

//        if (empty($result)) {
//            throw new NotFoundException('House: '.$house.' Not Found', 404);
//        }
        return $this->_getPlugins($results);
    }

    /**
     * Get Nodes by house id
     * 
     * @return HomeNet_Model_Node[] (HomeNet_Model_Node_Interface[])
     * @throws InvalidArgumentException
     */
    public function getTrashedObjects() {

        $results = $this->getMapper()->fetchObjects(HomeNet_Model_Node::STATUS_TRASHED);

//        if (empty($result)) {
//            throw new NotFoundException('House: '.$house.' Not Found', 404);
//        }
        return $this->_getPlugins($results);
    }

    /**
     * Get Nodes by room id
     * 
     * @param int $room
     * @return HomeNet_Model_Node[] (HomeNet_Model_Node_Abstract[])
     * @throws InvalidArgumentException
     */
    public function getObjectsByRoom($room) {
        if (empty($room) || !is_numeric($room)) {
            throw new InvalidArgumentException('Invalid Room Id');
        }

        $results = $this->getMapper()->fetchObjectsByRoom($room);

//        if (empty($nodes)) {
//            throw new NotFoundException('Node not found', 404);
//        }
        return $this->_getPlugins($results);
    }

    /**
     * Get Node by house and node address
     * 
     * @param int $address
     * @return HomeNet_Model_Node (HomeNet_Model_Node_Abstract)
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectByAddress($address) {

        if (empty($address) || !is_numeric($address)) {
            throw new InvalidArgumentException('Invalid Node Id');
        }

        $result = $this->getMapper()->fetchObjectByAddress($address);

        if (empty($result)) {
            throw new NotFoundException('Node Address: ' . $address . ' not found', 404);
        }

//        if ($result->type == HomeNet_Model_Node::INTERNET) {
//            $internet = $this->getInternetMapper()->fetchObjectById($id);
//            $result->fromArray($internet->toArray());
//        }

        return $this->_getPlugin($result);
    }

    /**
     * Get the next Node id by house
     * 
     * @todo might be beter to do this with a SQL expression/subquery to prevent any concurrecy issues
     * 
     * @return int Next Id
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getNextAddress() {

        $result = $this->getMapper()->fetchNextAddress();

        if (empty($result)) {
            throw new NotFoundException('House not found', 404);
        }
        return $result;
    }

    /**
     * Get the Id's of Internet nodes by house
     * 
     * @param int $type
     * @return int[]
     * @throws InvalidArgumentException
     */
    public function getObjectsByType($type) {

        if (!is_numeric($type)) {
            throw new InvalidArgumentException('Invalid Type');
        }

        $results = $this->getMapper()->fetchObjectsByType($type);


        return $results;
    }

    /**
     * Get the Id's of Internet nodes by house
     * 
     * @param int $type
     * @return int[]
     * @throws InvalidArgumentException
     */
    public function getUplinks() {

        $results = $this->getObjectsByType(HomeNet_Model_Node::INTERNET);

        $array = array();
        foreach ($results as $value) {
            $array[$value->id] = $value->address;
        }


        return $array;
    }

//    public function geObjectByIdWithModel($id, $columns){
//        $node = $this->getMapper()->fetchNodeByIdWithModel($id, $columns);
//
//        if (empty($node)) {
//            throw new HomeNet_Model_Exception('Node not found', 404);
//        }
//        return $node;
//    }
//     public function getDriverById($id) {
//        $node = $this->getNodeByIdWithModel($id, array('name','driver', 'max_devices'));
//
//        return $this->_getDriver($node);
//    }

    /**
     * Get new Node Based on a NodeModel
     * 
     * @param int $id
     * @return HomeNet_Model_Node_Abstract 
     * @throws InvalidArgumentException
     */
    public function newObjectFromModel($id, $room = null, $address = null) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Model Id');
        }

        $nmService = new HomeNet_Model_NodeModel_Service();
        $object = $nmService->getObjectById($id);

        if (empty($object->plugin)) {
            throw new InvalidArgumentException('Missing Component Driver');
        }

        $class = 'HomeNet_Plugin_Node_' . $object->plugin . '_Node';

        if (!class_exists($class, true)) {
            throw new InvalidArgumentException('Node Plugin: ' . $object->plugin . ' Doesn\'t Exist');
        }

        return new $class(array('model' => $object, 'data'=>array('house'=>$this->getHouseId(),'room'=>$room,'address'=>$address)));
    }
    
    /**
     * Shorcut function for adding a new node
     * 
     * @param int $model Node Model ID
     * @param int $house House ID
     * @param int $room Room ID
     * @param array $networks in array(networkId =>networkAddress) format
     * @param string $description
     * @param array $settings
     * @param int $address if null, the system will use the next available
     */
    public function add($model, $room, $networks, $description = '', $settings = null, $uplink = null, $address = null){

        if (empty($house) || !is_numeric($house)) {
            throw new InvalidArgumentException('Invalid House Id');
        }
        
        if (empty($room) || !is_numeric($room)) {
            throw new InvalidArgumentException('Invalid Room Id');
        }
        
        $object = $this->newObjectFromModel($model);
        $object->house = $this->getHouseId();
        $object->room = $room;
        $object->description = $description;
        $object->address = $address;
        
        if($settings !== null && is_array($settings)){
            $object->settings = $settings;
        }
        
        if($object->address === null){    
            $object->address = $this->getNextAddress($house);
        }
        return $this->create($object);
    }
    

    /**
     * Mark a node as deleted and cascades to all child elements
     * 
     * @param HomeNet_Model_Node_Interface $object 
     */
    public function trash(HomeNet_Model_Node_Interface $object) {
        $object->status = HomeNet_Model_Node::STATUS_TRASHED;
        $result = $this->update($object);

        $devices = $object->getDevices();
        $service = new HomeNet_Model_Device_Service();
        foreach ($devices as $device) {
            $service->trash($device);
        }
        return $result;
    }

    /**
     * Undelete a node and cascades to all child elements
     * 
     * @param HomeNet_Model_Node_Interface $object 
     */
    public function untrash(HomeNet_Model_Node_Interface $object) {
        $object->status = HomeNet_Model_Node::STATUS_LIVE;
        $result = $this->update($object);

        $devices = $object->getDevices();
        $service = new HomeNet_Model_Device_Service();
        foreach ($devices as $device) {
            $service->untrash($device);
        }
        return $result;
    }

    /**
     * Create a new Node
     * 
     * @param HomeNet_Model_Node_Interface|array $mixed
     * @return HomeNet_Model_Node (HomeNet_Model_Node_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {

        if ($mixed instanceof HomeNet_Model_Node_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Node(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Node');
        }

        if ($object->status === null) {
            $object->status = HomeNet_Model_Node::STATUS_LIVE;
        }

        $result = $this->getMapper()->save($object);
        $object->id = $result->id;

        $devices = $object->getDevices();

        if (!empty($devices)) {
            $service = new HomeNet_Model_Device_Service();
            foreach ($devices as $device) {
                $device->status = $object->status;
                $device->house = $object->house;
                $device->setRoomId($object->room);
                $device->node = $object->id;
                $service->create($device);
            }
        }



        //patch to fix null id, $node missing some parameters
//        if ($object->type == HomeNet_Model_Node::INTERNET) {
//
//            $this->getInternetMapper()->save($object);
//        }
//        $houseService = new HomeNet_Model_HousesService();
//        $house = $houseService->getHouseById($node->house);
//        $houseService->clearCacheById($node->house);
//
//        $types = array('house' => 'House',
//            'apartment' => 'Apartment',
//            'condo' => 'Condo',
//            'other' => '',
//            'na' => '');
//
//        $table = new HomeNet_Model_DbTable_Alerts();
//
//        //$table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new node ' . $node->name . ' to their ' . $types[$this->house->type] . ' ' . $this->house->name . ' to HomeNet', null, $id);
//        $table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new node ' . $node->name . ' to ' . $house->name . ' to HomeNet', null, $node->id);


        return $object;
    }

    /**
     * Update an existing Node
     * 
     * @param HomeNet_Model_Node_Interface|array $mixed
     * @return HomeNet_Model_Node (HomeNet_Model_Node_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_Node_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Node(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Node');
        }

        $result = $this->getMapper()->save($object);

        $devices = $object->getDevices();

        if (!empty($devices)) {
            $service = new HomeNet_Model_Device_Service();
            foreach ($devices as $device) {
                $service->update($device);
            }
        }




//        if ($object->type == HomeNet_Model_Node::INTERNET) {
//            $this->getInternetMapper()->save($object);
//        }

        /* @todo add message */
        //@todo determine if we need to return the actual result or will this do
        return $object;
    }

    /**
     * Delete a Node
     * 
     * @param HomeNet_Model_Node_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if ($mixed instanceof HomeNet_Model_Node_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Node(array('data' => $mixed));
        } elseif (is_numeric($mixed)) {
            $object = $this->getObjectbyId((int) $mixed);
        } else {
            throw new InvalidArgumentException('Invalid Node');
        }

        $devices = $object->getDevices();

        $result = $this->getMapper()->delete($object);


        if (!empty($devices)) {

            $deviceService = new HomeNet_Model_Device_Service();
            foreach ($devices as $device) {
                $deviceService->delete($device);
            }
        }

        return $result;
    }

    /**
     * Delete all Nodes. Used for unit testing/Will not work in production 
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