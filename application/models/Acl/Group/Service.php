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

    public function setMapper(Core_Model_Acl_Group_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
      /**
     * @param int $id
     * @return Core_Model_Acl_Group_Interface 
     */
    public function getObjectById($id) {
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('User Acl not found', 404);
        }
        return $result;
    }
    
    public function getObjectsByGroup($group){
         return $this->getMapper()->fetchObjectsByGroup($group); 
    }
    
    public function getObjectsByGroups(array $groups){
      return $this->getMapper()->fetchObjectsByGroups($groups); 
    }
    
    public function getObjectsByGroupsModule(array $groups, $module) {
        return $this->getMapper()->fetchObjectsByGroupsModule($groups, $module); 
    }
    
    public function getObjectsByGroupsModuleObject(array $groups, $module, $object = null){
        $rows = $this->getMapper()->fetchObjectsByGroupsModuleObject($groups, $module, $object);
        $array = array();
        foreach($rows as $row){
            $array[$row->group][] = $row;
        }

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $array;
    }
    
    public function getObjectsByGroupsModuleControllerObjects(array $groups, $module, $controllers, array $objects){
        $rows = $this->getMapper()->fetchObjectsByGroupsModuleControllerObjects($groups, $module, $controllers, $objects);
        $array = array();
        foreach($rows as $row){
            $array[$row->group][] = $row;
        }

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $array;
    }
    
    
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
        if ($acl instanceof Core_Model_Acl_Group_Interface) {
            $h = $acl;
        } elseif (is_array($acl)) {
            $h = new Core_Model_Acl_Group(array('data' => $acl));
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
        if ($acl instanceof Core_Model_Acl_Group_Interface) {
            $h = $acl;
        } elseif (is_array($acl)) {
            $h = new Core_Model_Acl_Group(array('data' => $acl));
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
            $h = new Core_Model_Acl_Group();
            $h->id = $acl;
        } elseif ($acl instanceof Core_Model_Acl_Group_Interface) {
            $h = $acl;
        } elseif (is_array($acl)) {
            $h = new Core_Model_Acl_Group(array('data' => $acl));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }

        return $this->getMapper()->delete($h);
    }
    
    public function deleteByGroup($group) {
        $this->getMapper()->deleteByGroup($group);
    }
    
     public function deleteAll(){
        if(APPLICATION_ENV != 'testing'){
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }

}