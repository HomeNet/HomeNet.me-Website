<?php

/*
 * SubdeviceModelMapperDbTable.php
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
 * @subpackage SubdeviceModel
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_SubdeviceModel_MapperDbTable implements HomeNet_Model_SubdeviceModel_MapperInterface {

    protected $_table = null;

    /**
     *
     * @return HomeNet_Model_DbTable_SubdeviceModels;
     */
    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = new HomeNet_Model_DbTable_SubdeviceModels();
        }
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    public function fetchSubdeviceModels() {

        $select = $this->getTable()->select()
                        ->order('id ASC');//array('id ', 'name asc')
        return $this->getTable()->fetchAll($select);
    }

    public function fetchSubdeviceModelById($id) {

        return $this->getTable()->find($id)->current();
    }

    public function fetchSubdeviceModelsByIds($ids) {
       // die(debugArray($ids));
        $select = $this->getTable()->select('settings')->where('id in (?)', array_unique($ids));
        $rows = $this->getTable()->fetchAll($select);
        $array = array();
        foreach ($rows as $row) {
            $array[$row->id] = $row;
        }
        return $array;
    }




    public function save(HomeNet_Model_SubdeviceModel_Interface $subdeviceModel) {


        if (($subdeviceModel instanceof HomeNet_Model_DbTableRow_SubdeviceModel) && ($subdeviceModel->isConnected())) {
            $subdeviceModel->save();
            return;
        } elseif (!is_null($subdeviceModel->id)) {
            $row = $this->getTable()->find($subdeviceModel->id)->current();
        } else {
            $row = $this->getTable()->createRow();
        }

        $row->fromArray($subdeviceModel->toArray());
       // die(debugArray($row));
        $row->save();

        return $row;
    }

    public function delete(HomeNet_Model_SubdeviceModel_Interface $subdeviceModel) {

        if (($subdeviceModel instanceof HomeNet_Model_DbTableRow_SubdeviceModel) && ($subdeviceModel->isConnected())) {
            $subdeviceModel->delete();
            return true;
        } elseif (!is_null($subdeviceModel->id)) {
            $row = $this->getTable()->find($subdeviceModel->id)->current()->delete();
            return;
        }

        throw new HomeNet_Model_Exception('Invalid SubdeviceModel');
    }
}