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
class Core_Model_Content_Service {

    /**
     * @var Core_Model_Content_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Core_Model_Content_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Core_Model_Content_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Core_Model_Content_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return Core_Model_Group_Interface 
     */
    public function getObjectById($id) {
        $content = $this->getMapper()->fetchObjectById($id);

        if (empty($content)) {
            throw new Exception('Content not found', 404);
        }
        return $content;
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
        if ($group instanceof Core_Model_Content_Interface) {
            $h = $group;
        } elseif (is_array($group)) {
            $h = new Core_Model_Content(array('data' => $group));
        } else {
            throw new Core_Model_Exception('Invalid Content');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $group
     * @throws InvalidArgumentException 
     */
    public function update($group) {
        if ($group instanceof Core_Model_Content_Interface) {
            $h = $group;
        } elseif (is_array($group)) {
            $h = new Core_Model_Content(array('data' => $group));
        } else {
            throw new Core_Model_Exception('Invalid Content');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $group
     * @throws InvalidArgumentException 
     */
    public function delete($group) {
        if (is_int($group)) {
            $h = new Core_Model_Content();
            $h->id = $group;
        } elseif ($group instanceof Core_Model_Content_Interface) {
            $h = $group;
        } elseif (is_array($group)) {
            $h = new Core_Model_Content(array('data' => $group));
        } else {
            throw new Core_Model_Exception('Invalid Content');
        }

        return $this->getMapper()->delete($h);
    }

}