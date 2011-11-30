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
 * Description of ComponentsMapperDbTable
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class HomeNet_Model_Component_MapperDbTable implements HomeNet_Model_Component_MapperInterface {

    protected $_table = null;

    /**
     * @return Zend_Db_Table;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new Zend_Db_Table('homenet_components');
            $this->_table->setRowClass('HomeNet_Model_Component_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    

    public function fetchObjectById($id) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('homenet_components.id = ?', $id)
                ->join('homenet_component_models', 'homenet_component_models.id = homenet_components.model', array('plugin', 'name AS modelName'))
                ->order('order ASC');

        return $this->getTable()->fetchRow($select);

    }

    public function fetchObjectsByDevice($device) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('device = ?', $device)
                ->join('homenet_component_models', 'homenet_component_models.id = homenet_components.model', array('plugin', 'name AS modelName'))
                ->order('order ASC');

        return $this->getTable()->fetchAll($select);
    }

    public function fetchObjectsByRoom($room) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('room = ?', $room)
                ->join('homenet_component_models', 'homenet_component_models.id = homenet_components.model', array('plugin', 'name AS modelName'))
                ->order('order ASC');

        return $this->getTable()->fetchAll($select);
    }

    public function save(HomeNet_Model_Component_Interface $component) {


        if (($component instanceof HomeNet_Model_Room_DbTableRow) && ($component->isConnected())) {
            return $component->save();
        } elseif (!is_null($component->id)) {
            $row = $this->getTable()->find($component->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($component->toArray());

        return $row->save();

    }

    public function delete(HomeNet_Model_Component_Interface $component) {

        if (($component instanceof HomeNet_Model_Room_DbTableRow) && ($component->isConnected())) {
            return $component->delete();
        } elseif (!is_null($component->id)) {
            return $this->getTable()->find($component->id)->current()->delete();
        }

        throw new HomeNet_Model_Exception('Invalid Component');
    }

    public function deleteAll() {
        if (APPLICATION_ENV != 'production') {
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `' . $this->getTable()->info('name') . '`');
        }
    }

}