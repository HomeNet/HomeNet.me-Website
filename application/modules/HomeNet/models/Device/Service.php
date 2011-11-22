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
 * @subpackage Device
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Device_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_DevicesMapperInterface
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_DevicesMapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Device_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_Device_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_Device_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * Get plugin helper
     * 
     * @param HomeNet_Model_Device $device
     * @return driver 
     * @throws InvalidArgumentException
     */
    protected function _getPlugin(HomeNet_Model_Device $device) {

        if (empty($device->driver)) {
            throw new InvalidArgumentException('Missing Subdevice Driver');
        }

        if (!class_exists($device->driver)) {
            throw new InvalidArgumentException('Subdevice Driver ' . $device->driver . ' Doesn\'t Exist');
        }

        return new $device->driver(array('data' => $device->toArray()));
    }

    /**
     * Get Device by id
     * 
     * @param integer $id
     * @return HomeNet_Model_Device (HomeNet_Model_Device_Interface)
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Device: '.$id.' Not Found', 404);
        }
        return $result;
    }

    /**
     * Get Device by node, posistion
     * 
     * @param integer $node Node Id
     * @param iteger $position
     * @return HomeNet_Model_Device (HomeNet_Model_Device_Interface)
     * @throws InvalidArgumentException
     * @throws NotFoundException 
     */
    public function getObjectByNodePosition($node, $position) {
        if (empty($node) || !is_numeric($node)) {
            throw new InvalidArgumentException('Invalid Node');
        }
        if (empty($position) || !is_numeric($position)) {
            throw new InvalidArgumentException('Invalid Position');
        }
        $result = $this->getMapper()->fetchObjectByNodePosition($node, $position);

        if (empty($result)) {
            throw new NotFoundException('Device not found', 404);
        }
        return $result;
    }

    /**
     * Get plugin by model
     * 
     * @param integer $id Model Id
     * @return driver 
     * @throws InvalidArgumentException
     */
    public function getPluginByModel($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        $dmService = new HomeNet_Model_DeviceModel_Service();

        $model = $dmService->getObjectById($id);

        $driver = $model->driver;

        return new $driver(array('model' => $model));
    }

//    public function getDriverByNodePosition($node, $position){
//        $device = $this->getDeviceByNodePositionWithModel($node, $position, array('driver'));
//
//        return $this->_getDriver($device);
//    }
//    public function getObjectByNodePosition($node, $position){
//        $device = $this->getMapper()->fetchObjectByNodePosition($node, $position);
//
//        if (empty($device)) {
//            throw new HomeNet_Model_Exception('Device not found', 404);
//        }
//        return $device;
//    }
//    public function getDevicesByNode($node){
//        $device = $this->getMapper()->fetchDevicesByNode($node);
//
//        if (empty($device)) {
//            //throw new HomeNet_Model_Exception('Device not found', 404);
//        }
//        return $device;
//    }

    /**
     * Get Devices by node
     * 
     * @param type $node
     * @return HomeNet_Model_Device[] (HomeNet_Model_Device_Interface[]) 
     * @throws InvalidArgumentException
     */
    public function getObjectsByNode($node) {
        if (empty($node) || !is_numeric($node)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        //@todo valide not exists
        $devices = $this->getMapper()->fetchObjectsByNode($node);

        if (empty($devices)) {
            // throw new HomeNet_Model_Exception('Device not found', 404);
        }

        $array = array();

        foreach ($devices as $device) {
            $array[$device->position] = $device;
        }
        return $array;
    }

    /**
     * Get Device by house, node, device
     * 
     * @param integer $house house id
     * @param integer $node Node id
     * @param integer $device device id
     * @return HomeNet_Model_Device (HomeNet_Model_Device_Interface) 
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectByHouseNodeDevice($house, $node, $device) {
        if (empty($house) || !is_numeric($house)) {
            throw new InvalidArgumentException('Invalid House');
        }
        if (empty($node) || !is_numeric($node)) {
            throw new InvalidArgumentException('Invalid Node');
        }
        if (empty($device) || !is_numeric($device)) {
            throw new InvalidArgumentException('Invalid Device');
        }
        $object = $this->getMapper()->fetchObjectByHouseNodeDevice((int)$house, (int)$node, (int)$device);

        if (empty($object)) {
            throw new NotFoundException('Device not found ' . "$house, $node, $device", 404);
        }
        return $object;
    }

    /**
     * Get Device by id with node data
     * 
     * @param integer $id
     * @return HomeNet_Model_Device (HomeNet_Model_Device_Interface) 
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectByIdWithNode($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        
        $device = $this->getMapper()->fetchObjectByIdWithNode($id);

        if (empty($device)) {
            throw new NotFoundException('Device not found', 404);
        }
        return $device;
    }

    /**
     * Create a new Device
     * 
     * @param HomeNet_Model_Device_Interface|array $mixed
     * @return HomeNet_Model_Device (HomeNet_Model_Device_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_Device_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Device(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Device');
        }

        $device = $this->getMapper()->save($object);

        $subdevices = $h->getSubdevices(false);

        if (!empty($subdevices)) {

            $sService = new HomeNet_Model_Subdevice_Service();
            foreach ($subdevices as $subdevice) {
                $subdevice->device = $device->id;
                // die(debugArray($subdevice));
                $sService->create($subdevice);
            }
        }

        return $device;
    }

    /**
     * Update an existing Device
     * 
     * @param HomeNet_Model_Device_Interface|array $mixed
     * @return HomeNet_Model_Device (HomeNet_Model_Device_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_Device_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Device(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Device');
        }

        $device = $this->getMapper()->save($object);

        $subdevices = $object->getSubdevices(false);

        if (!empty($subdevices)) {

            $sService = new HomeNet_Model_Subdevice_Service();
            foreach ($subdevices as $subdevice) {
                $sService->update($subdevice);
            }
        }
        return $device;
    }

    /**
     * Delete a Device
     * 
     * @param HomeNet_Model_Device_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = $this->getObjectById($mixed);
        } elseif ($mixed instanceof HomeNet_Model_Device_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Device(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Datapoint');
        }

        $result = $this->getMapper()->delete($object);

        $subdevices = $object->getSubdevices();

        if (!empty($subdevices)) {

            $sService = new HomeNet_Model_Subdevice_Service();
            foreach ($subdevices as $subdevice) {
                $sService->delete($subdevice);
            }
        }

        return $result;
    }

    /**
     * Delete all devices. Used for unit testing/Will not work in production 
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