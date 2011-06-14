<?php

/*
 * NodeService.php
 *
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
     * @var HomeNet_Model_NodesMapperInterface  
     */
    protected $_mapper;

    /**
     * @return HomeNet_Model_NodesMapper_Interface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_NodeModel_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_NodeModel_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return HomeNet_Model_NodeModel_Interface
     */
    public function getObjectById($id) {
        $nodeModel = $this->getMapper()->fetchNodeModelById($id);

        if (empty($nodeModel)) {
            throw new HomeNet_Model_Exception('NodeModel not found', 404);
        }
        return $nodeModel;
    }

    public function getObjects() {
        $nodeModels = $this->getMapper()->fetchNodeModels();

        if (empty($nodeModels)) {
            throw new HomeNet_Model_Exception('House not found', 404);
        }
        return $nodeModels;
    }

    public function getObjectsByStatus($status) {
        $nodeModels = $this->getMapper()->fetchNodeModelsByStatus($status);

        if (empty($nodeModels)) {
            throw new HomeNet_Model_Exception('Node not found', 404);
        }
        return $nodeModels;
    }

    /**
     * @param mixed $nodeModel
     * @return HomeNet_Model_NodeModel_Interface
     */
    public function create($nodeModel) {
        if ($nodeModel instanceof HomeNet_Model_NodeModel_Interface) {
            $h = $nodeModel;
        } elseif (is_array($nodeModel)) {
            $h = new HomeNet_Model_NodeModel(array('data' => $nodeModel));
        } else {
            throw new HomeNet_Model_Exception('Invalid Node');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $nodeModel
     * @return HomeNet_Model_NodeModel_Interface
     */
    public function update($nodeModel) {
        if ($nodeModel instanceof HomeNet_Model_NodeModel_Interface) {
            $h = $nodeModel;
        } elseif (is_array($nodeModel)) {
            $h = new HomeNet_Model_NodeModel(array('data' => $nodeModel));
        } else {
            throw new HomeNet_Model_Exception('Invalid Node');
        }
        return $this->getMapper()->save($h);
    }

    public function delete($nodeModel) {
        if (is_int($nodeModel)) {
            $h = new HomeNet_Model_NodeModel();
            $h->id = $nodeModel;
        } elseif ($nodeModel instanceof HomeNet_Model_NodeModel_Interface) {
            $h = $nodeModel;
        } elseif (is_array($nodeModel)) {
            $h = new HomeNet_Model_Node(array('data' => $nodeModel));
        } else {
            throw new HomeNet_Model_Exception('Invalid Node');
        }

        return $this->getMapper()->delete($nodeModel);
    }

}