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
     */
    public function getObjectById($id) {
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Membership not found', 404);
        }
        return $result;
    }

    public function getGroupsByUser($user){
        $result = $this->getMapper()->fetchGroupsByUser($user);

        if (empty($result)) {
            throw new NotFOundException('No Memberships Found', 404);
        }
        return $result;
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
     * @param mixed $membership
     * @throws InvalidArgumentException 
     */
    public function create($membership) {
        if ($membership instanceof Core_Model_User_Membership_Interface) {
            $h = $membership;
        } elseif (is_array($membership)) {
            $h = new Core_Model_User_Membership(array('data' => $membership));
        } else {
            throw new InvalidArgumentException('Invalid Membership');
        }

        $result = $this->getMapper()->save($h);
        
        $gService = new Core_Model_Group_Service();
        $gService->incrementGroupCount($result->group,1);
        
        return $result;
    }

//    /**
//     * @param mixed $membership
//     * @throws InvalidArgumentException 
//     */
//    public function update($membership) {
//        if ($membership instanceof Core_Model_User_Membership_Interface) {
//            $h = $membership;
//        } elseif (is_array($membership)) {
//            $h = new Core_Model_User_Membership(array('data' => $membership));
//        } else {
//            throw new InvalidArgumentException('Invalid Membership');
//        }
//
//        return $this->getMapper()->save($h);
//    }

    /**
     * @param mixed $membership
     * @throws InvalidArgumentException 
     */
    public function delete($membership) {
        if (is_int($membership)) {
            $h = $this->getObjectById($membership);
        } elseif ($membership instanceof Core_Model_User_Membership_Interface) {
            $h = $membership;
        } elseif (is_array($membership)) {
            $h = new Core_Model_User_Membership(array('data' => $membership));
        } else {
            throw new InvalidArgumentException('Invalid Membership');
        }
        
        if(empty($h->group)){
            throw new InvalidArgumentException('Missing Group ID');
        }
        
        $group = $h->group;
        
        $result = $this->getMapper()->delete($h);
        
        $gService = new Core_Model_Group_Service();
        $gService->decrementGroupCount($group,1);
        
        return $result;
    }
    
    public function deleteByUser($user) {
        $affectedRows = $this->getMapper()->deleteByUser($user);
        
        $gService = new Core_Model_Group_Service();
        $gService->decrementGroupCount($membership->group,$affectedRows);
        
        return $affectedRows;
    }
    
    public function deleteByGroup($group) {
        $affectedRows = $this->getMapper()->deleteByGroup($group);
        
        $gService = new Core_Model_Group_Service();
        $gService->updateGroupCount($membership->group,0);
        
        return $affectedRows;
        
    }
    
     public function deleteAll(){
        if(APPLICATION_ENV == 'testing'){
            $this->getMapper()->deleteAll();
            return;
        }
        throw new Exception("Not Allowed");
    }

}