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
 * @subpackage Field
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_Field_Service {

    /**
     * @var Content_Model_Field_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Content_Model_Field_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_Field_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Content_Model_Field_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return Content_Model_Field_Interface 
     */
    public function getObjectById($id) {
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Object not found', 404);
        }
        return $result;
    }
    
    /**
     * @param int $id
     * @return Content_Model_Field_Interface 
     */
    public function getObjectBySectionName($section,$name) {
        $result = $this->getMapper()->fetchObjectBySectionName($section,$name);

        if (empty($result)) {
            throw new NotFoundException('Object not found', 404);
        }
        return $result;
    }

    /**
     * @param int $id
     * @return Content_Model_Field_Interface[]
     */
    public function getObjectsBySection($section) {
        $contents = $this->getMapper()->fetchObjectsBySection($section);

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $contents;
    }

//    public function getObjectsByIdHouse($id,$house){
//        $apikeys = $this->getMapper()->fetchObjectsByIdHouse($id,$house);
//
//        if (empty($apikeys)) {
//            return array();
//            //throw new Content_Model_Exception('Apikey not found', 404);
//        }
//        return $apikeys;
//    }

    /**
     * @param mixed $object
     * @throws InvalidArgumentException 
     */
    public function create($object) {
        if ($object instanceof Content_Model_Field_Interface) {
            $h = $object;
        } elseif (is_array($object)) {
            $h = new Content_Model_Field(array('data' => $object));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }
        $field = $this->getMapper()->save($h);
        
        if($field->type != Content_Model_Field::SYSTEM){
            $service = new Content_Model_Content_Service();
            $service->addCustomField($field);
        }

        return $field;
    }

    /**
     * @param mixed $object
     * @throws InvalidArgumentException 
     */
    public function update($object) {
        if ($object instanceof Content_Model_Field_Interface) {
            $h = $object;
        } elseif (is_array($object)) {
            $h = new Content_Model_Field(array('data' => $object));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }
        
        $found = null;
        
        try {
            $found = $this->getObjectBySectionName($h->section,$h->name);
        } catch(NotFoundException $e){
            
        }
        
        if(!is_null($found)){
            throw new DuplicateEntryException('Name already exists ');
        }
        
        $old = $this->getObjectById($h->id);
        
        
        $field = $this->getMapper()->save($h);
        
        if($field->type != Content_Model_Field::SYSTEM){
            $service = new Content_Model_Content_Service();
            $service->renameCustomField($old, $field);
        }

        return $field;
    }

    /**
     * @param mixed $sectionField
     * @throws InvalidArgumentException 
     */
    public function delete($sectionField) {
        if (is_int($sectionField)) {
            
            $h = $this->getObjectById($sectionField);
            //$h = new Content_Model_Field();
            //$h->id = $sectionField;
        } elseif ($sectionField instanceof Content_Model_Field_Interface) {
            $h = $sectionField;
        } elseif (is_array($sectionField)) {
            $h = new Content_Model_Field(array('data' => $sectionField));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }
        
        if($h->type != Content_Model_Field::SYSTEM){
            $service = new Content_Model_Content_Service();
            //die('remove field');
            $service->removeCustomField($h);
        }

        return $this->getMapper()->delete($h);
    }
    
    public function deleteBySection($section) {
        return $this->getMapper()->deleteBySection($section);
    }
    
     public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getMapper()->deleteAll();
            return;
        }
        throw new Exception("Not Allowed");
    }

}