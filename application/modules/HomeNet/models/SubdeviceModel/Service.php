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
 * @package HomeNet
 * @subpackage SubdeviceModel
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_SubdeviceModel_Service {

    /**
     * @var HomeNet_Model_SubdeviceModel_MapperInterface
     */
    protected $_mapper;

    /**
     * @return HomeNet_Model_SubdeviceModel_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_SubdeviceModel_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_SubdeviceModel_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return HomeNet_Model_SubdeviceModel_Interface
     */
    public function getObjectById($id) {
        $subdevice = $this->getMapper()->fetchSubdeviceModelById($id);

        if (empty($subdevice)) {
            throw new HomeNet_Model_Exception('Subdevice Model not found', 404);
        }
        return $subdevice;
    }

    public function getObjectsByIds($ids){
        $subdevices = $this->getMapper()->fetchSubdeviceModelsByIds($ids);

        if (empty($subdevices)) {
            throw new HomeNet_Model_Exception('Subdevice not found', 404);
        }
        return $subdevices;
    }

    public function getObjects(){
        $subdevices = $this->getMapper()->fetchSubdeviceModels();

        if (empty($subdevices)) {
            throw new HomeNet_Model_Exception('Subdevice not found', 404);
        }
        return $subdevices;
    }

    public function getSubdevicesByIds($ids){
        $m = $this->getObjectsByIds($ids);
        $models = array();

        foreach($m as $value){
            $models[$value->id] = $value;
        }

        $subdevices = array();
        foreach($ids as $key => $value){
            $driver = $models[$value]->driver;
            $subdevices[] = new $driver(array('data' => array('position'=> $key),'model'=>$models[$value]));
        }

        return $subdevices;
    }


    public function create($subdeviceModel) {
        if ($subdeviceModel instanceof HomeNet_Model_SubdeviceModel_Interface) {
            $h = $subdeviceModel;
        } elseif (is_array($subdeviceModel)) {
            $h = new HomeNet_Model_SubdeviceModel(array('data' => $subdeviceModel));
        } else {
            throw new HomeNet_Model_Exception('Invalid Subdevice');
        }
        unset($subdeviceModel);
        $subdeviceModel = $this->getMapper()->save($h);

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


        return $subdeviceModel;
    }

    public function update($subdeviceModel) {
        if ($subdeviceModel instanceof HomeNet_Model_SubdeviceModel_Interface) {
            $h = $subdeviceModel;
        } elseif (is_array($subdeviceModel)) {
            $h = new HomeNet_Model_SubdeviceModel(array('data' => $subdeviceModel));
        } else {
            throw new HomeNet_Model_Exception('Invalid Room');
        }
        $row = $this->getMapper()->save($h);

        //$houseService = new HomeNet_Model_HousesService();
        //$houseService->clearCacheById($this->house);

        return $row;
    }

    public function delete($subdeviceModel) {
        //if is id
        if (is_int($subdeviceModel)) {
            $h = new HomeNet_Model_Subdevice();
            $h->id = $subdeviceModel;
        //if object
        } elseif ($subdeviceModel instanceof HomeNet_Model_SubdeviceModel_Interface) {
            $h = $subdeviceModel;
        //if is array
        } elseif (is_array($subdeviceModel)) {
            $h = new HomeNet_Model_SubdeviceModel(array('data' => $subdeviceModel));
        } else {
            throw new HomeNet_Model_Exception('Invalid Room');
        }

        $row = $this->getMapper()->delete($subdeviceModel);

        //$houseService = new HomeNet_Model_HousesService();
        //$houseService->clearCacheById($this->house);

        return $row;
    }

}