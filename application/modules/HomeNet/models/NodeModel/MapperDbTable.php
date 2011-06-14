<?php

/*
 * NodeMapperDbTable.php
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
 * @subpackage NodeModel
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_NodeModel_MapperDbTable implements HomeNet_Model_NodeModel_MapperInterface {

    protected $_table = null;

    /**
     * @return HomeNet_Model_DbTable_Nodes;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new HomeNet_Model_DbTable_NodeModels();
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    public function fetchNodeModelById($id) {
        return $this->getTable()->find($id)->current();
    }

    public function fetchNodeModels() {
        $select = $this->getTable()->select()
                        ->order(array('type', 'name asc'));
        return $this->getTable()->fetchAll($select);
    }

    public function fetchNodeModelsByStatus($status) {
        $select = $this->getTable()->select()->where('status = ?', $status)
                        ->order(array('type', 'name asc'));
        return $this->getTable()->fetchAll($select);
    }

    public function save(HomeNet_Model_NodeModel_Interface $node) {


        if (($node instanceof HomeNet_Model_DbTableRow_Node) && ($node->isConnected())) {
            $node->save();
            return;
        } elseif (!is_null($node->id)) {
            $row = $this->getTable()->find($node->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($node->toArray());
        // die(debugArray($row));
        $row->save();

        return $row;
    }

    public function delete(HomeNet_Model_NodeModel_Interface $node) {

        if (($node instanceof HomeNet_Model_DbTableRow_NodeModel) && ($node->isConnected())) {
            $node->delete();
            return true;
        } elseif (!is_null($node->id)) {
            $row = $this->getTable()->find($node->id)->current()->delete();
            return;
        }

        throw new HomeNet_Model_Exception('Invalid NodeModel');
    }

}