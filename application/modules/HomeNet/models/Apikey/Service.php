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
     * Storage mapper
     * 
     * @var HomeNet_Model_Apikey_MapperInterface
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_Apikey_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Apikey_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_Apikey_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_Apikey_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * Get ApiKey by id
     * 
     * @param int $id   Apikey id
     * @return HomeNet_Model_ApiKey  (HomeNet_Model_ApiKey_Interface) 
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Apikey not found', 404);
        }
        return $result;
    }

    /**
     * Get Apikeys by house id, user id
     * 
     * @param int $house    House Id
     * @param int $user     User Id
     * @return HomeNet_Model_ApiKey[] (HomeNet_Model_ApiKey_Interface[]) 
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function getObjectsByHouseUser($house, $user = null) {
        
        if (empty($house) || !is_numeric($house)) {
            throw new InvalidArgumentException('Invalid House');
        }
        
        if (!is_null($user) && !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid User');
        }
        
        $results = $this->getMapper()->fetchObjectsByHouseUser((int) $house, $user);

        if (empty($results)) {
            throw new NotFoundException('No Apikeys found', 404);
        }
        return $results;
    }

    /**
     * Get Apikeys by id, house
     * 
     * @param int $id       Apikey id
     * @param int $house    House id
     * @return HomeNet_Model_ApiKey[] (HomeNet_Model_ApiKey_Interface[]) 
     * @throws InvalidArgumentException
     */
    public function getObjectsByIdHouse($id, $house) {
        
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }
        
        if (empty($house) || !is_numeric($house)) {
            throw new InvalidArgumentException('Invalid House');
        }
        
        $results = $this->getMapper()->fetchObjectsByIdHouse($id, $house);

        if (empty($results)) {
            return array();
            //throw new NotFoundException('Apikey not found', 404);
        }
        return $results;
    }

    /**
     * Generate a new Apikey for a house and current user
     * 
     * @param HomeNet_Model_House_Interface|int $house House id
     * @return HomeNet_Model_ApiKey (HomeNet_Model_ApiKey_Interface)
     * @throws InvalidArgumentException 
     */
    public function createApikeyForHouse($house) {

        if ($house instanceof HomeNet_Model_House_Interface) {
            $house = $house->id;
        } elseif (empty($house) || !is_numeric($house)) {
            throw new InvalidArgumentException('Invalid House');
        }

        $user = Core_Model_User_Manager::getUser();

        $apikey = new HomeNet_Model_Apikey();
        $apikey->id = sha1('saltsaddsf' . microtime() . $house . $user->id);
        $apikey->house = $house;
        $apikey->user = $user->id;

        return $this->create($apikey);
    }

    /**
     * Validate Apikey
     * 
     * @param string $key   Apikey
     * @param int $house    House id
     * @return string key
     * @throws InvalidArgumentException 
     */
    public function validate($key, $house = null) {
        $count = 0;
        if (!preg_match('/\b([a-f0-9]{40})\b/', $key)) {
            //return false;
            throw new InvalidArgumentException('Invalid Api Key Format');
        }

        $keys = array();

        if (!is_null($house)) {
            $keys = $this->getObjectsByIdHouse($key, $house);
        } else {
            $keys[0] = $this->getObjectById($key);
        }

        $count = count($keys);

        if ($count == 0) {
            throw new Exception('Invalid API Key');
        }
        return $keys[0];
    }

    /**
     * Create new Apikey
     * 
     * @param HomeNet_Model_Apikey_Interface|array $mixed
     * @return HomeNet_Model_Apikey (HomeNet_Model_Apikey_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_Apikey_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Apikey(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Apikey');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Update an existing Apikey
     * 
     * @param HomeNet_Model_Apikey_Interface|array $mixed
     * @return HomeNet_Model_Apikey (HomeNet_Model_Apikey_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_Apikey_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Apikey(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Apikey');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Delete Apikey
     * 
     * @param HomeNet_Model_SubdeviceModel_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = $this->getObjectById($mixed);
        } elseif ($mixed instanceof HomeNet_Model_Apikey_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Apikey(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid ApiKey');
        }

        return $this->getMapper()->delete($object);
    }

    /**
     * Delete all Apikeys. Used for unit testing/Will not work in production 
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