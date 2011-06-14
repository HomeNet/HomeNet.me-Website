<?php

/*
 * HouseUserMapperDbTable.php
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
 * @subpackage HouseUser
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_HouseUser_MapperDbTable implements HomeNet_Model_HouseUser_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return HomeNet_Model_DbTable_HouseUsers;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new HomeNet_Model_DbTable_HouseUsers();
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    public function fetchHousesbyUser($user) {
        $select = $this->getTable()->select()->where('user = ?', $user);
        return $this->getTable()->fetchAll($select);
    }

     public function fetchHouseUserbyId($id) {
        return $this->getTable()->find($id)->current();
    }

    public function save(HomeNet_Model_HouseUser_Interface $houseUser) {


        if (($houseUser instanceof HomeNet_Model_DbTableRow_HouseUser) && ($houseUser->isConnected())) {
            $houseUser->save();
            return;
        } elseif (!is_null($houseUser->id)) {
            $row = $this->fetchHouseUserbyId($houseUser->id);
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($houseUser->toArray());
        // die(debugArray($row));
        $row->save();

        return $row;
    }

    public function delete(HomeNet_Model_HouseUser_Interface $houseUser) {

        if (($houseUser instanceof HomeNet_Model_DbTableRow_HouseUser) && ($houseUser->isConnected())) {
            $houseUser->delete();
            return true;
        } elseif (!is_null($houseUser->id)) {
            $where = $this->getTable()->getAdapter()->quoteInto('id = ?', $houseUser->id);
            $row = $this->getTable()->delete($where);
            return;
        }

        throw new HomeNet_Model_Exception('Invalid HouseUser');
    }

}