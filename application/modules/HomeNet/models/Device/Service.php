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
 * Description of HouseService
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class HomeNet_Model_Device_Service {

    /**
     * @var HomeNet_Model_DevicesMapperInterface
     */
    protected $_mapper;

    /**
     * @return HomeNet_Model_DevicesMapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Device_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_Device_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }


    protected function _getDriver($device){

            if(empty($device->driver)){
                throw new HomeNet_Model_Exception('Missing Subdevice Driver');
            }

            if(!class_exists($device->driver)){
                throw new HomeNet_Model_Exception('Subdevice Driver '.$device->driver.' Doesn\'t Exist');
            }

            return new $device->driver(array('data' => $device->toArray()));
        }

    public function getObjectById($id){
        $device = $this->getMapper()->fetchObjectById($id);

        if (empty($device)) {
            throw new HomeNet_Model_Exception('Device not found', 404);
        }
        return $device;
    }
    
    public function getObjectByNodePosition($node, $position){
        $device = $this->getMapper()->fetchObjectByNodePosition($node, $position);

        if (empty($device)) {
            throw new HomeNet_Model_Exception('Device not found', 404);
        }
        return $device;
    }

    public function getObjectByModel($id){

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
    
    public function getObjectsByNode($node){
        $devices = $this->getMapper()->fetchObjectsByNode($node);

        if (empty($devices)) {
           // throw new HomeNet_Model_Exception('Device not found', 404);
        }

        $array = array();
        
        foreach($devices as $device){
            $array[$device->position] = $device;
        }
        return $array;
    }

 
    
    public function getObjectByHouseNodeDevice($house, $node, $device){
        $object = $this->getMapper()->fetchObjectByHouseNodeDevice($house, $node, $device);

        if (empty($object)) {
            throw new HomeNet_Model_Exception('Device not found '."$house, $node, $device", 404);
        }
        return $object;
    }
    
    public function getDeviceByIdWithNode($id){
        $device = $this->getMapper()->fetchDeviceByIdWithNode($id);

        if (empty($device)) {
            throw new HomeNet_Model_Exception('Device not found', 404);
        }
        return $device;
    }





    
    public function create($device) {
        if ($device instanceof HomeNet_Model_Device_Interface) {
            $h = $device;
        } elseif (is_array($device)) {
            $h = new HomeNet_Model_Device(array('data' => $device));
        } else {
            throw new HomeNet_Model_Exception('Invalid Device');
        }

        $device = $this->getMapper()->save($h);

        $subdevices = $h->getSubdevices(false);

        if(!empty($subdevices)){

            $sService = new HomeNet_Model_Subdevice_Service();
            foreach($subdevices as $subdevice){
                $subdevice->device = $device->id;
               // die(debugArray($subdevice));
                $sService->create($subdevice);
            }
        }

        return $device;
    }

    public function update($device) {
        if ($device instanceof HomeNet_Model_Device_Interface) {
            $h = $device;
        } elseif (is_array($device)) {
            $h = new HomeNet_Model_Device(array('data' => $device));
        } else {
            throw new HomeNet_Model_Exception('Invalid Device');
        }
        $item = $this->getMapper()->save($h);

        $subdevices = $h->getSubdevices(false);

        //die(debugArray($subdevices));

        if(!empty($subdevices)){

            $sService = new HomeNet_Model_Subdevice_Service();
            foreach($subdevices as $subdevice){
                $sService->update($subdevice);
            }
        }

        return $item;
    }

    public function delete($device) {

        if (is_int($device)) {
            $h = new HomeNet_Model_Device();
            $h->id = $device;
        } elseif ($device instanceof HomeNet_Model_Device_Interface) {
            $h = $device;
        } elseif (is_array($device)) {
            $h = new HomeNet_Model_Device(array('data' => $device));
        } else {
            throw new HomeNet_Model_Exception('Invalid Device');
        }
       // die(debugArray($device));
      //  die('test:'.debugArray($subdevices));

        //$subdevices = $h->getSubdevices();
        

        $item = $this->getMapper()->delete($h);

        $subdevices = $h->getSubdevices();

        if(!empty($subdevices)){

            $sService = new HomeNet_Model_Subdevice_Service();
            foreach($subdevices as $subdevice){
                $sService->delete($subdevice);
            }
        }

        return $item;
    }

}