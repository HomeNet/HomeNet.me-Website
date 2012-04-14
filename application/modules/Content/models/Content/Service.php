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
 * @subpackage Content
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_Content_Service {

    /**
     * @var Content_Model_Content_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Content_Model_Content_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_Content_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * @param Content_Model_Content_MapperInterface $mapper 
     */
    public function setMapper(Content_Model_Content_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param integer $section  Section Id
     * @param array $fields     Fields to add
     * @return boolean          Success 
     */
    public function addCustomTable($section, $fields = array()) {

        return $this->getMapper()->addCustomTable($section);
    }

    /**
     * @param Content_Model_Field_Interface $field
     * @return boolean  Success 
     */
    public function addCustomField(Content_Model_Field_Interface $field) {
        return $this->getMapper()->addCustomField($field);
    }

    /**
     * @param Content_Model_Field_Interface $old    Old Field Object
     * @param Content_Model_Field_Interface $new    New Field Object
     * @return boolean  Success 
     */
    public function renameCustomField(Content_Model_Field_Interface $old, Content_Model_Field_Interface $new) {
        return $this->getMapper()->renameCustomField($old, $new);
    }

    /**
     * @param Content_Model_Field_Interface $field
     * @return boolean Success 
     */
    public function removeCustomField(Content_Model_Field_Interface $field) {
        return $this->getMapper()->removeCustomField($field);
    }

    /**
     * @param integer $section Section Id
     * @return boolean Success 
     */
    public function removeCustomTable($section) {
        return $this->getMapper()->removeCustomTable($section);
    }

    /**
     * @param int $id Content Id
     * @param string Content Revision
     * @return Content_Model_Content 
     * @throws NotFoundException
     */
    public function getObjectByIdRevision($id, $revision) {
        $object = $this->getMapper()->fetchObjectByIdRevision($id, $revision);

        if (empty($object)) {
            throw new NotFoundException('Content not found', 404);
        }
        return $object;
    }

    /**
     * @param int $section Section Id
     * @return Content_Model_Content[]
     */
    public function getObjectsBySection($section, $select = array()) {
        $objects = $this->getMapper()->fetchObjectsBySection($section, $select);

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $objects;
    }
    
      /**
     * @param int $category Section Id
     * @return Content_Model_Content[]
     */
    public function getObjectsBySectionCategory($section, $mixed, $select = array()) {
        
        if(!is_numeric($section) && is_string($section)){
            $sService = new Content_Model_Section_Service();
            $object = $sService->getObjectByUrl($section); 
            $s = $object->id;
        } elseif(is_numeric($section)){
            $s = (int)$section;
        } else {
            throw new InvalidArgumentException('Invalid Section: '.$section);
        }
        
        
        if(!is_numeric($mixed) && is_string($mixed)){
            $cService = new Content_Model_Category_Service();
            $object = $cService->getObjectByUrl($mixed); 
            $category = $object->id;
        } elseif(is_numeric($mixed)){
            $category = (int) $mixed;
        } else {
            throw new InvalidArgumentException('Invalid Category: '.$mixed);
        }
        
        $objects = $this->getMapper()->fetchObjectsBySectionCategory($s, $category, $select);

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $objects;
    }

    /**
     * @param int $id Content Id
     * @return Content_Model_Content
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        $Object = $this->getMapper()->fetchObjectById($id);

        if (empty($Object)) {
            throw new NotFoundException('Content not found', 404);
        }
        return $Object;
    }

    /**
     * @param int $url Content URL
     * @return Content_Model_Content
     * @throws NotFoundException
     */
    public function getObjectByUrl($url) {
        $object = $this->getMapper()->fetchObjectByUrl($url);

        if (empty($object)) {
            throw new NotFoundException('Content not found', 404);
        }
        return $object;
    }

    /**
     * @param Content_Model_Content_Interface|array $mixed
     * @return Content_Model_Content
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof Content_Model_Content_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Content(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }

        $result = $this->getMapper()->save($object);
        
        //save addtional data as required by elements
        //@todo create custom function that only pulls the elements that are saveable/deleteable

        $fields = $object->getSection()->getFields();
        foreach ($fields as $key => $value) {
            // die('called'.$key);
            $o = $object->$key;
            if ($o instanceof Content_Model_Plugin_Element) {

                $o->save($result);
            }
        }

        return $result;
    }

    /**
     * @param mixed $mixed
     * @return Content_Model_Content
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof Content_Model_Content_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Content(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }

        $result = $this->getMapper()->save($object);

        //save addtional data as required by elements
        $fields = $object->getSection()->getFields();
        foreach ($fields as $key => $value) {
            // die('called'.$key);
            $o = $object->$key;
            if ($o instanceof Content_Model_Plugin_Element) {

                $o->save($result);
            }
        }
        
       // die('Check Now');

        return $result;
    }

    /**
     * @param Content_Model_Content_Interface|array|int $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new Content_Model_Content();
            $object->id = $mixed;
        } elseif ($mixed instanceof Content_Model_Content_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Content(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }

        $fields = $object->getSection()->getFields();
        foreach ($fields as $key => $value) {
            $o = $object->$key;
            if ($o instanceof Content_Model_Plugin_Element) {
                $o->delete($object);
            }
        }

        return $this->getMapper()->delete($object);
    }

    /**
     * @param integer $section  Section Id
     * @return boolean          Success
     */
    public function deleteBySection($section) {
        return $this->getMapper()->deleteBySection($section);
    }

    /**
     *  Delete all data. Used for unit testing/Will not work in production 
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