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
 * @subpackage Menu
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */

class Core_Model_Menu_Service {
    
    /**
     * @var Core_Model_Menu_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Core_Model_Menu_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Core_Model_Menu_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Core_Model_Menu_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
    /**
     * @param int $id
     * @return Core_Model_Menu_Interface 
     */
    public function getObjects(){
        $objects = $this->getMapper()->fetchObjects();

       // if (empty($objects)) {
       //     throw new NotFoundException('Category Sets Not Found', 404);
       // }
        return $objects;
    }
    
    /**
     * @param int $id
     * @return Core_Model_Menu_Interface 
     */
    public function getObjectById($id){
        $object = $this->getMapper()->fetchObjectById($id);

        if (empty($object)) {
            throw new NotFoundException('Category Set Not Found', 404);
        }
        return $object;
    }

    public function create($mixed) {
        if ($mixed instanceof Core_Model_Menu_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Menu(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Category Set');
        }

        return $this->getMapper()->save($object);
    }

    public function update($mixed) {
        if ($mixed instanceof Core_Model_Menu_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Menu(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Category Set');
        }
        
        return $this->getMapper()->save($object);
    }

    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new Core_Model_Menu();
            $object->id = $mixed;
        } elseif ($mixed instanceof Core_Model_Menu_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Core_Model_Menu(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Category Set');
        }

        return $this->getMapper()->delete($object);
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV == 'production'){
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }
}