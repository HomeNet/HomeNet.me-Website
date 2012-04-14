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

    /**
     * @var Zend_Db_Table 
     */
    protected $_table = null;

    /**
     * @return Zend_Db_Table;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('homenet_components');
            $this->_table->setRowClass('HomeNet_Model_Component_DbTableRow');
        }
        return $this->_table;
    }

    /**
     * @param type Zend_Db_Table 
     */
    public function setTable($table) {
        $this->_table = $table;
    }

    
    /**
     * @param int $id
     * @return HomeNet_Model_Component_DbTableRow 
     */
    public function fetchObjectById($id) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('homenet_components.id = ?', $id)
                ->join('homenet_component_models', 'homenet_component_models.id = homenet_components.model', array('plugin', 'datatype', 'name AS model_name'))
                ->order('order ASC');

        return $this->getTable()->fetchRow($select);

    }

    /**
     *
     * @param int $device
     * @param int $status
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchObjectsByDevice($device, $status = HomeNet_Model_Component::STATUS_LIVE) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('device = ?', $device)
                ->where('homenet_components.status = ?', $status)
                ->join('homenet_component_models', 'homenet_component_models.id = homenet_components.model', array('plugin', 'datatype', 'name AS model_name'))
                ->order('order ASC');

        return $this->getTable()->fetchAll($select);
    }

    /**
     *
     * @param int $room
     * @param int $status
     * @return Zend_Db_Table_Rowset_Abstract 
     */
    public function fetchObjectsByRoom($room, $status = HomeNet_Model_Component::STATUS_LIVE) {

        $select = $this->getTable()->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
        $select->setIntegrityCheck(false)
                ->where('room = ?', $room)
                ->where('homenet_components.status = ?', $status)
                ->join('homenet_component_models', 'homenet_component_models.id = homenet_components.model', array('plugin', 'datatype', 'name AS model_name'))
                ->order('order ASC');

        return $this->getTable()->fetchAll($select);
    }

    /**
     * @param HomeNet_Model_Component_Interface $object
     * @return HomeNet_Model_Component_DbTableRow 
     */

    public function save(HomeNet_Model_Component_Interface $object) {


        if (($object instanceof HomeNet_Model_Component_DbTableRow) && ($object->isConnected())) {
            return $object->save();
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($object->toArray());

        return $row->save();

    }

     /**
     * @param HomeNet_Model_Component_Interface $object
     * @return boolean
     * 
     * @throws InvalidArgumentException
     */
    public function delete(HomeNet_Model_Component_Interface $component) {

        if (($component instanceof HomeNet_Model_Component_DbTableRow) && ($component->isConnected())) {
            return $component->delete();
        } elseif (!is_null($component->id)) {
            return $this->getTable()->find($component->id)->current()->delete();
        }

        throw new InvalidArgumentException('Invalid Component');
    }

    public function deleteAll() {
        if (APPLICATION_ENV != 'production') {
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `' . $this->getTable()->info('name') . '`');
        }
    }

}