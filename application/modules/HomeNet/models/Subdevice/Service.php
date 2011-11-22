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
 * @subpackage Subdevice
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Subdevice_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_SubdeviceMapper_Interface
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_Subdevice_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Subdevice_MapperDbTable();
        }
        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_Subdevice_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_Subdevice_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * Get Subdevices by id
     * 
     * @param int $id
     * @return HomeNet_Model_Subdevice (HomeNet_Model_Subdevice_Interface)
     * @throw InvalidArgumentException
     * @throw NotFoundException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Subdevice ' . $id . ' Not Found', 404);
        }
        return $result;
    }

    /**
     * Get Subdevices by device id
     * 
     * @param int $house
     * @return HomeNet_Model_Subdevice[] (HomeNet_Model_Subdevice_Interface[])
     * @throw InvalidArgumentException
     */
    public function getObjectsByDevice($device) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        return $this->getMapper()->fetchObjectsByDevice($device);
    }

    /**
     * Get Subdevices by room id
     * 
     * @param int $house
     * @return HomeNet_Model_Subdevice[] (HomeNet_Model_Subdevice_Interface[])
     * @throw InvalidArgumentException
     */
    public function getObjectsByRoom($room) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        $subdevices = $this->getMapper()->fetchObjectsByRoom($room);

        if (empty($subdevices)) {
            return array();
        }

        $array = array();

        foreach ($subdevices as $subdevice) {
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

    /**
     * Create a new Subdevice
     * 
     * @param HomeNet_Model_Subdevice_Interface|array $mixed
     * @return HomeNet_Model_Subdevice (HomeNet_Model_Subdevice_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_Subdevice_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Subdevice(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Subdevice');
        }

        $result = $this->getMapper()->save($object);

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

        return $result;
    }

    /**
     * Update an existing Subdevice
     * 
     * @param HomeNet_Model_Subdevice_Interface|array $mixed
     * @return HomeNet_Model_Subdevice (HomeNet_Model_Subdevice_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_Subdevice_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Subdevice(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Subdevice');
        }

        $result = $this->getMapper()->save($object);

        //$houseService = new HomeNet_Model_HousesService();
        //$houseService->clearCacheById($this->house);

        return $result;
    }

    /**
     * Delete a Subdevice
     * 
     * @param HomeNet_Model_Subdevice_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = $this->getObjectbyId($mixed);
        } elseif ($mixed instanceof HomeNet_Model_Subdevice_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Subdevice(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Subdevice');
        }

        $result = $this->getMapper()->delete($object);

        //$houseService = new HomeNet_Model_HousesService();
        //$houseService->clearCacheById($this->house);

        return $result;
    }

    /**
     * Delete all Subdevices. Used for unit testing/Will not work in production 
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