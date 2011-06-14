<?php

/*
 * DeviceService.php
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
 * @subpackage DeviceModel
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_DeviceModel_Service {

    /**
     * @var HomeNet_Model_DevicesMapper_Interface
     */
    protected $_mapper;

    /**
     * @return HomeNet_Model_DevicesMapper_Interface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_DeviceModel_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_DeviceModel_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }





 




    
    public function getObjectById($id) {
        $deviceModel = $this->getMapper()->fetchObjectById($id);

        if (empty($deviceModel)) {
            throw new HomeNet_Model_Exception('DeviceModel not found', 404);
        }
        return $deviceModel;
    }

    public function getObjects() {
        $deviceModel = $this->getMapper()->fetchObjects();

        if (empty($deviceModel)) {
            throw new HomeNet_Model_Exception('DeviceModel not found', 404);
        }
        return $deviceModel;
    }

    public function getObjectsByStatus($status = 1) {
        $deviceModel = $this->getMapper()->fetchObjectsByStatus($status);

        if (empty($deviceModel)) {
            throw new HomeNet_Model_Exception('DeviceModel not found', 404);
        }
        return $deviceModel;
    }

    public function getCategories(){
         $array = array(
            0 => 'Code Stubs',
            1 => 'Temperature Sensors',
            2 => 'Humidity Sensors',
            3 => 'Barometric Pressure Sensors',
            4 => 'Light Sensors',
            5 => 'Motion Sensors',
            6 => 'Switches',
            7 => 'Outputs',
            8 => 'Security');

        // die(debugArray($array));

         return $array;

    } 

    public function create($deviceModel) {
        if ($deviceModel instanceof HomeNet_Model_DeviceModel_Interface) {
            $h = $deviceModel;
        } elseif (is_array($deviceModel)) {
            $h = new HomeNet_Model_DeviceModel(array('data' => $deviceModel));
        } else {
            throw new HomeNet_Model_Exception('Invalid DeviceModel');
        }

        return $this->getMapper()->save($h);
    }

    public function update($deviceModel) {
        if ($deviceModel instanceof HomeNet_Model_DeviceModel_Interface) {
            $h = $deviceModel;
        } elseif (is_array($deviceModel)) {
            $h = new HomeNet_Model_DeviceModel(array('data' => $deviceModel));
        } else {
            throw new HomeNet_Model_Exception('Invalid DeviceModel');
        }

        return $this->getMapper()->save($h);
    }

    public function delete($deviceModel) {
        if (is_int($deviceModel)) {
            $h = new HomeNet_Model_DeviceModel();
            $h->id = $deviceModel;
        } elseif ($deviceModel instanceof HomeNet_Model_DeviceModel_Interface) {
            $h = $deviceModel;
        } elseif (is_array($deviceModel)) {
            $h = new HomeNet_Model_Device(array('data' => $deviceModel));
        } else {
            throw new HomeNet_Model_Exception('Invalid DeviceModel');
        }

        return $this->getMapper()->delete($h);
    }

}