<?php

/*
 * ApikeyService.php
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
 * @subpackage Apikey
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Apikey_Service {

    /**
     * @var HomeNet_Model_Apikey_MapperInterface
     */
    protected $_mapper;

    /**
     * @return HomeNet_Model_Apikey_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Apikey_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_Apikey_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    public function getObjectById($id){
        $apikey = $this->getMapper()->fetchObjectById($id);

        if (empty($apikey)) {
            throw new HomeNet_Model_Exception('Apikey not found', 404);
        }
        return $apikey;
    }

    public function getObjectsByHouseUser($house,$user = null){
        $apikey = $this->getMapper()->fetchObjectsByHouseUser($house,$user);

        if (empty($apikey)) {
            throw new HomeNet_Model_Exception('Apikey not found', 404);
        }
        return $apikey;
    }

    public function getObjectsByIdHouse($id,$house){
        $apikeys = $this->getMapper()->fetchObjectsByIdHouse($id,$house);

        if (empty($apikeys)) {
            return array();
            //throw new HomeNet_Model_Exception('Apikey not found', 404);
        }
        return $apikeys;
    }


    public function createApikeyForHouse($house){

        if($house instanceof HomeNet_Model_House_Interface){
            $house = $house->id;
        }

        $apikey = new HomeNet_Model_Apikey();
        $apikey->id = sha1('saltsaddsf'.microtime().$house.$_SESSION['User']['id']);
        $apikey->house = $house;
        $apikey->user = $_SESSION['User']['id'];

        return $this->create($apikey);
    }


    public function validate($key,$house = null){
       $count= 0;
        if(!preg_match('/\b([a-f0-9]{40})\b/', $key)){
            //return false;
            throw new HomeNet_Model_Exception('Invalid Api Key Format');
        }

        $keys = array();

       if(!is_null($house)){
            $keys = $this->getObjectsByIdHouse($key, $house);
//die(debugArray($house));

       } else {
            $keys[0] = $this->getObjectById($key);
       }

        $count = count($keys);

        if($count == 0) {
           throw new HomeNet_Model_Exception('Invalid API Key');
        }
        return $keys[0];
    }

    public function create($apikey) {
        if ($apikey instanceof HomeNet_Model_Apikey_Interface) {
            $h = $apikey;
        } elseif (is_array($apikey)) {
            $h = new HomeNet_Model_Apikey(array('data' => $apikey));
        } else {
            throw new HomeNet_Model_Exception('Invalid Apikey');
        }

        return $this->getMapper()->save($h);
    }

    public function update($apikey) {
        if ($apikey instanceof HomeNet_Model_Apikey_Interface) {
            $h = $apikey;
        } elseif (is_array($apikey)) {
            $h = new HomeNet_Model_Apikey(array('data' => $apikey));
        } else {
            throw new HomeNet_Model_Exception('Invalid Apikey');
        }
        
        return $this->getMapper()->save($h);
    }

    public function delete($apikey) {
        if (is_int($apikey)) {
            $h = new HomeNet_Model_Apikey();
            $h->id = $apikey;
        } elseif ($apikey instanceof HomeNet_Model_Apikey_Interface) {
            $h = $apikey;
        } elseif (is_array($apikey)) {
            $h = new HomeNet_Model_Apikey(array('data' => $apikey));
        } else {
            throw new HomeNet_Model_Exception('Invalid Apikey');
        }

        return $this->getMapper()->delete($h);
    }

}