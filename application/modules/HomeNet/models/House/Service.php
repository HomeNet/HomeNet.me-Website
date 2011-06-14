<?php

/*
 * HouseService.php
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
 * @subpackage House
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_House_Service {

    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_mapper;
    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_cacheMapper;
    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_mapperClass = 'HomeNet_Model_House_MapperDbTable';
    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_cacheMapperClass = 'HomeNet_Model_House_MapperCache';
    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_cache = true;

    /**
     * @return HomeNet_Model_House_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {

            if ($this->cacheEnabled()) {
                $this->_mapper = $this->getCacheMapper();
            } else {
                $this->_mapper = new $this->_mapperClass();
            }
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_House_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @return HomeNet_Model_HousesMapperInterface
     */
    public function getCacheMapper() {

        if (empty($this->_cacheMapper)) {
            $this->_cacheMapper = new $this->_cacheMapperClass(new $this->_mapperClass());
        }

        return $this->_cacheMapper;
    }

    public function setCacheMapper(HomeNet_Model_House_MapperInterface $mapper) {
        $this->_cacheMapper = $mapper;
    }

    public function enableCache() {

        $this->_cache = true;
    }

    public function disableCache() {
        $this->_cache = false;
        unset($this->_mapper);
        unset($this->_cacheMapper);
    }

    /**
     * @return boolean
     */
    public function cacheEnabled() {

        return $this->_cache;
    }

    /**
     * @param int $id
     * @return HomeNet_Model_House_Interface
     */
    public function getObjectById($id) {
        if (empty($id)) {
            throw new HomeNet_Model_Exception('Missing House');
        }


        $house = $this->getMapper()->fetchHouseById($id);

        if (empty($house)) {
            throw new HomeNet_Model_Exception('House not found', 404);
        }
        return $house;
    }

    /**
     * @param int $id
     * @return HomeNet_Model_House_Interface
     */
    public function getObjectByIdWithRooms($id) {

        if (empty($id)) {
            throw new HomeNet_Model_Exception('Missing House');
        }

        $house = $this->getMapper()->fetchHouseByIdWithRooms($id);

        if (empty($house)) {
            throw new HomeNet_Model_Exception('House not found', 404);
        }
        return $house;
    }

    public function getObjectsByIds($ids) {
        if (empty($ids)) {
            throw new HomeNet_Model_Exception('Missing Houses');
        }

        $houses = $this->getMapper()->fetchHousesByIds($ids);

        if (empty($houses)) {
            // throw new HomeNet_Model_Exception('House not found', 404);
        }
        return $houses;
    }

    public function getObjectsByIdsWithRooms($ids) {

        if (empty($ids)) {
            throw new HomeNet_Model_Exception('Missing Houses');
        }

        $houses = $this->getMapper()->fetchHousesByIdsWithRooms($ids);

        if (empty($houses)) {
            throw new HomeNet_Model_Exception('House not found', 404);
        }
        return $houses;
    }

    /**
     * @todo find a better home for this
     */
    public function getHouseIdsByUser($user = null) {

        if (!isset($_SESSION['HomeNet']['houses'])) {

            $user = new Zend_Session_Namespace('User');

            $service = new HomeNet_Model_HouseUser_Service();
            $rows = $service->getObjectsbyUser($user->id);

            $_SESSION['HomeNet']['houses'] = array();

            foreach ($rows as $row) {
                $_SESSION['HomeNet']['houses'][] = $row->house;
            }
        }
        return $_SESSION['HomeNet']['houses'];
    }

    public function getHouseRegionNames($id) {

        if (empty($id)) {
            throw new HomeNet_Model_Exception('Missing House');
        }

        $regions = array(
            '1' => 'First Floor',
            '2' => 'Second Floor',
            '3' => 'Third Floor',
            '4' => 'Forth Floor',
            '5' => 'Fifth Floor',
            'B' => 'Basement',
            'A' => 'Attic',
            'O' => 'Outdoors');

        $r = $this->getObjectById($id)->regions;

        if (is_string($r)) {
            $r = unserialize($r);
        }
        $r = array_flip($r);

        foreach ($r as $key => $region) {
            $r[$key] = array('id' => $key, 'name' => $regions[$key]);
        }
        return $r;
    }

    public function clearCacheById($id) {
        
    }

    public function create($house) {
        if ($house instanceof HomeNet_Model_House_Interface) {
            $h = $house;
        } elseif (is_array($house)) {
            $h = new HomeNet_Model_House(array('data' => $house));
        } else {
            throw new HomeNet_Model_Exception('Invalid House');
        }

        if (is_null($h->status)) {
            $h->status = -1;
        }

        if (is_null($h->url)) {
            $h->url = '';
        }

        if (is_null($h->settings)) {
            $h->settings = array();
        }

        if (is_null($h->permissions)) {
            $h->permissions = array();
        }

        unset($house);
        $house = $this->getMapper()->save($h);

        //add to house user
        //create user perrmissions
        $table2 = new HomeNet_Model_DbTable_HouseUsers();
        $houseUser = $table2->createRow();
        $user = new Zend_Session_Namespace('User');
        $houseUser->user = $user->id;
        $houseUser->house = $house->id;
        $houseUser->permissions = '';

        $houseUser->save();

        //reset session cache
        unset($_SESSION['HomeNet']['houses']);


        //add alerts
        $table = new HomeNet_Model_DbTable_Alerts();

        //$url = $this->view->url(array('action' => 'index'), 'homenet-setup-index');

        $table->add(HomeNet_Model_Alert::NEWITEM, 'Congrates on starting your HomeNet. If you need to, you can return to the <a href="/home/' . $house->id . '/setup">Setup Wizard</a>', $_SESSION['User']['id']);
        $table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added their home &quot;' . $house->name . '&quot; to HomeNet', null, $house->id);

        return $house;
    }

    public function update($house) {
        if ($house instanceof HomeNet_Model_House_Interface) {
            $h = $house;
        } elseif (is_array($house)) {
            $h = new HomeNet_Model_House(array('data' => $house));
        } else {
            throw new HomeNet_Model_Exception('Invalid House');
        }

        $row = $this->getMapper()->save($h);
        $this->clearCacheById($h->id);
        return $row;
    }

    public function delete($house) {
        if (is_int($house)) {
            $h = new HomeNet_Model_House();
            $h->id = $house;
        } elseif ($house instanceof HomeNet_Model_House_Interface) {
            $h = $house;
        } elseif (is_array($house)) {
            $h = new HomeNet_Model_House(array('data' => $house));
        } else {
            throw new HomeNet_Model_Exception('Invalid House');
        }

        $row = $this->getMapper()->delete($h);
        $this->clearCacheById($h->id);
        return $row;
    }

}