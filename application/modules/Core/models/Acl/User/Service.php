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
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package Core
 * @subpackage Acl_User
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Acl_User_Service {

    /**
     * @var Core_Model_Acl_User_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Core_Model_Acl_User_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Core_Model_Acl_User_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * @param Content_Model_Section_MapperInterface $mapper 
     */
    public function setMapper(Core_Model_Acl_User_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id   Acl_User Id
     * @return Core_Model_Acl_User
     * @throws NotFoundException 
     */
    public function getObjectById($id) {
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Id ' . $id . ' Not Found', 404);
        }
        return $result;
    }

    /**
     * @param integer $user  User Id
     * @return Core_Model_Acl_User 
     */
    public function getObjectsByUser($user) {
        return $this->getMapper()->fetchObjectsByUser($user);
    }

    /**
     * @param integer $user User Id
     * @param string $module
     * @return Core_Model_Acl_User[] 
     */
    public function getObjectsByUserModule($user, $module) {
        return $this->getMapper()->fetchObjectsByUserModule($user, $module);
    }
    
    /**
     * @param integer $user User Id
     * @param string $module
     * @param int $collection   Object Id
     * @return Core_Model_Acl_User[] 
     */
    public function getObjectsByUserModuleCollection($user, $module, $collection) {
        return $this->getMapper()->fetchObjectsByUserModuleCollection($user, $module, $collection);
    }

    /**
     * @param integer $user User Id
     * @param string $module
     * @param string $controller
     * @param int $object   Object Id
     * @return Core_Model_Acl_User[] 
     */
    public function getObjectsByUserModuleControllerObject($user, $module, $controller, $object = null) {
        return $this->getMapper()->fetchObjectsByUserModuleControllerObject($user, $module, $controller, $object);
    }
    
    
    

    /**
     * @param integer $user User Id
     * @param string $module
     * @param string $controller
     * @param array $objects
     * @return Core_Model_Acl_User[] 
     */
    public function getObjectsByUserModuleControllerObjects($user, $module, $controller, array $objects) {
        return $this->getMapper()->fetchObjectsByUserModuleControllerObjects($user, $module, $controller, $objects);
    }
    
     public function allow($user, $module, $controller = null, $action = null, $collection = null, $obj = null){
        $object = new Core_Model_Acl_User();
        $object->user = $user;
        $object->module = $module;
        $object->controller = $controller;
        $object->action = $action;
        $object->collection = $collection;
        $object->object = $obj;
        $object->permission = 1;
        
        return $this->create($object);
    }
    

//    public function getObjectsBySection($section){
//        $contents = $this->getMapper()->fetchObjectsBySection($section);
//
////        if (empty($contents)) {
////            throw new Exception('Apikey not found', 404);
////        }
//        return $contents;
//    }
//    public function getObjectsByIdHouse($id,$house){
//        $apikeys = $this->getMapper()->fetchObjectsByIdHouse($id,$house);
//
//        if (empty($apikeys)) {
//            return array();
//            //throw new Core_Model_Exception('Apikey not found', 404);
//        }
//        return $apikeys;
//    }

    /**
     * @param Core_Model_Acl_User_Interface|array $mixed
     * @return Core_Model_Acl_User
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof Core_Model_Acl_User_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Acl_User(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }
        
       // var_dump($object);

        return $this->getMapper()->save($object);
    }

    /**
     * @param Core_Model_Acl_User_Interface|array $mixed
     * @return Core_Model_Acl_User
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof Core_Model_Acl_User_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Acl_User(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param Core_Model_Acl_User_Interface|array $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new Core_Model_Acl_User();
            $object->id = $mixed;
        } elseif ($mixed instanceof Core_Model_Acl_User_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Acl_User(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->delete($object);
    }

    /**
     * @param integer $user User Id
     * @return boolean Success
     */
    public function deleteByUser($user) {
        return $this->getMapper()->deleteByUser($user);
    }
    
   public function deleteByModule($module) {
        if(($module === null) || !is_string($module)){
            throw new InvalidArgumentException('Invalid Module');
        }
        return $this->getMapper()->deleteByModule($module);
    }
    
    public function deleteByModuleCollection($module, $collection) {
        if(($module === null) || !is_string($module)){
            throw new InvalidArgumentException('Invalid Module');
        }
        if(($collection === null) || !is_numeric($collection)){
            throw new InvalidArgumentException('Invalid Collection');
        }
        return $this->getMapper()->deleteByModuleCollection($module, $collection);
    }
    
     public function deleteByUserModuleCollection($user, $module, $collection) {
         if(($user === null) || !is_numeric($user)){
            throw new InvalidArgumentException('Invalid User');
        }
        if(($module === null) || !is_string($module)){
            throw new InvalidArgumentException('Invalid Module');
        }
        if(($collection === null) || !is_numeric($collection)){
            throw new InvalidArgumentException('Invalid Collection');
        }
        return $this->getMapper()->deleteByUserModuleCollection($user, $module, $collection);
    }
    
    
    public function deleteByModuleControllerObject($module, $controller, $object) {
        if(($module === null) || !is_string($module)){
            throw new InvalidArgumentException('Invalid Module');
        }
        if(($controller === null) || !is_string($controller)){
            throw new InvalidArgumentException('Invalid Controller');
        }
        if(($object === null)){
            throw new InvalidArgumentException('Invalid Object');
        }
        return $this->getMapper()->deleteByModuleControllerObject($module, $controller, $object);
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
        return $this->getMapper()->deleteAll();
    }

}