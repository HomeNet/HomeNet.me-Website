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
            $this->_mapper = new Content_Model_Template_MapperCache();
            $this->_mapper->setMapper(new Content_Model_Template_MapperDbTable());
        }

        return $this->_mapper;
    }
/**
     * @param Content_Model_Section_MapperInterface $mapper 
     */
    public function setMapper(Content_Model_Template_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return Content_Model_Template_Interface 
     * @throws NotFoundException
     */
    public function getObjectByIdRevision($id,$revision) {
        $result = $this->getMapper()->fetchObjectByIdRevision($id, $revision);

        if (empty($result)) {
            throw new NotFoundException('Id: ' . $id . ', Revision: '.$revision.' Not Found', 404);
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
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Id: ' . $id . ' Not Found', 404);
        }
        return $result;
    }
    
    /**
     * @param string $url
     * @return Content_Model_Template_Interface 
     * @throws NotFoundException
     */
    public function getObjectByUrl($url) {
        $result = $this->getMapper()->fetchObjectByUrl($url);

        if (empty($result)) {
            throw new NotFoundException('URL: ' . $url . ' Not Found', 404);
        }
        return $result;
    }
    
    /**
     * @param string $url
     * @return string
     * @throws NotFoundException
     */
    public function getPathBySectionTemplate($section, $template) {
        
        if(!is_numeric($section)){
            //@todo cache a lookuparray to make this faster
            $service = new Content_Model_Section_Service();
            $object = $service->getObjectByUrl($section);
            $section = $object->id;
        }

        $path = $this->getMapper()->fetchPathBySection($section);

        if (empty($path)) {
            throw new NotFoundException('Section not found: ' . $section . ' Not Found', 404);
        }
        
        if (!file_exists($path .'/'. $template.'.phtml')) {
            throw new Exception("Template: $template is not in the cache");
        }
        
        return $path;
    }
    
    public function getTemplate($template) {
        return $template.'.phtml';
    }

    

    

    /**
     * @param Content_Model_Template_Interface|array $mixed
     * @return Content_Model_Template
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof Content_Model_Template_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Template(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }
        //create cache
        $object->active = true;
        
        if($object->layout == ''){
            $object->layout = null;
        }
        //@todo test to see if url aleady exists
        //die(debugArray($h));
        return $this->getMapper()->save($object);
    }

    /**
     * @param Content_Model_Template_Interface|array $mixed
     * @return Content_Model_Template
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof Content_Model_Template_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Template(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }
        
        if($object->layout == ''){
            $object->layout = null;
        }
        //create cache
        $object->active = true;

        return $this->getMapper()->save($object);
    }

    /**
     * @param Content_Model_Template_Interface|array $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new Content_Model_Template();
            $object->id = $mixed;
        } elseif ($mixed instanceof Content_Model_Template_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Template(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }
        
        //delete cache
        //@todo delete cache

        return $this->getMapper()->delete($object);
    }
    
    /**
     * @param integer $id Content Template Id 
     * @return boolean Success
     */
    public function deleteById($id) {
        $this->getMapper()->deleteById($id);
    }
        /**
     * @param integer $section Content Section Id 
     * @return boolean Success
     */
    public function deleteBySection($section) {
        $this->getMapper()->deleteBySection($section);
    }
     /**
     * Delete all data. Used for unit testing/Will not work in production 
     *
     * @return boolean Success
     * @throws NotAllowedException
     */
     public function deleteAll(){
        if(APPLICATION_ENV != 'production'){
            $this->getMapper()->deleteAll();
            return;
        }
        throw new Exception("Not Allowed");
    }

}