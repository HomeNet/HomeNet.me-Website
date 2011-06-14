<?php

/*
 * DatapointService.php
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
 * @subpackage Datapoint
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Datapoint_Service {

    /**
     * @var HomeNet_Model_DatapointsMapperInterface
     */
    protected $_mapper;

    protected $_type;

    /**
     * @return HomeNet_Model_DatapointsMapperInterface
     */
    public function getMapper() {

        if(empty($this->_type)){
            throw new Zend_Exception('Set Type First');
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

    public function setType($type){
        $this->_type = $type;
    }





    /**
     * @param int $id
     * @return HomeNet_Model_Datapoint_Interface
     */
    public function getNewestDatapointBySubdevice($subdevice) {
        $datapoint = $this->getMapper()->fetchNewestDatapointBySubdevice($subdevice);

        if (empty($datapoint)) {
           // throw new HomeNet_Model_Exception('Datapoint not found', 404);
        }
        return $datapoint;
    }

    public function getAveragesBySubdeviceTimespan($subdevice, Zend_Date $start, Zend_Date $end, $points = null){
       $datapoints = $this->getMapper()->fetchAveragesBySubdeviceTimespan($subdevice, $start, $end, $points);

        if (empty($datapoints)) {
           // throw new HomeNet_Model_Exception('Datapoint not found', 404);
        }
        return $datapoints;
    }

    public function getDatapointsBySubdeviceTimespan($subdevice, Zend_Date $start, Zend_Date $end){
        $datapoint = $this->getMapper()->fetchDatapointsBySubdeviceTimespan($subdevice, $start, $end);

        if (empty($datapoint)) {
          //  throw new HomeNet_Model_Exception('Datapoint not found', 404);
        }
        return $datapoint;
    }


    public function add($type,$subdevice,$value,$timestamp){
        $this->setType($type);
        $datapoint = new HomeNet_Model_Datapoint();
        $datapoint->subdevice = $subdevice;
        $datapoint->value = $value;
        $datapoint->datetime = $timestamp;
        $this->create($datapoint);
    }




    /**
     * @param mixed $datapoint
     * @return HomeNet_Model_DatapointInterface
     */
    public function create($datapoint) {
        if ($datapoint instanceof HomeNet_Model_Datapoint_Interface) {
            $h = $datapoint;
        } elseif (is_array($datapoint)) {
            $h = new HomeNet_Model_Datapoint(array('data' => $datapoint));
        } else {
            throw new HomeNet_Model_Exception('Invalid Datapoint');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $datapoint
     * @return HomeNet_Model_DatapointInterface
     */
    public function update($datapoint) {
        if ($datapoint instanceof HomeNet_Model_Datapoint_Interface) {
            $h = $datapoint;
        } elseif (is_array($datapoint)) {
            $h = new HomeNet_Model_Datapoint(array('data' => $datapoint));
        } else {
            throw new HomeNet_Model_Exception('Invalid Datapoint');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $datapoint
     * @return HomeNet_Model_DatapointInterface
     */
    public function delete($datapoint) {
        if (is_int($datapoint)) {
            $h = new HomeNet_Model_Datapoint();
            $h->id = $datapoint;
        } elseif ($datapoint instanceof HomeNet_Model_Datapoint_Interface) {
            $h = $datapoint;
        } elseif (is_array($datapoint)) {
            $h = new HomeNet_Model_Datapoint(array('data' => $datapoint));
        } else {
            throw new HomeNet_Model_Exception('Invalid Datapoint');
        }

        return $this->getMapper()->delete($datapoint);
    }
}