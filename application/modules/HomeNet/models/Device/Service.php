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
     * @param HomeNet_Model_Device $object
     * @return HomeNet_Model_Device
     * @throws InvalidArgumentException
     */
    protected function _getPlugin($object) {

        if (empty($object->plugin)) {
            throw new InvalidArgumentException('Missing Device Plugin');
        }

        $class = 'HomeNet_Plugin_Device_' . $object->plugin . '_Device';

        if (!class_exists($class, true)) {
            throw new Exception('Device Plugin: ' . $object->plugin . ' Doesn\'t Exist');
        }

        return new $class(array('data' => $object->toArray()));
    }

    /**
     * Get plugin helpers
     * 
     * @param HomeNet_Model_Device[] $objects
     * @return HomeNet_Model_Device 
     * @throws InvalidArgumentException
     */
    protected function _getPlugins($objects) {
        $plugins = array();
        foreach ($objects as $object) {
            $plugins[] = $this->_getPlugin($object);
        }

        return $plugins;
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
            throw new NotFoundException('Device: ' . $id . ' Not Found', 404);
        }
        return $this->_getPlugin($result);
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
        return $this->_getPlugin($result);
    }

    /**
     * Get plugin by model
     * 
     * @param integer $id Model Id
     * @return driver 
     * @throws InvalidArgumentException
     */
    public function newObjectFromModel($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        $dmService = new HomeNet_Model_DeviceModel_Service();

        $model = $dmService->getObjectById($id);

        $class = 'HomeNet_Plugin_Device_' . $model->plugin . '_Device';

        if (!class_exists($class, true)) {
            throw new Exception('Device Plugin: ' . $model->plugin . ' Doesn\'t Exist');
        }

        return new $class(array('model' => $model));
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
            $array[$device->position] = $this->_getPlugin($device);
        }
        return $array;
    }
    
    /**
     * Get Devices by node
     * 
     * @param type $node
     * @return HomeNet_Model_Device[] (HomeNet_Model_Device_Interface[]) 
     * @throws InvalidArgumentException
     */
    public function getTrashedObjectsByNode($node) {
        if (empty($node) || !is_numeric($node)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        //@todo valide not exists
        $devices = $this->getMapper()->fetchObjectsByNode($node, HomeNet_Model_Device::STATUS_TRASHED);

        if (empty($devices)) {
            // throw new HomeNet_Model_Exception('Device not found', 404);
        }

        $array = array();

        foreach ($devices as $device) {
            $array[$device->position] = $this->_getPlugin($device);
        }
        return $array;
    }

    /**
     * Get Device by house, node, device
     * 
     * @param integer $house house id
     * @param integer $nodeAddress Node address
     * @param integer $position device id
     * @return HomeNet_Model_Device (HomeNet_Model_Device_Interface) 
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectByHouseNodeaddressPosition($house, $nodeAddress, $position) {
        if (empty($house) || !is_numeric($house)) {
            throw new InvalidArgumentException('Invalid House');
        }
        if (empty($nodeAddress) || !is_numeric($nodeAddress)) {
            throw new InvalidArgumentException('Invalid Node Address');
        }
        if (empty($position) || !is_numeric($position)) {
            throw new InvalidArgumentException('Invalid Device');
        }
        
        $result = $this->getMapper()->fetchObjectByHouseNodeaddressPosition((int) $house, (int) $nodeAddress, (int) $position);

        if (empty($result)) {
            throw new NotFoundException('Device not found ' . "$house, $nodeAddress, $position", 404);
        }

        return $this->_getPlugin($result);
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

        $result = $this->getMapper()->fetchObjectByIdWithNode($id);

        if (empty($result)) {
            throw new NotFoundException('Device not found', 404);
        }
        return $result;
    }
    
    /**
     * Mark a device as deleted and cascades to all child elements
     * 
     * @param HomeNet_Model_Device_Interface $object 
     */
    public function trash(HomeNet_Model_Device_Interface $object){
        $object->status = HomeNet_Model_Device::STATUS_TRASHED;
        $result = $this->update($object);
        
        $components = $object->getComponents();
        $service = new HomeNet_Model_Component_Service();
        foreach($components as $component){
            $service->trash($component);
        }
        return $result;
    }
    
    /**
     * Undelete a device and cascades to all child elements
     * 
     * @param HomeNet_Model_Device_Interface $object 
     */
    public function untrash(HomeNet_Model_Device_Interface $object){
        $object->status = HomeNet_Model_Device::STATUS_LIVE;
        $result = $this->update($object);
        
        $components = $object->getComponents();
        $service = new HomeNet_Model_Component_Service();
        foreach($components as $component){
            $service->untrash($component);
        }
        return $result;
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

        $result = $this->getMapper()->save($object);

        $components = $object->getComponents(false);

        if (!empty($components)) {

            $sService = new HomeNet_Model_Component_Service();
            foreach ($components as $component) {
                $component->status = $object->status;
                $component->house = $object->house;
                $component->room = $object->getRoom()->id;
                $component->device = $result->id;

                $sService->create($component);
            }
        }
        return $result;
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

        $result = $this->getMapper()->save($object);

        $components = $object->getComponents(false);

        if (!empty($components)) {

            $sService = new HomeNet_Model_Component_Service();
            foreach ($components as $component) {
                $sService->update($component);
            }
        }
        return $result;
    }

    /**
     * Delete a Device
     * 
     * @param HomeNet_Model_Device_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if ($mixed instanceof HomeNet_Model_Device_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Device(array('data' => $mixed));
        } elseif (is_numeric($mixed)) {
            $object = $this->getObjectById((int) $mixed);
        } else {
            throw new InvalidArgumentException('Invalid Device');
        }

        $components = $object->getComponents();

        $result = $this->getMapper()->delete($object);

        if (!empty($components)) {

            $sService = new HomeNet_Model_Component_Service();
            foreach ($components as $component) {
                $sService->delete($component);
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