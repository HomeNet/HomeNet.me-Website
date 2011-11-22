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
 * Description of SubdevicesMapperDbTable
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class HomeNet_Model_Subdevice_MapperDbTable implements HomeNet_Model_Subdevice_MapperInterface {

    protected $_table = null;

    /**
     * @return Zend_Db_Table;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new Zend_Db_Table('homenet_subdevice_models');
            $this->_table->setRowClass('HomeNet_Model_Subdevice_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    protected function _getDriver($subdevice) {

        $driver = $subdevice->driver;

        if (empty($driver)) {
            throw new HomeNet_Model_Exception('Missing Subdevice Driver');
        }

        if (!class_exists($driver)) {
            throw new HomeNet_Model_Exception('Subdevice Driver ' . $subdevice->driver . ' Doesn\'t Exist');
        }

        return new $driver(array('data' => $subdevice->toArray()));
    }

    protected function _getDrivers($subdevices) {
        $objects = array();
        foreach ($subdevices as $subdevice) {
            $objects[] = $this->_getDriver($subdevice);
        }

        return $objects;
    }

    public function fetchObjectById($id) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('homenet_subdevices.id = ?', $id)
                ->join('homenet_subdevice_models', 'homenet_subdevice_models.id = homenet_subdevices.model', array('driver', 'name AS modelName'))
                ->order('order ASC');

        $row = $this->getTable()->fetchRow($select);
        if (empty($row)) {
            return null;
        }

        return $this->_getDriver($row);
    }

    public function fetchObjectsByDevice($device) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('device = ?', $device)
                ->join('homenet_subdevice_models', 'homenet_subdevice_models.id = homenet_subdevices.model', array('driver', 'name AS modelName'))
                ->order('order ASC');

        $rows = $this->getTable()->fetchAll($select);

        if (empty($rows)) {
            return array();
        }

        return $this->_getDrivers($rows);
    }

    public function fetchObjectsByRoom($room) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('room = ?', $room)
                ->join('homenet_subdevice_models', 'homenet_subdevice_models.id = homenet_subdevices.model', array('driver', 'name AS modelName'))
                ->order('order ASC');

        $rows = $this->getTable()->fetchAll($select);

        if (empty($rows)) {
            return array();
        }

        return $this->_getDrivers($rows);
    }

    public function save(HomeNet_Model_Subdevice_Interface $subdevice) {


        if (($subdevice instanceof HomeNet_Model_DbTableRow_Room) && ($subdevice->isConnected())) {
            $subdevice->save();
            return;
        } elseif (!is_null($subdevice->id)) {
            $row = $this->getTable()->find($subdevice->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        // die(debugArray($subdevice));

        $row->fromArray($subdevice->toArray());
        //  die(debugArray($row));
        $row->save();

        return $row;
    }

    public function delete(HomeNet_Model_Subdevice_Interface $subdevice) {

        if (($subdevice instanceof HomeNet_Model_DbTableRow_Room) && ($subdevice->isConnected())) {
            $subdevice->delete();
            return true;
        } elseif (!is_null($subdevice->id)) {
            $row = $this->getTable()->find($subdevice->id)->current()->delete();
            return true;
        }

        throw new HomeNet_Model_Exception('Invalid Subdevice');
    }

    public function deleteAll() {
        if (APPLICATION_ENV != 'production') {
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `' . $this->getTable()->info('name') . '`');
        }
    }

}