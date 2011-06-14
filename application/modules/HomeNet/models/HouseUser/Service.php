<?php

/*
 * HouseUserService.php
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
 * @subpackage HouseUser
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_HouseUser_Service {

    /**
     * @var HomeNet_Model_HouseUsersMapperInterface
     */
    protected $_mapper;

    /**
     * @return HomeNet_Model_HouseUsersMapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_HouseUser_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_HouseUser_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }


    public function getObjectsbyUser($user){
        $houseUser = $this->getMapper()->fetchHousesbyUser($user);

        if (empty($houseUser)) {
            throw new HomeNet_Model_Exception('HouseUser not found', 404);
        }
        return $houseUser;
    }

    public function getObjectbyId($id){
        $houseUsers = $this->getMapper()->fetchHouseUserbyId($id);

        if (empty($houseUsers)) {
            throw new HomeNet_Model_Exception('HouseUser not found', 404);
        }
        return $houseUsers;
    }





    public function create($houseUser) {
        if ($houseUser instanceof HomeNet_Model_HouseUser_Interface) {
            $h = $houseUser;
        } elseif (is_array($houseUser)) {
            $h = new HomeNet_Model_HouseUser(array('data' => $houseUser));
        } else {
            throw new HomeNet_Model_Exception('Invalid HouseUser');
        }
        unset($houseUser);
        $houseUser = $this->getMapper()->save($h);

        $houseService = new HomeNet_Model_House_Service();
        $house = $houseService->getHouseById($houseUser->house);
        $houseService->clearCacheById($houseUser->house);

        $types = array('house' => 'House',
            'apartment' => 'Apartment',
            'condo' => 'Condo',
            'other' => '',
            'na' => '');

        $table = new HomeNet_Model_DbTable_Alerts();

        //$table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new houseUser ' . $houseUser->name . ' to their ' . $types[$this->house->type] . ' ' . $this->house->name . ' to HomeNet', null, $id);
        $table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new houseUser ' . $houseUser->name . ' to ' . $house->name . ' to HomeNet', null, $houseUser->id);


        return $houseUser;
    }

    public function update($houseUser) {
        if ($houseUser instanceof HomeNet_Model_HouseUser_Interface) {
            $h = $houseUser;
        } elseif (is_array($houseUser)) {
            $h = new HomeNet_Model_HouseUser(array('data' => $houseUser));
        } else {
            throw new HomeNet_Model_Exception('Invalid HouseUser');
        }
        $row = $this->getMapper()->save($h);

        $houseService = new HomeNet_Model_House_Service();
        $houseService->clearCacheById($this->house);

        return $row;
    }

    public function delete($houseUser) {
        if (is_int($houseUser)) {
            $h = new HomeNet_Model_HouseUser();
            $h->id = $houseUser;
        } elseif ($houseUser instanceof HomeNet_Model_HouseUser_Interface) {
            $h = $houseUser;
        } elseif (is_array($houseUser)) {
            $h = new HomeNet_Model_HouseUser(array('data' => $houseUser));
        } else {
            throw new HomeNet_Model_Exception('Invalid HouseUser');
        }

        $row = $this->getMapper()->delete($houseUser);

        $houseService = new HomeNet_Model_House_Service();
        $houseService->clearCacheById($this->house);

        return $row;
    }

}