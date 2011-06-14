<?php

/*
 * RoomMapperDbTable.php
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
class HomeNet_Model_Node_MapperDbTable implements HomeNet_Model_Node_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return HomeNet_Model_DbTable_Nodes;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new HomeNet_Model_DbTable_Nodes();
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    protected function _getDriver($subdevice){

        if(empty($subdevice->driver)){
            throw new HomeNet_Model_Exception('Missing Subdevice Driver');
        }

        if(!class_exists($subdevice->driver)){
            throw new HomeNet_Model_Exception('Subdevice Driver '.$subdevice->driver.' Doesn\'t Exist');
        }

        return new $subdevice->driver(array('data' => $subdevice->toArray()));
    }

    protected function _getDrivers($subdevices){
        $objects = array();
        foreach($subdevices as $subdevice){
            $objects[] = $this->_getDriver($subdevice);
        }

        return $objects;

    }



    
//    public function fetchNodeById($id){
//        return $this->getTable()->find($id)->current();
//    }
//
    public function fetchObjectById($id) {

        //= array('name','driver', 'max_devices')

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
               ->where('homenet_nodes.id = ?', $id)
               ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model', array('driver', 'name AS modelName', 'type', 'settings'))
                ->limit(1);

        $row =  $this->getTable()->fetchRow($select);
        if(empty($row)){
            return null;
        }

        return $this->_getDriver($row);
    }
    


//    public function fetchNodesByHouse($house){
//
//        $select = $this->getTable()->select()->where('house = ?', $house);
//        return $this->getTable()->fetchAll($select);
//    }
//
//    public function fetchNodesByRoom($room){
//
//        $select = $this->getTable()->select()->where('room = ?', $room);
//        return $this->getTable()->fetchAll($select);
//    }

    public function fetchObjectsByHouse($house){

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
               ->where('house = ?', $house)
               ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model', array('driver', 'name AS modelName', 'type', 'settings'));

        $rows = $this->getTable()->fetchAll($select);

        if(empty($rows)){
            return array();
        }

        return $this->_getDrivers($rows);
    }

    public function fetchObjectByHouseNode($house,$node){

//        $select = $this->getTable()->select()->where('house = ?', $house)
//                                 ->where('node = ?', $node)
//                                 ->order('node DESC')
//                                 ->limit(1);

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
               ->where('house = ?', $house)
               ->where('node = ?', $node)
               ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model', array('driver', 'name AS modelName', 'type', 'settings'))
                ->limit(1);

        $row =  $this->getTable()->fetchRow($select);

        if(empty($row)){
            return null;
        }

        return $this->_getDriver($row);
    }




    public function fetchNextIdByHouse($house){

        $select = $this->getTable()->select()->where('house = ?', $house)
                                  ->where('node NOT IN(?)', array(255,4095))
                                  ->order('node DESC')
                                  ->limit(1);
       $row = $this->getTable()->fetchRow($select);
        $next = $row['node'] + 1;
        return $next;
    }

    public function fetchInternetIdsByHouse($house){

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
               ->where('homenet_nodes.house = ?', $house)
               ->join('homenet_nodes_internet', 'homenet_nodes.id = homenet_nodes_internet.id');
               // ->limit(1);
        
        $rows = $this->getTable()->fetchAll($select);

        if(empty($rows)){
            return array();
        }

        $ids = array();

        foreach($rows as $row){
            $ids[$row->node] = $row->id;
        }

        return $ids;
    }

    

















    public function save(HomeNet_Model_Node_Interface $node) {


        if (($node instanceof HomeNet_Model_DbTableRow_Node) && ($node->isConnected())) {
            $node->save();
            return;
        } elseif (!is_null($node->id)) {
            $row = $this->getTable()->find($node->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($node->toArray());

//        if(empty($row->settings)){
//            $row->settings = '';
//        }

        $row->save();
//die(debugArray($row));
        return $row;
    }

    public function delete(HomeNet_Model_Node_Interface $node) {

        if (($node instanceof HomeNet_Model_DbTableRow_Node) && ($node->isConnected())) {
            $node->delete();
            return true;
        } elseif (!is_null($node->id)) {
            $row = $this->getTable()->find($node->id)->current()->delete();
            return;
        }

        throw new HomeNet_Model_Exception('Invalid Room');
    }
}