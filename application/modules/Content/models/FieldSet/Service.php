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
     * @param int $id
     * @return Content_Model_FieldSet_Interface 
     */
    public function getObjects(){
        $fieldSets = $this->getMapper()->fetchObjects();

       // if (empty($fieldSets)) {
       //     throw new NotFoundException('Field Sets Not Found', 404);
       // }
        return $fieldSets;
    }
    
    /**
     * @param int $id
     * @return Content_Model_FieldSet_Interface 
     */
    public function getObjectById($id){
        $content = $this->getMapper()->fetchObjectById($id);

        if (empty($content)) {
            throw new NotFoundException('Field Set Not Found', 404);
        }
        return $content;
    }

    public function create($fieldSet) {
        if ($fieldSet instanceof Content_Model_FieldSet_Interface) {
            $h = $fieldSet;
        } elseif (is_array($fieldSet)) {
            $h = new Content_Model_FieldSet(array('data' => $fieldSet));
        } else {
            throw new InvalidArgumentException('Invalid Field Set');
        }

        return $this->getMapper()->save($h);
    }

    public function update($fieldSet) {
        if ($fieldSet instanceof Content_Model_FieldSet_Interface) {
            $h = $fieldSet;
        } elseif (is_array($fieldSet)) {
            $h = new Content_Model_FieldSet(array('data' => $fieldSet));
        } else {
            throw new InvalidArgumentException('Invalid Field Set');
        }
        
        return $this->getMapper()->save($h);
    }

    public function delete($fieldSet) {
        if (is_int($fieldSet)) {
            $h = new Content_Model_FieldSet();
            $h->id = $fieldSet;
        } elseif ($fieldSet instanceof Content_Model_FieldSet_Interface) {
            $h = $fieldSet;
        } elseif (is_array($fieldSet)) {
            $h = new Content_Model_FieldSet(array('data' => $fieldSet));
        } else {
            throw new InvalidArgumentException('Invalid Field Set');
        }

        return $this->getMapper()->delete($h);
    }
    
    public function deleteBySection($section) {
        return $this->getMapper()->deleteBySection($section);
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV != 'testing'){
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }
}