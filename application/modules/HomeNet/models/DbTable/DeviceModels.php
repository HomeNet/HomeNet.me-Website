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
 * @subpackage Device
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_DbTable_DeviceModels extends Zend_Db_Table_Abstract
{

    protected $_name = 'homenet_device_models';

    protected $_rowClass = 'HomeNet_Model_DbTableRow_DeviceModel';

//    public function  fetchRowById($id)
//    {
//        return $this->find($id)->current();
//    }
//
//    public function fetchAllByStatus($status = 1){
//         $select = $this->select()->where('status = ?', $status)
//                 ->order('name asc');
//        return $this->fetchAll($select);
//    }
//
//    public function fetchSettingsById($id){
//         $select = $this->select('settings')->where('id = ?', $id)
//                        ->limit(1);
//        return $this->fetchRow($select)->settings;
//    }
//
//    /**
//     * look up a device and return it's driver object
//     *
//     * @param int $id
//     * @return HomeNet_Model_Device_Abstract
//     */
//
//    public function fetchDriverById($id){
//         $row = $this->find($id)->current();
//
//         if(!class_exists($row->driver)){
//             throw new Zend_Exception('Device Driver '.$row->driver.' Not Found');
//         }
//         $driver = new $row->driver();
//         $driver->loadModel($row);
//
//         return $driver;
//    }

}