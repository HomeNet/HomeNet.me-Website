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
 * @subpackage CategorySet
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */

class Content_Model_CategorySet_Service {
    
    /**
     * @var Content_Model_CategorySet_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Content_Model_CategorySet_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_CategorySet_MapperDbTable();
        }

        return $this->_mapper;
    }
/**
     * @param Content_Model_Section_MapperInterface $mapper 
     */
    public function setMapper(Content_Model_CategorySet_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
    /**
     * @param int $id
     * @return Content_Model_CategorySet[]
     */
    public function getObjects(){
        $categorySets = $this->getMapper()->fetchObjects();

       // if (empty($categorySets)) {
       //     throw new NotFoundException('Category Sets Not Found', 404);
       // }
        return $categorySets;
    }
    
    /**
     * @param int $id CategorySet Id
     * @return Content_Model_CategorySet
     * @throws NotFoundException
     */
    public function getObjectById($id){
        $content = $this->getMapper()->fetchObjectById($id);

        if (empty($content)) {
            throw new NotFoundException('Id: ' . $id . ' Not Found', 404);
        }
        return $content;
    }
    /**
     * @param Content_Model_Section_Interface|array $mixed
     * @return Content_Model_Section
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof Content_Model_CategorySet_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_CategorySet(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->save($object);
    }
    /**
     * @param Content_Model_CategorySet_Interface|array $mixed
     * @return Content_Model_CategorySet
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof Content_Model_CategorySet_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_CategorySet(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }
        
        return $this->getMapper()->save($object);
    }
   /**
     * @param Content_Model_CategorySet_Interface|array $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new Content_Model_CategorySet();
            $object->id = $mixed;
        } elseif ($mixed instanceof Content_Model_CategorySet_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_CategorySet(array('data' => $mixed));
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
    public function deleteAll(){
        if(APPLICATION_ENV == 'production'){
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }
}