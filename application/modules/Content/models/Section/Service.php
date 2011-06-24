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
 * @subpackage Section
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */

class Content_Model_Section_Service {
    
    /**
     * @var Content_Model_Section_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Content_Model_Section_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_Section_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Content_Model_Section_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
    /**
     * @param int $id
     * @return Content_Model_Section_Interface 
     */
    public function getObjectById($id){
        $section = $this->getMapper()->fetchObjectById($id);

        if (empty($section)) {
            throw new NotFoundException('Section Not Found', 404);
        }
        return $section;
    }

   /**
    * @param mixed $section
    * @throws InvalidArgumentException 
    */
    public function create($section) {
        if ($section instanceof Content_Model_Section_Interface) {
            $h = $section;
        } elseif (is_array($section)) {
            $h = new Content_Model_Section(array('data' => $section));
        } else {
            throw new InvalidArgumentException('Invalid Section');
        }

        return $this->getMapper()->save($h);
    }
    
  /**
    * @param mixed $section
    * @throws InvalidArgumentException 
    */
    public function update($section) {
        if ($section instanceof Content_Model_Section_Interface) {
            $h = $section;
        } elseif (is_array($section)) {
            $h = new Content_Model_Section(array('data' => $section));
        } else {
            throw new InvalidArgumentException('Invalid Section');
        }
        
        return $this->getMapper()->save($h);
    }
  /**
    * @param mixed $section
    * @throws InvalidArgumentException 
    */
    public function delete($section) {
        if (is_int($section)) {
            $h = new Content_Model_Section();
            $h->id = $section;
        } elseif ($section instanceof Content_Model_Section_Interface) {
            $h = $section;
        } elseif (is_array($section)) {
            $h = new Content_Model_Section(array('data' => $section));
        } else {
            throw new InvalidArgumentException('Invalid Section');
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