<?php

/*
 * RoomService.php
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
 * Description of HouseServices
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class HomeNet_Model_Subdevice_Service {

    /**
     * @var HomeNet_Model_SubdeviceMapper_Interface
     */
    protected $_mapper;

    /**
     * @return HomeNet_Model_Subdevice_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Subdevice_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_Subdevice_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }


//    public function fetchSubdeviceById($id);
//
//    public function fetchSubdeviceByIdWithModel($id, $columns);
//
//    public function fetchSubdevicesByDevice($device);
//
//    public function fetchSubdeviceByDeviceWithModel($device, $columns);
//
//    public function fetchSubdevicesByRoom($subdevice);
//
//    public function fetchSubdevicesByRoomWithModel($subdevice, $columns);

    /**
     * @param int $id
     * @return HomeNet_Model_Subdevice_Interface
     */
    public function getObjectById($id) {
        $subdevice = $this->getMapper()->fetchObjectById($id);

        if (empty($subdevice)) {
            throw new HomeNet_Model_Exception('Subdevice not found', 404);
        }
        return $subdevice;
    }

    public function getObjectsByDevice($device){

        $subdevices = $this->getMapper()->fetchObjectsByDevice($device);

        if (empty($subdevices)) {
            throw new HomeNet_Model_Exception('Subdevice not found', 404);
        }
        return $subdevices;
    }

    public function getObjectsByRoom($room){
        $subdevices = $this->getMapper()->fetchObjectsByRoom($room);

        if (empty($subdevices)) {
           return array();

           // throw new HomeNet_Model_Exception('Subdevice not found', 404);
        }
        
        $array = array();

        foreach($subdevices as $subdevice){
            $array[$subdevice->id] = $subdevice;
        }

        return $array;
    }

//    public function getModelsByIds($ids){
//        $smService = new HomeNet_Model_SubdeviceModel_Service();
//        $m = $smService->getObjectsByIds($ids);
//        $models = array();
//        foreach($m as $value){
//            $models[$value->id] = $value;
//        }
//
//        $subdevices = array();
//        foreach($ids as $key => $value){
//            $driver = $models[$value]->driver;
//            $subdevice = new $driver(array('model'=>$models[$value]));
//             $subdevice->order = $key;
//             $subdevices[] = $subdevice
//        }
//
//        return $subdevices;
//
//    }

    public function create($subdevice) {
        if ($subdevice instanceof HomeNet_Model_Subdevice_Interface) {
            $h = $subdevice;
        } elseif (is_array($subdevice)) {
            $h = new HomeNet_Model_Subdevice(array('data' => $subdevice));
        } else {
            throw new HomeNet_Model_Exception('Invalid Subdevice');
        }
        unset($subdevice);
        

        $subdevice = $this->getMapper()->save($h);

//        $houseService = new HomeNet_Model_HousesService();
//        $house = $houseService->getHouseById($subdevice->house);
//        $houseService->clearCacheById($subdevice->house);

//        $types = array('house' => 'House',
//            'apartment' => 'Apartment',
//            'condo' => 'Condo',
//            'other' => '',
//            'na' => '');

       // $table = new HomeNet_Model_DbTable_Alerts();

        //$table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new room ' . $subdevice->name . ' to their ' . $types[$this->house->type] . ' ' . $this->house->name . ' to HomeNet', null, $id);
       // $table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new room ' . $subdevice->name . ' to ' . $house->name . ' to HomeNet', null, $subdevice->id);


        return $subdevice;
    }

    public function update($subdevice) {
        if ($subdevice instanceof HomeNet_Model_Subdevice_Interface) {
            $h = $subdevice;
        } elseif (is_array($subdevice)) {
            $h = new HomeNet_Model_Subdevice(array('data' => $subdevice));
        } else {
            throw new HomeNet_Model_Exception('Invalid Subdevice');
        }
        $row = $this->getMapper()->save($h);

        //$houseService = new HomeNet_Model_HousesService();
        //$houseService->clearCacheById($this->house);

        return $row;
    }

    public function delete($subdevice) {
        //if is id
        if (is_int($subdevice)) {
            $h = new HomeNet_Model_Subdevice();
            $h->id = $subdevice;
        //if object
        } elseif ($subdevice instanceof HomeNet_Model_Subdevice_Interface) {
            $h = $subdevice;
        //if is array
        } elseif (is_array($subdevice)) {
            $h = new HomeNet_Model_Subdevice(array('data' => $subdevice));
        } else {
            throw new HomeNet_Model_Exception('Invalid Subdevice');
        }

        $row = $this->getMapper()->delete($h);

        //$houseService = new HomeNet_Model_HousesService();
        //$houseService->clearCacheById($this->house);

        return $row;
    }

}