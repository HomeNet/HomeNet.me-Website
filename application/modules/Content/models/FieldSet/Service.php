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
 * @package Content
 * @subpackage FieldSet
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_FieldSet_Service {

    /**
     * @var Content_Model_FieldSet_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Content_Model_FieldSet_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_FieldSet_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Content_Model_FieldSet_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @return Content_Model_FieldSet[]
     */
    public function getObjects() {
        $fieldSets = $this->getMapper()->fetchObjects();

        // if (empty($fieldSets)) {
        //     throw new NotFoundException('Field Sets Not Found', 404);
        // }
        return $fieldSets;
    }

    /**
     * @param int $id FieldSet Id
     * @return Content_Model_FieldSet
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        $content = $this->getMapper()->fetchObjectById($id);

        if (empty($content)) {
            throw new NotFoundException('Field Set Not Found', 404);
        }
        return $content;
    }

    /**
     * @param int $id Section Id
     * @return Content_Model_FieldSet
     * @throws NotFoundException
     */
    public function getObjectsBySection($id) {
        $results = $this->getMapper()->fetchObjectsBySection($id);

        if (empty($results)) {
            throw new NotFoundException('Field Set Not Found', 404);
        }
        return $results;
    }
    
    public function getObjectsBySectionWithFields($id) {
        
        //@todo create a array collection class for fieldsets with a function that loads fields
 
        $sets = $this->getObjectsBySection($id);
        $objects = array();
        foreach($sets as $value){
            $objects[$value->id] = $value;
        }
        
        $service = new Content_Model_Field_Service();
        $fields = $service->getObjectsBySection($id);
        
       // $fields = (object)$fields->toArray();
        
        foreach($fields as $value){
            if(!array_key_exists($value->set, $objects)){
                continue;
            }
            $objects[$value->set]->fields[] = (object)$value->toArray();
        }
        return $objects;
    }
    
    public function setObjectOrder($mixed,$order){
        
        if ($mixed instanceof Content_Model_Field_Interface) {
            $object = $mixed;  
        } elseif (is_numeric($mixed)) {
            $object = $this->getObjectById($mixed);
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Field(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }
        
        $results = $this->getMapper()->setObjectOrder($object, $order);
        return $results;
    }

    /**
     * @param Content_Model_FieldSet_Interface|array  $mixed
     * @return Content_Model_FieldSet 
     * @throws InvalidArgumentException
     */
    public function create($mixed) {
        if ($mixed instanceof Content_Model_FieldSet_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_FieldSet(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Field Set');
        }
        
        $result = $this->getMapper()->save($object);;
        
        $this->getMapper()->shiftOrderBySection($result->section, null, $result->order, $result->id);

        return $result;
    }

    /**
     * @param Content_Model_FieldSet_Interface|array  $mixed
     * @return Content_Model_FieldSet 
     * @throws InvalidArgumentException
     */
    public function update($mixed) {
        if ($mixed instanceof Content_Model_FieldSet_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_FieldSet(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Field Set');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param Content_Model_FieldSet_Interface|array|int  $mixed
     * @return bool Success
     * @throws InvalidArgumentException
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = $this->getObjectById($mixed);
        } elseif ($mixed instanceof Content_Model_FieldSet_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_FieldSet(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Field Set');
        }
        //@todo get count using count, this is lazy
        $count = $this->getObjectsBySection($object->section);
        if(count($count) == 1){
            throw new Exception('Can not Delete Last Field Set');
        }
        $section = $object->section;
        $order = $object->order;
     
        $result = $this->getMapper()->delete($object);
        if($result){
            $this->getMapper()->shiftOrderBySection($section, $order);
        }

        return $result;
    }

    /**
     * @param type $section Section Id 
     * @return boolean      Success 
     */
    public function deleteBySection($section) {
        return $this->getMapper()->deleteBySection($section);
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