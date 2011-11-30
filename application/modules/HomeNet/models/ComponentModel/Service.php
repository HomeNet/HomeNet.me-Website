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
 * @subpackage ComponentModel
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_ComponentModel_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_ComponentModel_MapperInterface
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_ComponentModel_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_ComponentModel_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_ComponentModel_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_ComponentModel_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
    /**
     * Get all ComponentModels
     * 
     * @return HomeNet_Model_ComponentModel[] (HomeNet_Model_ComponentModel_Interface[]) 
     */
    public function getObjects(){
        return $this->getMapper()->fetchObjects();
    }

    /**
     * Get ComponentModel by id
     * 
     * @param int $id
     * @return HomeNet_Model_ComponentModel (HomeNet_Model_ComponentModel_Interface)
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        $result = $this->getMapper()->fetchObjectById((int) $id);

        if (empty($result)) {
            throw new NotFoundException('Component Model not found', 404);
        }
        return $result;
    }

    /**
     * Get ComponentModels by Ids
     * 
     * @param array $ids
     * @return HomeNet_Model_ComponentModel[] (HomeNet_Model_ComponentModel_Interface[]) 
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectsByIds(array $ids){
        if (empty($ids)) {
            throw new InvalidArgumentException('Invalid Ids');
        }
        
        //check ids
        foreach($ids as $id){
            if(!is_numeric($id)){
                throw new InvalidArgumentException('Invalid Id: '.$id);
            }
        }
        
        $results = $this->getMapper()->fetchObjectsByIds($ids);
        
        if (count($results) == 0) {
            throw new NotFoundException('No ContentModels Found', 404);
        }
        //@todo check to make sure all results asked for are recieved
        
        $array = array();
        foreach ($results as $row) {
            $array[$row->id] = $row;
        }

        return $array;
    }

    /**
     * Get ComponentModel Plugins by ids
     * 
     * @param array $ids
     * @return driver 
     * @throws InvalidArgumentException
     */
    public function getComponentsByIds(array $ids){
        if (empty($ids)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        
        $results = $this->getObjectsByIds($ids);
        
        $objects = array();

        foreach($results as $value){
            $objects[$value->id] = $value;
        }

        $subdevices = array();
        foreach($ids as $key => $value){
            $class = 'HomeNet_Plugin_Component_'.$objects[$value]->plugin.'_Component';
            $subdevices[] = new $class(array('data' => array('position'=> $key),'model'=>$objects[$value]));
        }

        return $subdevices;
    }
    
    /**
     * Create a new ComponentModel
     * 
     * @param HomeNet_Model_ComponentModel_Interface|array $mixed
     * @return HomeNet_Model_ComponentModel (HomeNet_Model_ComponentModel_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_ComponentModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_ComponentModel(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid ComponentModel');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Update an existing ComponentModel
     * 
     * @param HomeNet_Model_ComponentModel_Interface|array $mixed
     * @return HomeNet_Model_ComponentModel (HomeNet_Model_ComponentModel_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_ComponentModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_ComponentModel(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid ComponentModel');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Delete a ComponentModel
     * 
     * @param HomeNet_Model_ComponentModel_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if ($mixed instanceof HomeNet_Model_ComponentModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_ComponentModel(array('data' => $mixed));
        } elseif (is_numeric($mixed)) {
            $object = $this->getObjectById((int) $mixed);
        } else {
            throw new InvalidArgumentException('Invalid ComponentModel');
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