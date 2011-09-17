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
 * @subpackage Route
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */

class Core_Model_Route_Service {
    
    /**
     * @var Core_Model_Route_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Core_Model_Route_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Core_Model_Route_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Core_Model_Route_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
    /**
     * @param int $id
     * @return Core_Model_Route_Interface 
     */
    public function getObjects(){
        $objects = $this->getMapper()->fetchObjects();

        return $objects;
    }
    
    /**
     * @param int $id
     * @return Core_Model_Route_Interface 
     */
    public function getObjectById($id){
        $router = $this->getMapper()->fetchObjectById($id);

        if (empty($router)) {
            throw new NotFoundException('Route Not Found', 404);
        }
        return $router;
    }

   /**
    * @param mixed $router
    * @throws InvalidArgumentException 
    */
    public function create($router) {
        if ($router instanceof Core_Model_Route_Interface) {
            $h = $router;
        } elseif (is_array($router)) {
            $h = new Core_Model_Route(array('data' => $router));
        } else {
            throw new InvalidArgumentException('Invalid Route');
        }
        return $this->getMapper()->save($h);
    }
    
  /**
    * @param mixed $router
    * @throws InvalidArgumentException 
    */
    public function update($router) {
        if ($router instanceof Core_Model_Route_Interface) {
            $h = $router;
        } elseif (is_array($router)) {
            $h = new Core_Model_Route(array('data' => $router));
        } else {
            throw new InvalidArgumentException('Invalid Route');
        }
        
        return $this->getMapper()->save($h);
    }
  /**
    * @param mixed $router
    * @throws InvalidArgumentException 
    */
    public function delete($router) {
        if (is_int($router)) {
            $h = new Core_Model_Route();
            $h->id = $router;
        } elseif ($router instanceof Core_Model_Route_Interface) {
            $h = $router;
        } elseif (is_array($router)) {
            $h = new Core_Model_Route(array('data' => $router));
        } else {
            throw new InvalidArgumentException('Invalid Route');
        }
        
        return $this->getMapper()->delete($h);
    }
    
    public function deleteAll(){
        if(APPLICATION_ENV == 'production'){
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }
}