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
 * @subpackage Room
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Room_MapperDbTable implements HomeNet_Model_Room_MapperInterface {

    protected $_table;
    
     /**
     * @var int House ID 
     */
    protected $_houseId;
 
    public function __construct($house = null) {
        $this->setHouseId($house);
    }
    
    public function setHouseId($house){
        if($house !== null){
            if (empty($house) || !is_numeric($house)) {
                throw new InvalidArgumentException('Invalid House ID');
            }
            $this->_houseId = $house;
        }
    }
    
    public function getHouseId(){
        if($this->_houseId === null){
            throw new InvalidArgumentException('Invalid House ID');
        }
        
        return $this->_houseId;
    }

     /**
     * @return Zend_Db_Table;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('homenet_rooms');
            $this->_table->setRowClass('HomeNet_Model_Room_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    public function fetchObjectById($id) {

        $select = $this->getTable()->select()
                ->where('house = ?', $this->getHouseId())
                ->where('id = ?', $id)
                ->limit(1);
        return $this->getTable()->fetchRow($select);
    }

    public function fetchObjects() {

        $select = $this->getTable()->select()
                ->where('house = ?', $this->getHouseId());
        return $this->getTable()->fetchAll($select);
    }

    public function fetchObjectsByHouses($ids) {

        $select = $this->getTable()->select()->where('house in (?)', $ids);
        return $this->getTable()->fetchAll($select);
    }

    public function fetchObjectsByRegion($region) {

        $select = $this->getTable()->select()
                ->where('house = ?', $this->getHouseId())
                ->where('region = ?', $region);
        return $this->getTable()->fetchAll($select);
    }

    public function save(HomeNet_Model_Room_Interface $object) {

        if (($object instanceof HomeNet_Model_Room_DbTableRow) && ($object->isConnected())) {
            return $object->save();
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($object->toArray());
        
        return $row->save();
    }

    public function delete(HomeNet_Model_Room_Interface $object) {

        if (($object instanceof HomeNet_Model_Room_DbTableRow) && ($object->isConnected())) {
            return $object->delete();
        } elseif ($object->id !== null) {
            return $this->getTable()->find($object->id)->current()->delete();
        }

        throw new InvalidArgumentException('Invalid Room');
    }
    
    public function deleteAll() {
        if (APPLICATION_ENV != 'production') {
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `' . $this->getTable()->info('name') . '`');
        }
    }
}