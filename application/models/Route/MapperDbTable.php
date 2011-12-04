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
 * @subpackage Route
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Route_MapperDbTable implements Core_Model_Route_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return Core_Model_DbTable_Route;
     */
    public function getTable() {
        if ($this->_table === null) {
            $this->_table = new Zend_Db_Table('routes');
            $this->_table->setRowClass('Core_Model_Route_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable(Zend_Db_Table_Abstract $table) {
        $this->_table = $table;
    }

    public function fetchObjects() {
        return $this->getTable()->fetchAll();
    }

    public function fetchObjectById($id) {
        return $this->getTable()->find($id)->current();
    }

    public function save(Core_Model_Route_Interface $object) {

        if (($object instanceof Core_Model_DbTableRow_Route) && ($object->isConnected())) {
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
        try {
            $row->save();
        } catch (Exception $e) {
            if (strstr($e->getMessage(), '1062 Duplicate')) {
                throw new DuplicateEntryException("Route Name Already Exists");
           // } elseif (strstr($e->getMessage(), '1048 Column')) {
           //     throw new InvalidArgumentException("Invalid Column");
            } else {
                throw new Exception($e->getMessage());
            }
        };

        return $row;
    }

    public function delete(Core_Model_Route_Interface $object) {

        if (($object instanceof Core_Model_DbTableRow_Route) && ($object->isConnected())) {
            $object->delete();
            return true;
        } elseif ($object->id !== null) {
            $row = $this->getTable()->find($object->id)->current()->delete();
            return;
        }

        throw new Exception('Invalid Route Object');
    }

    public function deleteAll() {
        if (APPLICATION_ENV != 'production') {
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `' . $this->getTable()->info('name') . '`');
        }
    }

}