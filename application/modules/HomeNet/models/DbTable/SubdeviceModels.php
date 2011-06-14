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
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package HomeNet
 * @subpackage Subdevice
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_DbTable_SubdeviceModels extends Zend_Db_Table_Abstract {

    protected $_name = 'homenet_subdevice_models';
    protected $_rowClass = 'HomeNet_Model_DbTableRow_SubdeviceModel';

//    public function fetchRowById($id) {
//
//        return $this->find($id)->current();
//    }
//
//    public function fetchAllByIds($ids) {
//       // die(debugArray($ids));
//        $select = $this->select('settings')->where('id in (?)', array_unique($ids));
//        $rows = $this->fetchAll($select);
//        $array = array();
//        foreach ($rows as $row) {
//            $array[$row->id] = $row;
//        }
//        return $array;
//    }
//
//    public function fetchDriversByIds($ids) {
//        $rows = $this->fetchAllByIds($ids);
//
//        $drivers = array();
//        foreach ($ids as $id) {
//
//            $driver = $rows[$id]->driver;
//
//            if (!class_exists($driver)) {
//                throw new Zend_Exception('Subdevice driver ' . $driver . ' not found');
//            }
//
//             $d = new $driver();
//             $d->loadModel($rows[$id]);
//             $drivers[] = $d;
//        }
//
//
//        return $drivers;
//    }

}