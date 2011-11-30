<?php

/*
 * ComponentModelMapperDbTable.php
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
 * @subpackage ComponentModel
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_ComponentModel_MapperDbTable implements HomeNet_Model_ComponentModel_MapperInterface {

    protected $_table = null;

    /**
     * @return Zend_Db_Table;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new Zend_Db_Table('homenet_component_models');
            $this->_table->setRowClass('HomeNet_Model_ComponentModel_DbTableRow');
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    public function fetchObjects() {

        $select = $this->getTable()->select()
                        ->order('id ASC');//array('id ', 'name asc')
        return $this->getTable()->fetchAll($select);
    }

    public function fetchObjectById($id) {

        return $this->getTable()->find($id)->current();
    }

    public function fetchObjectsByIds($ids) {
       // die(debugArray($ids));
        $select = $this->getTable()->select('settings')->where('id in (?)', array_unique($ids));
        return $this->getTable()->fetchAll($select);
    }

    public function save(HomeNet_Model_ComponentModel_Interface $object) {

        if (($object instanceof HomeNet_Model_ComponentModel_DbTableRow) && ($object->isConnected())) {
            return $object->save();
        } elseif (!is_null($object->id)) {
            $row = $this->getTable()->find($object->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($object->toArray());
       // die(debugArray($row));
        return $row->save();
    }

    public function delete(HomeNet_Model_ComponentModel_Interface $object) {

        if (($object instanceof HomeNet_Model_ComponentModel_DbTableRow) && ($object->isConnected())) {
            return $object->delete();
        } elseif (!is_null($object->id)) {
            return $this->getTable()->find($object->id)->current()->delete();
        }

        throw new HomeNet_Model_Exception('Invalid ComponentModel');
    }
    public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getTable()->getAdapter()->query('TRUNCATE TABLE `'. $this->getTable()->info('name').'`');
        }
    }
}