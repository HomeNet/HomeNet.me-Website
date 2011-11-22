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
 * @package HomeNet
 * @subpackage DeviceModel
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_DeviceModel_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_DevicesMapper_Interface
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_DevicesMapper_Interface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_DeviceModel_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * Set get mapper
     * 
     * @param HomeNet_Model_DeviceModel_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_DeviceModel_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * Get DeviceModel by id
     * 
     * @param integer $id
     * @return HomeNet_Model_DeviceModel (HomeNet_Model_DeviceModel_Inteface)
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('DeviceModel: '.$id.' not found', 404);
        }
        return $result;
    }

    /**
     * Get all DeviceModels
     * 
     * @return HomeNet_Model_DeviceModel[] (HomeNet_Model_DeviceModel_Inteface[])
     */
    public function getObjects() {
        $result = $this->getMapper()->fetchObjects();

        return $result;
    }

    /**
     * Get DeviceModels by status
     * 
     * @param integer $status
     * @return HomeNet_Model_DeviceModel (HomeNet_Model_DeviceModel_Inteface) 
     */
    public function getObjectsByStatus($status = 1) {
        if (empty($status) || !is_numeric($status)) {
            throw new InvalidArgumentException('Invalid Status');
        }
        $result = $this->getMapper()->fetchObjectsByStatus($status);
//
//        if (empty($deviceModel)) {
//            throw new NotFoundException('DeviceModel not found', 404);
//        }
        return $result;
    }

    /**
     * Get the list of device categories 
     * 
     * @return array  
     */
    public function getCategories() {
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

    /**
     * Create a new DeviceModel
     * 
     * @param HomeNet_Model_DeviceModel_Interface|array $mixed
     * @return HomeNet_Model_DeviceModel (HomeNet_Model_DeviceModel_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_DeviceModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_DeviceModel(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid DeviceModel');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Update an existing DeviceModel
     * 
     * @param HomeNet_Model_DeviceModel_Interface|array $mixed
     * @return HomeNet_Model_DeviceModel (HomeNet_Model_DeviceModel_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_DeviceModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_DeviceModel(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid DeviceModel');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Delete a DeviceModel
     * 
     * @param HomeNet_Model_DeviceModel_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = $this->getObjectbyId($mixed);
        } elseif ($mixed instanceof HomeNet_Model_DeviceModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_DeviceModel(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid DeviceModel');
        }

        return $this->getMapper()->delete($object);
    }

    /**
     * Delete all DeviceModels. Used for unit testing/Will not work in production 
     *
     * @return boolean Success
     * @throws NotAllowedException
     */
    public function deleteAll() {
        if (APPLICATION_ENV == 'production') {
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }
}