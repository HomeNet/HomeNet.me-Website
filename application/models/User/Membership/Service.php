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
 * @subpackage User_Membership
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_User_Membership_Service {

    /**
     * @var Core_Model_Membership_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Core_Model_Membership_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Core_Model_User_Membership_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Core_Model_User_Membership_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return Core_Model_Group_Interface 
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        if($id === null && !is_numeric($id)){
            throw new InvalidArgumentException('Invalid Id');
        }
        
        $result = $this->getMapper()->fetchObjectById((int) $id);

        if (empty($result)) {
            throw new NotFoundException('Id: '.$id.' Not Found');
        }
        return $result;
    }

    /**
     * @param int $user user id 
     * @throws NotFoundException
     */
    public function getGroupIdsByUser($user) {
        if($user === null && !is_numeric($user)){
            throw new InvalidArgumentException('Invalid user');
        }
           
        $result = $this->getMapper()->fetchGroupIdsByUser((int) $user);

        if (empty($result)) {
            throw new NotFoundException('User: '.$user.' Not Found');
        }
        return $result;
    }
    
     /**
     * @param int $user user id 
     * @throws NotFoundException
     */
    public function getObjectByUserGroup($user, $group) {
        if($user === null && !is_numeric($user)){
            throw new InvalidArgumentException('Invalid user');
        }
        if($group === null && !is_numeric($group)){
            throw new InvalidArgumentException('Invalid group');
        }
        $result = $this->getMapper()->fetchObjectByUserGroup((int) $user, (int) $group);

        if (empty($result)) {
            throw new NotFoundException('User: '.$user.', Group: '.$group.' Not Found');
        }
        return $result;
    }
    
    /**
     *
     * @param int $user User Id
     * @return type 
     */
    public function getObjectsByUser($user) {
        if($user === null && !is_numeric($user)){
            throw new InvalidArgumentException('Invalid user');
        }
        return $this->getMapper()->fetchObjectsByUser($user);
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
    
    public function add($user, $group){
        if($user === null && !is_numeric($user)){
            throw new InvalidArgumentException('Invalid user');
        }
        if($group === null && !is_numeric($group)){
            throw new InvalidArgumentException('Invalid group');
        }
        $object = new Core_Model_User_Membership();
        $object->user = (int) $user;
        $object->group = (int) $group;
        return $this->create($object);
        
    }

    /**
     * @param Core_Model_User_Membership_Interface|array $mixed
     * @return Core_Model_User_Membership
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof Core_Model_User_Membership_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_User_Membership(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        $result = $this->getMapper()->save($object);

        $gService = new Core_Model_Group_Service();
        $gService->incrementGroupCount($result->group, 1);

        return $result;
    }

//    /**
//     * @param Core_Model_User_Membership_Interface|array $mixed
//     * @return Core_Model_User_Membership
//     * @throws InvalidArgumentException 
//     */
//    public function update($mixed) {
//        if ($mixed instanceof Core_Model_User_Membership_Interface) {
//            $object = $mixed;
//        } elseif (is_array($mixed)) {
//            $object = new Core_Model_User_Membership(array('data' => $mixed));
//        } else {
//            throw new InvalidArgumentException('Invalid Membership');
//        }
//        
//        
//
//        return $this->getMapper()->save($object);
//    }

    /**
     * @param Core_Model_User_Membership_Interface|array|int $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = $this->getObjectById($mixed);
        } elseif ($mixed instanceof Core_Model_User_Membership_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_User_Membership(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        if (empty($object->group)) {
            throw new InvalidArgumentException('Missing Group ID');
        }

        $group = $object->group;

        $result = $this->getMapper()->delete($object);

        $gService = new Core_Model_Group_Service();
        $gService->decrementGroupCount($group, 1);

        return $result;
    }

    /**
     * @param integer $user User Id
     * @return boolean Success 
     */
    public function deleteByUser($user) {
        
        if($user === null && !is_numeric($user)){
            throw new InvalidArgumentException('Invalid user');
        }
        
        $objects = $this->getObjectsByUser($user);
        
        $gService = new Core_Model_Group_Service();
        
        foreach($objects as $object){
            $gService->decrementGroupCount($object->group);
        }
        
        $affectedRows = $this->getMapper()->deleteByUser($user);

        return $affectedRows;
    }
    
     public function deleteByUserGroup($user, $group) {
         
         if($user === null && !is_numeric($user)){
            throw new InvalidArgumentException('Invalid user');
        }
        if($group === null && !is_numeric($group)){
            throw new InvalidArgumentException('Invalid group');
        }
        
        $affectedRows = $this->getMapper()->deleteByUserGroup($user, $group);
        
        $gService = new Core_Model_Group_Service();
        
        $gService->decrementGroupCount($group);
      
        return $affectedRows;
    }
    

    /**
     * @param Integer $group   Group Id
     * @return boolean Success 
     */
    public function deleteByGroup($group) {
        
        if($group === null && !is_numeric($group)){
            throw new InvalidArgumentException('Invalid group');
        }
        
        $affectedRows = $this->getMapper()->deleteByGroup($group);

        $gService = new Core_Model_Group_Service();
        $gService->updateGroupCount($group, 0);

        return $affectedRows;
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