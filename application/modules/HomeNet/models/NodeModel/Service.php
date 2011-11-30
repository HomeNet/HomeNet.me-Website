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
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * @package HomeNet
 * @subpackage NodeModel
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_NodeModel_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_NodesMapperInterface  
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_NodesMapper_Interface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_NodeModel_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_NodeModel_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_NodeModel_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * Get NodeModel by id
     * 
     * @param int $id
     * @return HomeNet_Model_NodeModel (HomeNet_Model_NodeModel_Interface)
     * @throw InvalidArgumentException
     * @throw NotFoundException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('NodeModel: '.$id.' Not Found', 404);
        }
        return $result;
    }

    /**
     * Get all NodelModels
     * 
     * @return HomeNet_Model_NodeModel (HomeNet_Model_NodeModel_Interface) 
     */
    public function getObjects() {
        $results = $this->getMapper()->fetchObjects();
        return $results;
    }

    /**
     * Get NodelModel by status
     * 
     * @param integer $status
     * @return HomeNet_Model_NodeModel (HomeNet_Model_NodeModel_Interface)
     * @throw InvalidArgumentException
     */
    public function getObjectsByStatus($status) {
        if (empty($status)  || !is_numeric($status)) {
            throw new InvalidArgumentException('Invalid Status');
        }
        return $this->getMapper()->fetchObjectsByStatus($status);
    }
    
     /**
     * Create a new NodeModel
     * 
     * @param HomeNet_Model_Message_Interface|array $mixed
     * @return HomeNet_Model_Message (HomeNet_Model_NodeModel_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_NodeModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_NodeModel(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid NodeModel');
        }

        return $this->getMapper()->save($object);
    }
    
    /**
     * Update an existing NodeModel
     * 
     * @param HomeNet_Model_NodeModel_Interface|array $mixed
     * @return HomeNet_Model_NodeModel (HomeNet_Model_NodeModel_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_NodeModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_NodeModel(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid NodeModel');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Delete a NodeModel
     * 
     * @param HomeNet_Model_NodeModel_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if ($mixed instanceof HomeNet_Model_NodeModel_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_NodeModel(array('data' => $mixed));
        } elseif (is_numeric($mixed)) {
            $object = $this->getObjectbyId((int) $mixed);
        } else {
            throw new InvalidArgumentException('Invalid NodeModel');
        }

        return $this->getMapper()->delete($object);
    }

    /**
     * Delete all NodeModel. Used for unit testing/Will not work in production 
     *
     * @return boolean Success
     * @throws NotAllowedException
     */
    public function deleteAll() {
        if (APPLICATION_ENV == 'production') {
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }


}