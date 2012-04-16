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
class HomeNet_Model_Network_MapperDbTable implements HomeNet_Model_Network_MapperInterface {

    protected $_table = null;

    /**
     * @return Zend_Db_Table;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('homenet_networks');
            $this->_table->setRowClass('HomeNet_Model_Network_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    public function fetchObjectById($id) {

        //= array('name','driver', 'max_devices')

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('homenet_networks.id = ?', $id)
                ->join('homenet_network_types', 'homenet_network_types.id = homenet_networks.type', array('plugin', 'settings AS type_settings'))
                ->limit(1);

        return $this->getTable()->fetchRow($select);
    }


    public function fetchObjectsByHouse($house) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('house = ?', $house)
               // ->where('homenet_networks.status = ?', $status)
                ->join('homenet_network_types', 'homenet_network_types.id = homenet_networks.type', array('plugin', 'name AS type_name', 'settings AS type_settings'));

        return $this->getTable()->fetchAll($select);
    }

    public function fetchObjectsByRoom($room) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('room = ?', $room)
              //  ->where('homenet_networks.status = ?', $status)
                ->join('homenet_network_types', 'homenet_network_types.id = homenet_networks.type', array('plugin', 'name AS type_name', 'settings AS type7l_settings'));

        return $this->getTable()->fetchAll($select);
    }

    public function fetchObjectsByHouseType($house, $type) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('house = ?', $house)
                ->where('type = ?', $type)
                ->join('homenet_network_types', 'homenet_network_types.id = homenet_networks.type', array('plugin', 'name AS type_name', 'settings AS type_settings'));
                
        

        return $this->getTable()->fetchAll($select);
    }

    public function save(HomeNet_Model_Network_Interface $object) {

        $settings = $object->settings;

        if (is_array($object->type_settings)) {
            $object->settings = array_diff_assoc($settings, $object->type_settings); // remove model settings
        }

        if (($object instanceof HomeNet_Model_Network_DbTableRow) && ($object->isConnected())) {
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

    public function delete(HomeNet_Model_Network_Interface $object) {

        if (($object instanceof HomeNet_Model_Network_DbTableRow) && ($object->isConnected())) {
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