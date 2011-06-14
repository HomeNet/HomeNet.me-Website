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

    protected $_table = null;

    /**
     *
     * @return HomeNet_Model_DbTable_Rooms;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new HomeNet_Model_DbTable_Rooms();
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    public function fetchRoomById($id) {

        return $this->getTable()->find($id)->current();
    }

    public function fetchRoomsByHouse($id) {

        $select = $this->getTable()->select()->where('house = ?', $id);
        return $this->getTable()->fetchAll($select);
    }

    public function fetchRoomsByHouses($ids) {

        $select = $this->getTable()->select()->where('house in (?)', $ids);
        return $this->getTable()->fetchAll($select);
    }

    public function fetchRoomsByRegion($id) {

        $select = $this->getTable()->select()->where('region = ?', $id);
        return $this->getTable()->fetchAll($select);
    }

    public function save(HomeNet_Model_Room_Interface $room) {


        if (($room instanceof HomeNet_Model_DbTableRow_Room) && ($room->isConnected())) {
            $room->save();
            return;
        } elseif (!is_null($room->id)) {
            $row = $this->getTable()->find($room->id);
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($room->toArray());
       // die(debugArray($row));
        $row->save();

        return $row;
    }

    public function delete(HomeNet_Model_Room_Interface $room) {

        if (($room instanceof HomeNet_Model_DbTableRow_Room) && ($room->isConnected())) {
            $room->delete();
            return true;
        } elseif (!is_null($room->id)) {
            $row = $this->getTable()->find($room->id)->current()->delete();
            return;
        }

        throw new HomeNet_Model_Exception('Invalid Room');
    }
}