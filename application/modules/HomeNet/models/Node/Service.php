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
     * Storage mapper
     * 
     * @var HomeNet_Model_Node_MapperInterface
     */
    protected $_mapper;

    /**
     * Storage mapper for Internet Nodes
     * 
     * @var HomeNet_Model_Node_Internet_MapperInterface
     */
    protected $_internetMapper;

    /**
     * @return HomeNet_Model_Node_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Node_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_Node_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @return HomeNet_Model_Node_MapperInterface
     */
    public function getInternetMapper() {

        if (empty($this->_internetMapper)) {
            $this->_internetMapper = new HomeNet_Model_Node_Internet_MapperDbTable();
        }

        return $this->_internetMapper;
    }

    public function setInternetMapper(HomeNet_Model_Node_Internet_MapperInterface $mapper) {
        $this->_internetMapper = $mapper;
    }

    /**
     * Get Node by id
     * 
     * @param int $id
     * @return HomeNet_Model_Node[] (HomeNet_Model_Node_Abstract[])
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        if (empty($house)) {
            throw new InvalidArgumentException('Invalid Node');
        }

        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Node: ' . $id . ' Not Found', 404);
        }

        if ($result->type == HomeNet_Model_Node::INTERNET) {
            $internet = $this->getInternetMapper()->fetchObjectById($id);

            $result->fromArray($internet->toArray());
        }

        return $result;
    }

    /**
     * Get Node by house id
     * 
     * @param int $house
     * @return HomeNet_Model_Node[] (HomeNet_Model_Node_Interface[])
     * @throws InvalidArgumentException
     */
    public function getObjectsByHouse($house) {
        if (empty($house)) {
            throw new InvalidArgumentException('Invalid House Id');
        }

        $results = $this->getMapper()->fetchObjectsByHouse($house);

//        if (empty($result)) {
//            throw new NotFoundException('House: '.$house.' Not Found', 404);
//        }
        return $results;
    }

    /**
     * Get Nodes by room id
     * 
     * @param int $room
     * @return HomeNet_Model_Node[] (HomeNet_Model_Node_Abstract[])
     * @throws InvalidArgumentException
     */
    public function getObjectsByRoom($room) {
        if (empty($room)) {
            throw new InvalidArgumentException('Invalid Room Id');
        }

        $nodes = $this->getMapper()->fetchObjectsByRoom($room);

//        if (empty($nodes)) {
//            throw new NotFoundException('Node not found', 404);
//        }
        return $nodes;
    }

    /**
     * Get Node by house and node id
     * 
     * @param int $house
     * @param int $node
     * @return HomeNet_Model_Node (HomeNet_Model_Node_Abstract)
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectByHouseNode($house, $node) {
        if (empty($house)) {
            throw new InvalidArgumentException('Invalid House Id');
        }
        if (empty($node)) {
            throw new InvalidArgumentException('Invalid Node Id');
        }

        $result = $this->getMapper()->fetchObjectByHouseNode($house, $node);

        if (empty($result)) {
            throw new NotFoundException('Node: ' . $node . ' not found', 404);
        }

        if ($result->type == HomeNet_Model_Node::INTERNET) {
            $internet = $this->getInternetMapper()->fetchObjectById($id);
            $result->fromArray($internet->toArray());
        }

        return $result;
    }

    /**
     * Get the next Node id by house
     * 
     * @todo might be beter to do this with a SQL expression/subquery to prevent any concurrecy issues
     * 
     * @param int $house house id
     * @return int Next Id
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getNextIdByHouse($house) {
        if (empty($house)) {
            throw new InvalidArgumentException('Invalid House Id');
        }
        $id = $this->getMapper()->fetchNextIdByHouse($house);

        if (empty($id)) {
            throw new NotFoundException('House not found', 404);
        }
        return $id;
    }

    /**
     * Get the Id's of Internet nodes by house
     * 
     * @param int $house
     * @return int[]
     * @throws InvalidArgumentException
     */
    public function getInternetIdsByHouse($house) {
        if (empty($house)) {
            throw new InvalidArgumentException('Invalid House Id');
        }
        $nodes = $this->getMapper()->fetchInternetIdsByHouse($house);

        if (empty($nodes)) {
            //throw new HomeNet_Model_Exception('Node not found', 404);
        }
        return $nodes;
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
     * Get new Node by model
     * 
     * @param type $id
     * @return driver 
     * @throws InvalidArgumentException
     */
    public function newObjectByModel($id) {
        if (empty($house)) {
            throw new InvalidArgumentException('Invalid House Id');
        }

        $nmService = new HomeNet_Model_NodeModel_Service();
        $model = $nmService->getObjectById($id);

        $driver = $model->driver;

        return new $driver(array('model' => $model));
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

        $node = $this->getMapper()->save($object);
        $object->id = $node->id; //patch to fix null id, $node missing some parameters
        if ($object->type == HomeNet_Model_Node::INTERNET) {

            $this->getInternetMapper()->save($object);
        }

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

        if ($object->type == HomeNet_Model_Node::INTERNET) {
            $this->getInternetMapper()->save($object);
        }

        /* @todo add message */

        return $result;
    }

    /**
     * Delete a Node
     * 
     * @param HomeNet_Model_Node_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = $this->getObjectbyId($mixed);
        } elseif ($mixed instanceof HomeNet_Model_Node_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Node(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Node');
        }

        $result = $this->getMapper()->delete($object);

        if ($object->type == HomeNet_Model_Node::INTERNET) {
            $this->getInternetMapper()->delete($object);
        }

        // $houseService = new HomeNet_Model_House_Service();
        // $houseService->clearCacheById($this->house);

        /* @todo add message */

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