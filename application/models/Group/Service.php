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
 * @subpackage Group
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Group_Service {

    /**
     * @var Core_Model_Group_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Core_Model_Group_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Core_Model_Group_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Core_Model_Group_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return Core_Model_Group_Interface 
     */
    public function getObjectById($id) {
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Group not found', 404);
        }
        return $result;
    }
    
     public function incrementGroupCount($id, $amount = 1) {
        $affectedRows = $this->getMapper()->incrementUserCount($id, $amount);

        if ($affectedRows == 0) {
            throw new NotFoundException('Group not found', 404);
        }
        return $affectedRows;
    }
    
     public function decrementGroupCount($id, $amount = 1 ) {
        $affectedRows = $this->getMapper()->incrementUserCount($id, $amount * -1);

        if ($affectedRows == 0) {
            throw new NotFoundException('Group not found', 404);
        }
        return $affectedRows;
    }
    
    public function updateGroupCount($id, $amount) {
        $affectedRows = $this->getMapper()->updateUserCount($id, $amount);

        if ($affectedRows == 0) {
            throw new NotFoundException('Group not found', 404);
        }
        
        return $affectedRows;
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
     * @param mixed $group
     * @throws InvalidArgumentException 
     */
    public function create($group) {
        if ($group instanceof Core_Model_Group_Interface) {
            $h = $group;
        } elseif (is_array($group)) {
            $h = new Core_Model_Group(array('data' => $group));
        } else {
            throw new InvalidArgumentException('Invalid Group');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $group
     * @throws InvalidArgumentException 
     */
    public function update($group) {
        if ($group instanceof Core_Model_Group_Interface) {
            $h = $group;
        } elseif (is_array($group)) {
            $h = new Core_Model_Group(array('data' => $group));
        } else {
            throw new InvalidArgumentException('Invalid Group');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $group
     * @throws InvalidArgumentException 
     */
    public function delete($group) {
        if (is_int($group)) {
            $h = new Core_Model_Group();
            $h->id = $group;
        } elseif ($group instanceof Core_Model_Group_Interface) {
            $h = $group;
        } elseif (is_array($group)) {
            $h = new Core_Model_Group(array('data' => $group));
        } else {
            throw new InvalidArgumentException('Invalid Group');
        }

        return $this->getMapper()->delete($h);
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV != 'testing'){
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }

}