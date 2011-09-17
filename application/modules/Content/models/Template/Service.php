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
 * @subpackage Template
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_Template_Service {

    /**
     * @var Content_Model_Template_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Content_Model_Template_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_Template_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Content_Model_Template_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return Content_Model_Template_Interface 
     */
    public function getObjectByIdRevision($id,$revision) {
        $result = $this->getMapper()->fetchObjectByIdRevision($id, $revision);

        if (empty($result)) {
            throw new NotFoundException('Content not found', 404);
        }
        return $result;
    }

    /**
     * @param int $id
     * @return Content_Model_Template_Interface[]
     */
    public function getObjectsBySection($section) {
        $results = $this->getMapper()->fetchObjectsBySection($section);

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $results;
    }
    /**
     * @param int $id
     * @return Content_Model_Template_Interface 
     */
    public function getObjectById($id) {
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Template not found', 404);
        }
        return $result;
    }
    
    /**
     * @param int $url
     * @return Content_Model_Template_Interface 
     */
    public function getObjectByUrl($url) {
        $result = $this->getMapper()->fetchObjectByUrl($url);

        if (empty($result)) {
            throw new NotFoundException('Template not found', 404);
        }
        return $result;
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
     * @param mixed $content
     * @throws InvalidArgumentException 
     */
    public function create($content) {
        if ($content instanceof Content_Model_Template_Interface) {
            $h = $content;
        } elseif (is_array($content)) {
            $h = new Content_Model_Template(array('data' => $content));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }
        //create cache
        $h->active = true;
        //@todo test to see if url aleady exists
        //die(debugArray($h));
        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $content
     * @throws InvalidArgumentException 
     */
    public function update($content) {
        if ($content instanceof Content_Model_Template_Interface) {
            $h = $content;
        } elseif (is_array($content)) {
            $h = new Content_Model_Template(array('data' => $content));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }
        
        //create cache

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $content
     * @throws InvalidArgumentException 
     */
    public function delete($content) {
        if (is_int($content)) {
            $h = new Content_Model_Template();
            $h->id = $content;
        } elseif ($content instanceof Content_Model_Template_Interface) {
            $h = $content;
        } elseif (is_array($content)) {
            $h = new Content_Model_Template(array('data' => $content));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }
        
        //delete cache

        return $this->getMapper()->delete($h);
    }
    public function deleteById($id) {
        $this->getMapper()->deleteById($id);
    }
    
    public function deleteBySection($section) {
        $this->getMapper()->deleteBySection($section);
    }
    
     public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getMapper()->deleteAll();
            return;
        }
        throw new Exception("Not Allowed");
    }

}