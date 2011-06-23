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
 * @subpackage CategorySet
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */

class Core_Model_CategorySet_Service {
    
    /**
     * @var Core_Model_CategorySet_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Core_Model_CategorySet_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Core_Model_CategorySet_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Core_Model_CategorySet_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    /**
     * @param int $id
     * @return Core_Model_CategorySet_Interface 
     */
    public function getObjectById($id){
        $content = $this->getMapper()->fetchObjectById($id);

        if (empty($content)) {
            throw new NotFoundException('Category Set Not Found', 404);
        }
        return $content;
    }

    public function create($categorySet) {
        if ($categorySet instanceof Core_Model_CategorySet_Interface) {
            $h = $categorySet;
        } elseif (is_array($categorySet)) {
            $h = new Core_Model_CategorySet(array('data' => $categorySet));
        } else {
            throw new InvalidArgumentException('Invalid Category Set');
        }

        return $this->getMapper()->save($h);
    }

    public function update($categorySet) {
        if ($categorySet instanceof Core_Model_CategorySet_Interface) {
            $h = $categorySet;
        } elseif (is_array($categorySet)) {
            $h = new Core_Model_CategorySet(array('data' => $categorySet));
        } else {
            throw new InvalidArgumentException('Invalid Category Set');
        }
        
        return $this->getMapper()->save($h);
    }

    public function delete($categorySet) {
        if (is_int($categorySet)) {
            $h = new Core_Model_CategorySet();
            $h->id = $categorySet;
        } elseif ($categorySet instanceof Core_Model_CategorySet_Interface) {
            $h = $categorySet;
        } elseif (is_array($categorySet)) {
            $h = new Core_Model_CategorySet(array('data' => $categorySet));
        } else {
            throw new InvalidArgumentException('Invalid Category Set');
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