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
 * @subpackage Device
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_DbTable_Devices extends Zend_Db_Table_Abstract {

    protected $_name = 'homenet_devices';
    protected $_rowClass = 'HomeNet_Model_DbTableRow_Device';

//    public function fetchRowById($id) {
//        return $this->find($id)->current();
//    }
//
//    public function fetchDriverById($id) {
//
//        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//                ->where('id = ?', $id)
//                ->join('homenet_device_models', 'homenet_device_models.id = homenet_devices.model', array('driver'))
//                ->limit(1);
//
//        $row = $this->fetchRow($select);
//        if (!class_exists($row->driver)) {
//            throw new Zend_Exception('Device driver ' . $row->driver . ' not found');
//        }
//
//        return new $row->driver($row);
//    }
//
//    public function fetchRowByNodePosition($node, $position) {
//        $select = $this->select()->where('node = ?', $node)
//                        ->where('position = ?', $position);
//        $rows = $this->fetchAll($select);
//        if ($rows->count() > 1) {
//            throw new Zend_Exception('Duplicate Items in database');
//        }
//
//        return $rows->current();
//    }
//
//    public function fetchRowByNodePositionWithModel($node, $position, $columns = array('name')) {
//         $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//                ->where('node = ?', $node)
//                ->where('position = ?', $position)
//                ->join('homenet_device_models', 'homenet_device_models.id = homenet_devices.model', $columns)
//                ->limit(1);
//
//        return $this->fetchRow($select);
//    }
//
//    public function fetchDriverByNodePosition($node, $position) {
//        $row = $this->fetchRowByNodePositionWithModel($node, $position, array('driver'));
//
//        if(is_null($row)){
//            throw new Zend_Exception('Device doesn\'t Exist');
//        }
//
//        if (!class_exists($row->driver)) {
//            throw new Zend_Exception('Device driver ' . $row->driver . ' not found');
//        }
//
//        return new $row->driver($row);
//    }
//
//    public function fetchAllByNode($node) {
//        $select = $this->select()->where('node = ?', $node)
//                        ->order('position ASC');
//
//        return $this->fetchAll($select);
//    }
//
//     public function fetchAllByNodeWithModel($node, $columns = array('name')) {
//        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//                ->where('node = ?', $node)
//                ->join('homenet_device_models', 'homenet_device_models.id = homenet_devices.model', $columns)
//                ->order('position ASC');
//
//        return $this->fetchAll($select);
//    }
//
//    public function fetchDriversByNode($device) {
//
//        $rows = $this->fetchAllByNodeWithModel($device, array('driver','settings'));
//
//        $drivers = array();
//
//        foreach ($rows as $row) {
//
//            if (!class_exists($row->driver)) {
//                throw new Zend_Exception('Device driver ' . $row->driver . ' not found');
//            }
//
//            $drivers[$row->position] = new $row->driver($row);
//        }
//
//        return $drivers;
//    }
//
//    public function fetchDriversByHouseNodeDevice($house, $node, $device) {
//        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//                ->where('homenet_devices.position = ?', $device)
//                ->join('homenet_nodes', 'homenet_nodes.id = homenet_devices.node', array('house'))
//
//                ->where('homenet_nodes.node = ?', $node)
//                ->where('homenet_nodes.house = ?', $house)
//                ->join('homenet_device_models', 'homenet_device_models.id = homenet_devices.model', array('driver'))
//
//                ->limit(1);
//
//        $row = $this->fetchRow($select);
//
//        if(is_null($row)){
//            throw new Zend_Exception('Device doesn\'t Exist');
//        }
//
//        if (!class_exists($row->driver)) {
//            throw new Zend_Exception('Device driver ' . $row->driver . ' not found');
//        }
//
//        return new $row->driver($row);
//    }
//
//    public function fetchNodeDeviceById($id) {
//        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//                ->from(null,array('homenet_devices.position AS device'))
//                ->where('homenet_devices.id = ?', $id)
//                ->join('homenet_nodes', 'homenet_nodes.id = homenet_devices.node', array('id', 'node', 'uplink'))
//                ->limit(1);
//
//
//         $row = $this->fetchRow($select);
//
//        if (empty($row)) {
//            throw new Zend_Exception('No results');
//        }
//
//        //die(debugArray($row->toArray()));
//
//        return $row;
//    }


    //fetchDriverByHouseNodePosition

}