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

    /**
     * @param Content_Model_Section_MapperInterface $mapper 
     */
    public function setMapper(Core_Model_Group_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
    /**
     * @return int number of items in table
     */
    public function getCount() {
        $result = $this->getMapper()->fetchCount();

//        if (empty($result)) {
//            throw new NotFoundException('Id: '.$id.' Not Found', 404);
//        }
        return $result;
    }
 
    /**
     * @return Core_Model_Group[]
     */
    public function getObjects() {
        $result = $this->getMapper()->fetchObjects();

//        if (empty($result)) {
//            throw new NotFoundException('Id: '.$id.' Not Found', 404);
//        }
        return $result;
    }
    
    public function getList(){
        $objects = $this->getObjects();
        $array = array();
        foreach($objects as $object){
            $array[$object->id] = $object->title;
        }
        return $array;
    }
    
    

    /**
     * @param int $id   Group Id
     * @return Core_Model_Group
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
     * @param integer $id Group Id
     * @param integer $amount Amount to increment by
     * @return integer affected rows  
     * @throws NotFoundException
     */
    public function incrementGroupCount($id, $amount = 1) {
        $affectedRows = $this->getMapper()->incrementUserCount($id, $amount);

        if ($affectedRows == 0) {
            throw new NotFoundException('Id: '.$id.' Not Found', 404);
        }
        return $affectedRows;
    }

    /**
     * @param integer $id Group Id
     * @param integer $amount Amount to increment by
     * @return integer affected rows  
     * @throws NotFoundException
     */
    public function decrementGroupCount($id, $amount = 1) {
        $affectedRows = $this->getMapper()->incrementUserCount($id, $amount * -1);

        if ($affectedRows == 0) {
            throw new NotFoundException('Id: '.$id.' Not Found', 404);
        }
        return $affectedRows;
    }

    /**
     * @param integer $id Group Id
     * @param integer $amount Set Group Count
     * @return integer affected rows 
     * @throws NotFoundException
     */
    public function updateGroupCount($id, $amount) {
        $affectedRows = $this->getMapper()->updateUserCount($id, $amount);

        if ($affectedRows == 0) {
            throw new NotFoundException('Id: '.$id.' Not Found', 404);
        }

        return $affectedRows;
    }

    /**
     * @param integer $type Group Type
     * @return Core_Model_Group[] 
     */
    public function getObjectsByType($type) {
        $results = $this->getMapper()->fetchObjectsByType($type);

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $results;
    }

    /**
     * @param Core_Model_Group_Interface|array $mixed
     * @return Core_Model_Group
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof Core_Model_Group_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Group(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param Core_Model_Group_Interface|array $mixed
     * @return Core_Model_Group
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof Core_Model_Group_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Group(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param Core_Model_Group_Interface|array $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new Core_Model_Group();
            $object->id = $mixed;
        } elseif ($mixed instanceof Core_Model_Group_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Group(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
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