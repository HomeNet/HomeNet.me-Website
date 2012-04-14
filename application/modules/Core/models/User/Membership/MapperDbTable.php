<?php

/*
 * ApikeyMapperDbTable.php
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

require_once "MapperInterface.php";

/**
 * @package Core
 * @subpackage User
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_User_Membership_MapperDbTable implements Core_Model_User_Membership_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return Core_Model_DbTable_User_Membership;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('user_memberships');
            $this->_table->setRowClass('Core_Model_User_Membership_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable(Zend_Db_Table_Abstract $table) {
        $this->_table = $table;
    }

    public function fetchObjectById($id) {
        return $this->getTable()->find($id)->current();
    }

    public function fetchObjectsByUser($user) {
        $select = $this->getTable()->select('group')->where('user = ?', $user);
        $results = $this->getTable()->fetchAll($select);
        return $results;
    }
    
    public function fetchGroupIdsByUser($user) {
        $select = $this->getTable()->select('group')->where('user = ?', $user);
        $result = $this->getTable()->fetchAll($select);
        $array = array();
        foreach ($result as $row) {
            $array[] = $row->group;
        }

        return $array;
    }

    public function fetchUserIdsByGroup($group) {
        $select = $this->getTable()->select('user')->where('`group` = ?', $group);
        $result = $this->getTable()->fetchAll($select);
        $array = array();
        foreach ($result as $row) {
            $array[] = $row->user;
        }

        return $array;
    }
    
    public function fetchObjectByUserGroup($user, $group) {
        $select = $this->getTable()->select()->where('`user` = ?', $user)->where('`group` = ?', $group);
        return $this->getTable()->fetchRow($select);
    }

    public function save(Core_Model_User_Membership_Interface $object) {

        if (($object instanceof Core_Model_UserMembership_DbTableRow) && ($object->isConnected())) {

            return $object->save();
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current();
            if (empty($row)) {
                $row = $this->getTable()->createRow();
            }
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($object->toArray());
        // die(debugArray($row));
        $row->save();

        return $row;
    }

    public function delete(Core_Model_User_Membership_Interface $object) {

        if (($object instanceof Core_Model_UserMembership_DbTableRow) && ($object->isConnected())) {
            $object->delete();
            return true;
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current()->delete();
            return;
        }

        throw new Exception('Invalid User Object');
    }

    public function deleteByUser($user) {
        $where = $this->getTable()->getAdapter()->quoteInto('user = ?', $user);
        return $this->getTable()->delete($where);
    }
    
    public function deleteByUserGroup($user, $group) {
        $where = array();
        $where[] = $this->getTable()->getAdapter()->quoteInto('`user` = ?', $user);
        $where[] = $this->getTable()->getAdapter()->quoteInto('`group` = ?', $group);
        return $this->getTable()->delete($where);
    }

    public function deleteByGroup($group) {
        $where = $this->getTable()->getAdapter()->quoteInto('`group` = ?', $group);
        return $this->getTable()->delete($where);
    }

    public function deleteAll() {
        if (APPLICATION_ENV == 'testing') {
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `' . $this->getTable()->info('name') . '`');
        }
    }

}