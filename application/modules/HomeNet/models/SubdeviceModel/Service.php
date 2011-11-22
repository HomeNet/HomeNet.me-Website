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
 * @subpackage SubdeviceModel
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_SubdeviceModel_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_SubdeviceModel_MapperInterface
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_SubdeviceModel_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_SubdeviceModel_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_SubdeviceModel_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_SubdeviceModel_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
    /**
     * Get all SubdeviceModels
     * 
     * @return HomeNet_Model_SubdeviceModel[] (HomeNet_Model_SubdeviceModel_Interface[]) 
     */
    public function getObjects(){
        return $this->getMapper()->fetchObjects();
    }

    /**
     * Get SubdeviceModel by id
     * 
     * @param int $id
     * @return HomeNet_Model_SubdeviceModel (HomeNet_Model_SubdeviceModel_Interface)
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        $result = $this->getMapper()->fetchObjectById((int) $id);

        if (empty($result)) {
            throw new NotFoundException('Subdevice Model not found', 404);
        }
        return $result;
    }

    /**
     * Get SubdeviceModels by Ids
     * 
     * @param array $ids
     * @return HomeNet_Model_SubdeviceModel[] (HomeNet_Model_SubdeviceModel_Interface[]) 
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectsByIds(array $ids){
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        $results = $this->getMapper()->fetchObjectsByIds($ids);

        if (empty($results)) {
            throw new NotFoundException('Subdevice: not found', 404);
        }
        return $results;
    }

    /**
     * Get SubdeviceModel Plugins by ids
     * 
     * @param array $ids
     * @return driver 
     * @throws InvalidArgumentException
     */
    public function getPluginsByIds(array $ids){
        if (empty($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        
        $results = $this->getObjectsByIds($ids);
        
        $objects = array();

        foreach($results as $value){
            $objects[$value->id] = $value;
        }

        $subdevices = array();
        foreach($ids as $key => $value){
            $driver = $objects[$value]->driver;
            $subdevices[] = new $driver(array('data' => array('position'=> $key),'model'=>$objects[$value]));
        }

        return $subdevices;
    }
    
    /**
     * Create a new SubdeviceModel
     * 
     * @param HomeNet_Model_SubdeviceModel_Interface|array $mixed
     * @return HomeNet_Model_SubdeviceModel (HomeNet_Model_SubdeviceModel_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_SubdeviceModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_SubdeviceModel(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid SubdeviceModel');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Update an existing SubdeviceModel
     * 
     * @param HomeNet_Model_SubdeviceModel_Interface|array $mixed
     * @return HomeNet_Model_SubdeviceModel (HomeNet_Model_SubdeviceModel_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_SubdeviceModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_SubdeviceModel(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid SubdeviceModel');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Delete a SubdeviceModel
     * 
     * @param HomeNet_Model_SubdeviceModel_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new HomeNet_Model_SubdeviceModel();
            $object->id = $mixed;
        } elseif ($mixed instanceof HomeNet_Model_SubdeviceModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_SubdeviceModel(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Subdevice');
        }

        return $this->getMapper()->delete($object);
    }

    /**
     * Delete all data. Used for unit testing/Will not work in production 
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