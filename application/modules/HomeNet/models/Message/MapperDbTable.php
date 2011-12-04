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
 * @subpackage Message
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Message_MapperDbTable implements HomeNet_Model_Message_MapperInterface {

    protected $_table = null;

    /**
     * @return Zend_Db_Table;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('homenet_messages');
            $this->_table->setRowClass('HomeNet_Model_Message_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }
    
     public function fetchObjectById($id){
        $result = $this->getTable()->find($id)->current();
        return $result;

    }

    public function fetchObjectsByUser($user) {

        $select = $this->getTable()->select()->where('user = ?', $user);

        return $this->getTable()->fetchAll($select);
    }

    public function fetchobjectsByHouseOrUser($house, $user) {
        $houses = array();
        if (!is_array($house)) {
            $houses[] = $house;
        } else {
            $houses = $house;
        }

        $select = $this->getTable()->select()->where('user = ?', $user)
                        ->orWhere('house in (?)', $houses)
                        ->order('id DESC')
                        ->limit(20);

        return $this->getTable()->fetchAll($select);
    }

    public function save(HomeNet_Model_Message_Interface $object) {

        if (($object instanceof HomeNet_Model_Message_DbTableRow) && ($object->isConnected())) {
             return $object->save();
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($object->toArray());

        return $row->save();
    }

    public function delete(HomeNet_Model_Message_Interface $object) {

        if (($object instanceof HomeNet_Model_Message_DbTableRow) && ($object->isConnected())) {
            return $object->delete();
        } elseif ($object->id !== null) {
            return $this->getTable()->find($object->id)->current()->delete();
        }

        throw new HomeNet_Model_Exception('Invalid Room');
    }
    public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        }
    }
    

}