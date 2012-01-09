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
     * @return Zend_Db_Table;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('homenet_nodes');
            $this->_table->setRowClass('HomeNet_Model_Node_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

//    public function fetchObjectById($id) {
//        return $this->getTable()->find($id)->current();
//    }

    public function fetchObjectById($id) {

        //= array('name','driver', 'max_devices')

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('homenet_nodes.id = ?', $id)
                ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model', array('plugin', 'name AS model_name', 'type', 'max_devices', 'settings AS model_settings'))
                ->limit(1);

        return $this->getTable()->fetchRow($select);
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

    public function fetchObjectsByHouse($house, $status = HomeNet_Model_Node::STATUS_LIVE) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('house = ?', $house)
                ->where('homenet_nodes.status = ?', $status)
                ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model', array('plugin', 'name AS model_name', 'type', 'max_devices', 'settings AS model_settings'));

        return $this->getTable()->fetchAll($select);
    }

    public function fetchObjectsByRoom($room, $status = HomeNet_Model_Node::STATUS_LIVE) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('room = ?', $room)
                ->where('homenet_nodes.status = ?', $status)
                ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model', array('plugin', 'name AS model_name', 'type','max_devices', 'settings AS model_settings'));

        return $this->getTable()->fetchAll($select);
    }

    public function fetchObjectByHouseAddress($house, $address) {

//        $select = $this->getTable()->select()->where('house = ?', $house)
//                                 ->where('node = ?', $node)
//                                 ->order('node DESC')
//                                 ->limit(1);

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('house = ?', $house)
                ->where('address = ?', $address)
                ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model', array('plugin', 'name AS model_name', 'type','max_devices', 'settings AS model_settings'))
                ->limit(1);

        return $this->getTable()->fetchRow($select);
    }

    public function fetchNextAddressByHouse($house) {

        $select = $this->getTable()->select()->where('house = ?', $house)
                ->where('address NOT IN(?)', array(255, 4095))
                ->order('address DESC')
                ->limit(1);
        $row = $this->getTable()->fetchRow($select);
        if(empty($row)){
            return null;
        }
        
        $next = $row['address'] + 1;
        if($next == 255){
            $next++;
        }
        return $next;
    }

    public function fetchObjectsByHouseType($house, $type, $status = HomeNet_Model_Node::STATUS_LIVE) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('house = ?', $house)
                ->where('homenet_nodes.status = ?', $status)
                ->join('homenet_node_models', 'homenet_node_models.id = homenet_nodes.model', array('plugin', 'name AS model_name', 'type', 'settings AS model_settings'))
                ->where('homenet_node_models.type = ?', $type);
        

        return $this->getTable()->fetchAll($select);
    }

    public function save(HomeNet_Model_Node_Interface $object) {

        $settings = $object->settings;

        if (is_array($object->model_settings)) {
            $object->settings = array_diff_assoc($settings, $object->model_settings); // remove model settings
        }

        if (($object instanceof HomeNet_Model_Node_DbTableRow) && ($object->isConnected())) {
            return $object->save();
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($object->toArray());

        $result = $row->save();
        $result->settings = $settings;

        return $result;
    }

    public function delete(HomeNet_Model_Node_Interface $object) {

        if (($object instanceof HomeNet_Model_Node_DbTableRow) && ($object->isConnected())) {
            return $object->delete();
        } elseif ($object->id !== null) {
            return $this->getTable()->find($object->id)->current()->delete();
        }

        throw new HomeNet_Model_Exception('Invalid Room');
    }

    public function deleteAll() {
        if (APPLICATION_ENV != 'production') {
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `' . $this->getTable()->info('name') . '`');
        }
    }

}