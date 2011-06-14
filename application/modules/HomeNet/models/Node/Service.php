<?php

/*
 * NodeService.php
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
 * @subpackage Node
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Node_Service {

    const INTERNET = 3;
    const BASESTATION = 2;
    const SENSOR = 1;


    /**
     * @var HomeNet_Model_Node_MapperInterface
     */
    protected $_mapper;


    /**
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
     * @param int $id
     * @return HomeNet_Model_Node_Abstract
     */
    public function getObjectById($id) {
        $node = $this->getMapper()->fetchObjectById($id);

        if (empty($node)) {
            throw new HomeNet_Model_Exception('Node not found', 404);
        }

        if($node->type == HomeNet_Model_Node_Service::INTERNET){
            $internet = $this->getInternetMapper()->fetchObjectById($id);
            
            $node->fromArray($internet->toArray());
        }

        return $node;
    }

    /**
     * @param int $house
     * @return HomeNet_Model_Node_Abstract[]
     */
    public function getObjectsByHouse($house){
        $nodes = $this->getMapper()->fetchObjectsByHouse($house);

        if (empty($nodes)) {
            throw new HomeNet_Model_Exception('House not found', 404);
        }
        return $nodes;
    }
/**
 * @param int $room
 * @return HomeNet_Model_Node_Abstract[]
 */
    public function getObjectsByRoom($room){
        $nodes = $this->getMapper()->fetchObjectsByRoom($room);

        if (empty($nodes)) {
            throw new HomeNet_Model_Exception('Node not found', 404);
        }
        return $nodes;
    }    
    /**
     * @param int $house
     * @param int $node
     * @return HomeNet_Model_Node_Abstract
     */
    public function getObjectByHouseNode($house, $node){
        $node = $this->getMapper()->fetchObjectByHouseNode($house, $node);
        if (empty($node)) {
            throw new HomeNet_Model_Exception('Node not found', 404);
        }

         if($node->type == HomeNet_Model_Node_Service::INTERNET){
            $internet = $this->getInternetMapper()->fetchObjectById($id);
            $node->fromArray($internet->toArray());
        }

        return $node;
    }

    /**
     * @param int $house
     * @return int Next Id
     */
    public function getNextIdByHouse($house){
        $id = $this->getMapper()->fetchNextIdByHouse($house);

        if (empty($id)) {
            throw new HomeNet_Model_Exception('Node not found', 404);
        }
        return $id;
    }
    
    /**
     * @param int $house
     * @return int[]
     */
    public function getInternetIdsByHouse($house){
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

    public function newObjectByModel($id){

        $nmService = new HomeNet_Model_NodeModel_Service();

        $model = $nmService->getObjectById($id);

        $driver = $model->driver;

        return new $driver(array('model' => $model));
    }







    public function create($node) {

        if ($node instanceof HomeNet_Model_Node_Interface) {
            $h = $node;
        } elseif (is_array($node)) {
            $h = new HomeNet_Model_Node(array('data' => $node));
        } else {
            throw new HomeNet_Model_Exception('Invalid Node');
        }
        unset($node);
        $node = $this->getMapper()->save($h);
        $h->id = $node->id; //patch to fix null id, $node missing some parameters
        if($h->type == HomeNet_Model_Node_Service::INTERNET){
            
            $this->getInternetMapper()->save($h);
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


        return $h;
    }

    public function update($node) {
        if ($node instanceof HomeNet_Model_Node_Interface) {
            $h = $node;
        } elseif (is_array($node)) {
            $h = new HomeNet_Model_Node(array('data' => $node));
        } else {
            throw new HomeNet_Model_Exception('Invalid Node');
        }
        $row = $this->getMapper()->save($h);

        if($h->type == HomeNet_Model_Node_Service::INTERNET){
            $this->getInternetMapper()->save($h);
        }

/* @todo add message */

        return $row;
    }

    public function delete($node) {
        if (is_int($node)) {
            $h = new HomeNet_Model_Node();
            $h->id = $node;
        } elseif ($node instanceof HomeNet_Model_Node_Interface) {
            $h = $node;
        } elseif (is_array($node)) {
            $h = new HomeNet_Model_Node(array('data' => $node));
        } else {
            throw new HomeNet_Model_Exception('Invalid Node');
        }

        $row = $this->getMapper()->delete($h);

         if($h->type == HomeNet_Model_Node_Service::INTERNET){
            $this->getInternetMapper()->delete($h);
        }

       // $houseService = new HomeNet_Model_House_Service();
       // $houseService->clearCacheById($this->house);

        /* @todo add message */

        return $row;
    }

}