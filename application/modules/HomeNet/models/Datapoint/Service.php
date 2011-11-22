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
 * @subpackage Datapoint
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Datapoint_Service {

    /**
     * @var HomeNet_Model_DatapointsMapperInterface
     */
    protected $_mapper;
    
    /**
     * @var string
     */
    protected $_type;

    /**
     * @return HomeNet_Model_DatapointsMapperInterface
     */
    public function getMapper() {

        if (empty($this->_type)) {
            throw new Exception('Set Type First');
        }

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Datapoint_MapperDbTable($this->_type);
        }

        return $this->_mapper;
    }

    /**
     * @param HomeNet_Model_DatapointsMapperInterface $mapper
     */
    public function setMapper(HomeNet_Model_Datapoint_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param string $type Datapoint type. Determines which table to use 
     */
    public function setType($type) {
        //@todo validate type
        $this->_type = $type;
    }

    /**
     * @param int $id
     * @return HomeNet_Model_Datapoint (HomeNet_Model_Datapoint_Interface)
     */
    public function getNewestDatapointBySubdevice($subdevice) {
        $result = $this->getMapper()->fetchNewestDatapointBySubdevice($subdevice);

        if (empty($result)) {
            // throw new HomeNet_Model_Exception('Datapoint not found', 404);
        }
        return $result;
    }

    /**
     * @param integer $subdevice  Subdevice Id
     * @param Zend_Date $start
     * @param Zend_Date $end
     * @param int $points number of points to return
     * @return HomeNet_Model_Datapoint[] (HomeNet_Model_Datapoint_Interface[]) 
     */
    public function getAveragesBySubdeviceTimespan($subdevice, Zend_Date $start, Zend_Date $end, $points = null) {
        $results = $this->getMapper()->fetchAveragesBySubdeviceTimespan($subdevice, $start, $end, $points);

        if (empty($results)) {
            // throw new HomeNet_Model_Exception('Datapoint not found', 404);
        }
        return $results;
    }

    /**
     * @param integer $subdevice
     * @param Zend_Date $start
     * @param Zend_Date $end
     * @return HomeNet_Model_Datapoint[] (HomeNet_Model_Datapoint_Interface[])  
     */
    public function getDatapointsBySubdeviceTimespan($subdevice, Zend_Date $start, Zend_Date $end) {
        $results = $this->getMapper()->fetchDatapointsBySubdeviceTimespan($subdevice, $start, $end);

        if (empty($results)) {
            //  throw new HomeNet_Model_Exception('Datapoint not found', 404);
        }
        return $results;
    }

    /**
     * @param string $type
     * @param integer $subdevice
     * @param mixed $value
     * @param type $timestamp 
     */
    public function add($type, $subdevice, $value, $timestamp) {
        $this->setType($type);
        $datapoint = new HomeNet_Model_Datapoint();
        $datapoint->subdevice = $subdevice;
        $datapoint->value = $value;
        $datapoint->datetime = $timestamp;
        $this->create($datapoint);
    }

    /**
     * @param HomeNet_Model_Datapoint_Interface|array $mixed
     * @return HomeNet_Model_Datapoint (HomeNet_Model_Datapoint_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_Datapoint_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Datapoint(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Datapoint');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param HomeNet_Model_Datapoint_Interface|array $mixed
     * @return HomeNet_Model_Datapoint (HomeNet_Model_Datapoint_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_Datapoint_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Datapoint(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Apikey');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param HomeNet_Model_Datapoint_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new HomeNet_Model_Datapoint();
            $object->id = $mixed;
        } elseif ($mixed instanceof HomeNet_Model_Datapoint_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Datapoint(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Datapoint');
        }

        return $this->getMapper()->delete($object);
    }

    /**
     * Delete all data. Used for unit testing/Will not work in production 
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