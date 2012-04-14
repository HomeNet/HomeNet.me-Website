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
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * @package Core
 * @subpackage Group
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Group_MapperDbTable implements Core_Model_Group_MapperInterface {

    /**
     * @var Zend_Db_Table_Abstract 
     */
    protected $_table = null;

    /**
     * @return Core_Model_DbTable_Group;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('groups');
            $this->_table->setRowClass('Core_Model_Group_DbTableRow');
        }
        return $this->_table;
    }

    /**
     * @param Zend_Db_Table_Abstract $table 
     */
    public function setTable(Zend_Db_Table_Abstract $table) {
        $this->_table = $table;
    }

    /**
     * @return int
     */
    public function fetchCount() {

        $select = $this->getTable()->select();
        $select->from($this->getTable(), array('count(*) as num_of_items'));
        $result = $this->getTable()->fetchRow($select);

        return($result->num_of_items);
    }

    /**
     * @return Zend_Db_RowSet 
     */
    public function fetchObjects() {
        $select = $this->getTable()->select()->order('id ASC');
        return $this->getTable()->fetchAll($select);
    }

    /**
     * @param int $id
     * @return Core_Model_Group_DbTableRow 
     */
    public function fetchObjectById($id) {
        return $this->getTable()->find($id)->current();
    }

    /**
     * @param int $type
     * @return Core_Model_Group_DbTableRow 
     */
    public function fetchObjectsByType($type) {
        if(is_array($type)){
            $select = $this->getTable()->select()->where('type in (?)', $type);
        } else {
            $select = $this->getTable()->select()->where('type = ?', $type);
        }

        return $this->getTable()->fetchAll($select);
    }

    /**
     * @param int $id
     * @param int $amount   amount to +increase/-decrease count by
     * @return int          The number of rows updated. 
     */
    public function incrementUserCount($id, $amount) {

        $data = array('user_count' => new Zend_Db_Expr($this->getTable()->getAdapter()->quoteInto('user_count + ?', $amount)));
        $where = $this->getTable()->getAdapter()->quoteInto('id = ?', $id);



        // $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        return $this->getTable()->update($data, $where);
    }

    /**
     * @param int $id
     * @param int $user_count   
     * @return int              The number of rows updated.
     */
    public function updateUserCount($id, $user_count) {

        $data = array('user_count' => $user_count);
        $where = $this->getTable()->getAdapter()->quoteInto('id = ?', $id);

        return $this->getTable()->update($data, $where);
    }

    /**
     * @param Core_Model_Group_Interface $object
     * @return Core_Model_Group_DbTableRow  
     */
    public function save(Core_Model_Group_Interface $object) {

        if (($object instanceof Core_Model_Group_DbTableRow) && ($object->isConnected())) {
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

    /**
     * @param Core_Model_Group_Interface $object
     * @return bool Success
     * @throws Exception
     */
    public function delete(Core_Model_Group_Interface $object) {

        if (($object instanceof Core_Model_Group_DbTableRow) && ($object->isConnected())) {
            $object->delete();
            return true;
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current()->delete();
            return true;
        }

        throw new Exception('Invalid Group Object');
    }

    /**
     * Delete All Groups, Will not work in production
     */
    public function deleteAll() {
        if (APPLICATION_ENV != 'production') {
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `' . $this->getTable()->info('name') . '`');
        }
    }

}