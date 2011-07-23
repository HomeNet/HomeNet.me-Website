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
 * @subpackage User
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

    public function setMapper(Core_Model_Acl_User_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return Core_Model_Group_Interface 
     */
    public function getObjectById($id) {
        $acl = $this->getMapper()->fetchObjectById($id);

        if (empty($acl)) {
            throw new NotFoundException('User Acl not found', 404);
        }
        return $acl;
    }
    
    public function getObjectsByGroup($group){
         return $this->getMapper()->fetchObjectsByGroup($group); 
    }
    
    public function getObjectsByGroups(array $groups){
      $rows = $this->getMapper()->fetchObjectsByGroups($groups); 
      $array = array();
        foreach($rows as $row){
            $array[$row->group][] = $row;
        }

        return $array;
    }
    
    public function getObjectsByGroupsModule(array $groups, $module) {
        $rows = $this->getMapper()->fetchObjectsByGroupsModule($groups, $module); 
        
        $array = array();
        foreach($rows as $row){
            $array[$row->group][] = $row;
        }

        return $array; 
    }
    
    public function getObjectsByGroupsModuleControllerObject(array $groups, $module,$controller, $object = null){
        $rows = $this->getMapper()->fetchObjectsByGroupsModuleControllerObject($groups, $module,$controller, $object);
        $array = array();
        foreach($rows as $row){
            $array[$row->group][] = $row;
        }

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $array;
    }
    
    public function getObjectsByGroupsModuleControllerObjects(array $groups, $module, $controller, array $objects){
        $rows = $this->getMapper()->fetchObjectsByGroupsModuleControllerObjects($groups, $module, $controller, $objects);
        $array = array();
        foreach($rows as $row){
            $array[$row->group][] = $row;
        }

        return $array;
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
     * @param mixed $acl
     * @throws InvalidArgumentException 
     */
    public function create($acl) {
        if ($acl instanceof Core_Model_Acl_User_Interface) {
            $h = $acl;
        } elseif (is_array($acl)) {
            $h = new Core_Model_Acl_User(array('data' => $acl));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $acl
     * @throws InvalidArgumentException 
     */
    public function update($acl) {
        if ($acl instanceof Core_Model_Acl_User_Interface) {
            $h = $acl;
        } elseif (is_array($acl)) {
            $h = new Core_Model_Acl_User(array('data' => $acl));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $acl
     * @throws InvalidArgumentException 
     */
    public function delete($acl) {
        if (is_int($acl)) {
            $h = new Core_Model_Acl_User();
            $h->id = $acl;
        } elseif ($acl instanceof Core_Model_Acl_User_Interface) {
            $h = $acl;
        } elseif (is_array($acl)) {
            $h = new Core_Model_Acl_User(array('data' => $acl));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }

        return $this->getMapper()->delete($h);
    }
public function deleteByUser($user) {
        $this->getMapper()->deleteByUser($user);
    }
    
     public function deleteAll(){
        if(APPLICATION_ENV != 'testing'){
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }
}