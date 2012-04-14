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
 * @subpackage Acl_Group
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Acl_Group_Service {

    /**
     * @var Core_Model_Acl_Group_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Core_Model_Acl_Group_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Core_Model_Acl_Group_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * @param Content_Model_Section_MapperInterface $mapper 
     */
    public function setMapper(Core_Model_Acl_Group_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id   Acl_Group Id
     * @return Core_Model_Acl_Group 
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Id: '.$id.' Not Found', 404);
        }
        return $result;
    }

    /**
     * @param type $group   Group Id
     * @return Core_Model_Acl_Group[] 
     */
    public function getObjectsByGroup($group) {
        if (!is_numeric($group)) {
            throw new InvalidArgumentException('Invalid Group');
        }
        return $this->getMapper()->fetchObjectsByGroup($group);
    }

    /**
     * @param array $groups Array of Group Ids
     * @return Core_Model_Acl_Group[] 
     */
    public function getObjectsByGroups(array $groups) {
        if (count($groups) == 0) {
            throw new InvalidArgumentException('Groups array is empty');
        }
        $rows = $this->getMapper()->fetchObjectsByGroups($groups);
        $array = array();
        foreach ($rows as $row) {
            $array[$row->group][] = $row;
        }

        return $array;
    }

    /**
     * @param array $groups Array of Group Ids
     * @param string $module
     * @return Core_Model_Acl_Group[] 
     */
    public function getObjectsByGroupsModule(array $groups, $module) {
        if (count($groups) == 0) {
            throw new InvalidArgumentException('Groups array is empty');
        }
        if (empty($module)) {
            throw new InvalidArgumentException('Invalid Module');
        }
        
        
        $rows = $this->getMapper()->fetchObjectsByGroupsModule($groups, $module);

        $array = array();
        foreach ($rows as $row) {
            $array[$row->group][] = $row;
        }

        return $array;
    }
    
    /**
     * @param array $groups Array of Group Ids
     * @param string $module
     * @param integer $collection
     * @return Core_Model_Acl_Group[] 
     */
    public function getObjectsByGroupsModuleCollection(array $groups, $module, $collection) {
        if (count($groups) == 0) {
            throw new InvalidArgumentException('Groups array is empty');
        }
        if (empty($module)) {
            throw new InvalidArgumentException('Invalid Module');
        }
        if (empty($collection) || !is_numeric($collection)) {
            throw new InvalidArgumentException('Invalid Collection');
        }
       
        
        
        $rows = $this->getMapper()->fetchObjectsByGroupsModuleCollection($groups, $module, $collection);
        $array = array();
        foreach ($rows as $row) {
            $array[$row->group][] = $row;
        }

        return $array;
    }

    /**
     * @param array $groups Array of Group Ids
     * @param string $module
     * @param string $controller
     * @param integer $object
     * @return Core_Model_Acl_Group[] 
     */
    public function getObjectsByGroupsModuleControllerObject(array $groups, $module, $controller, $object = null) {
        
        if (count($groups) == 0) {
            throw new InvalidArgumentException('Groups array is empty');
        }
        if (empty($module)) {
            throw new InvalidArgumentException('Invalid Module');
        }
        if (empty($controller)) {
            throw new InvalidArgumentException('Invalid Controller');
        }
        
        
        $rows = $this->getMapper()->fetchObjectsByGroupsModuleControllerObject($groups, $module, $controller, $object);
        $array = array();
        foreach ($rows as $row) {
            $array[$row->group][] = $row;
        }

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $array;
    }

    /**
     * @param array $groups
     * @param type $module
     * @param type $controller
     * @param integer[] $objects
     * @return Core_Model_Acl_Group[] 
     */
    public function getObjectsByGroupsModuleControllerObjects(array $groups, $module, $controller, array $objects) {
        
        if (count($groups) == 0) {
            throw new InvalidArgumentException('Groups array is empty');
        }
        if (empty($module)) {
            throw new InvalidArgumentException('Invalid Module');
        }
        if (empty($controller)) {
            throw new InvalidArgumentException('Invalid Controller');
        }
        if (empty($objects)) {
            throw new InvalidArgumentException('Objects array is empty');
        }
        
        
        $rows = $this->getMapper()->fetchObjectsByGroupsModuleControllerObjects($groups, $module, $controller, $objects);
        $array = array();
        foreach ($rows as $row) {
            $array[$row->group][] = $row;
        }

        return $array;
    }
    
    public function allow($group, $module, $controller = null, $action = null, $collection = null, $obj = null){
        $object = new Core_Model_Acl_Group();
        $object->group = $group;
        $object->module = $module;
        $object->controller = $controller;
        $object->action = $action;
        $object->collection = $collection;
        $object->object = $obj;
        $object->permission = 1;
        
        return $this->create($object);
    }

    /**
     * @param Core_Model_Acl_Group_Interface|array $mixed
     * @return Core_Model_Acl_Group
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof Core_Model_Acl_Group_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Acl_Group(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param Core_Model_Acl_Group_Interface|array $mixed
     * @return Core_Model_Acl_Group
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof Core_Model_Acl_Group_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Acl_Group(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param Core_Model_Acl_Group_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new Core_Model_Acl_Group();
            $object->id = $mixed;
        } elseif ($mixed instanceof Core_Model_Acl_Group_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Acl_Group(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->delete($object);
    }

    public function deleteByGroup($group) {
        return $this->getMapper()->deleteByGroup($group);
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