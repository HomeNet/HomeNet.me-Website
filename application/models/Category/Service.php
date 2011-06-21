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
 * @subpackage Category
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Category_Service {
    
    /**
     * @var Core_Model_Category_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Core_Model_Category_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Core_Model_Category_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Core_Model_Category_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

        /**
     * @param int $id
     * @return Core_Model_Category_Interface 
     */
    public function getObjectById($id){
        
        $category = $this->getMapper()->fetchObjectById($id);

        if (empty($category)) {
            throw new Exception('Category not found', 404);
        }
        return $category;
    }
    
    /**
     * @param string $url
     * @return Core_Model_Category_Interface 
     */
    public function getObjectByUrl($url){
        
        $category = $this->getMapper()->fetchObjectByUrl($id);

        if (empty($category)) {
            throw new Exception('Category not found', 404);
        }
        return $category;
    }
/**
     * @param mixed $category
     * @throws InvalidArgumentException 
     */
    public function create($category) {
        if ($category instanceof Core_Model_Category_Interface) {
            $h = $category;
        } elseif (is_array($category)) {
            $h = new Core_Model_Category(array('data' => $category));
        } else {
            throw new InvalidArgumentException('Invalid Category');
        }

        return $this->getMapper()->save($h);
    }
/**
     * @param mixed $category
     * @throws InvalidArgumentException 
     */
    public function update($category) {
        if ($category instanceof Core_Model_Category_Interface) {
            $h = $category;
        } elseif (is_array($category)) {
            $h = new Core_Model_Category(array('data' => $category));
        } else {
            throw new InvalidArgumentException('Invalid Category');
        }
        
        return $this->getMapper()->save($h);
    }
/**
     * @param mixed $category
     * @throws InvalidArgumentException 
     */
    public function delete($category) {
        if (is_int($category)) {
            $h = new Core_Model_Category();
            $h->id = $category;
        } elseif ($category instanceof Core_Model_Category_Interface) {
            $h = $category;
        } elseif (is_array($category)) {
            $h = new Core_Model_Category(array('data' => $category));
        } else {
            throw new InvalidArgumentException('Invalid Category');
        }

        return $this->getMapper()->delete($h);
    }
}