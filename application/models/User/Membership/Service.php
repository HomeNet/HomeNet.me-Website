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
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Id: '.$id.' Not Found', 404);
        }
        return $result;
    }

    /**
     * @param Content_Model_Section_MapperInterface $mapper 
     * @throws NotFoundException
     */
    public function getGroupIdsByUser($user) {
        $result = $this->getMapper()->fetchGroupIdsByUser($user);

        if (empty($result)) {
            throw new NotFOundException('Id: '.$id.' Not Found', 404);
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
        $affectedRows = $this->getMapper()->deleteByUser($user);

        $gService = new Core_Model_Group_Service();
        $gService->decrementGroupCount($membership->group, $affectedRows);

        return $affectedRows;
    }

    /**
     * @param Integer $group   Group Id
     * @return boolean Success 
     */
    public function deleteByGroup($group) {
        $affectedRows = $this->getMapper()->deleteByGroup($group);

        $gService = new Core_Model_Group_Service();
        $gService->updateGroupCount($membership->group, 0);

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