<?php

/*
 * RoomService.php
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
 * @subpackage Room
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Room_Service {

    /**
     * @var HomeNet_Model_Room_MapperInterface
     */
    protected $_mapper;

    /**
     * @return HomeNet_Model_Room_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Room_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_Room_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return HomeNet_Model_Room_Interface
     */
    public function getObjectById($id) {
        $room = $this->getMapper()->fetchRoomById($id);

        if (empty($room)) {
            throw new HomeNet_Model_Exception('Room not found', 404);
        }
        return $room;
    }

    public function getObjectsByHouse($house){
        $rooms = $this->getMapper()->fetchRoomsByHouse($house);

        if (empty($rooms)) {
            throw new HomeNet_Model_Exception('house not found', 404);
        }
        return $rooms;
    }

    public function getObjectsByHouses($houses){
        $room = $this->getMapper()->fetchRoomsByHouses($houses);

        if (empty($room)) {
            throw new HomeNet_Model_Exception('Room not found', 404);
        }
        return $room;
    }

    public function getObjectsByRegion($region){
        $rooms = $this->getMapper()->fetchRoomsByRegion($region);

        if (empty($room)) {
            throw new HomeNet_Model_Exception('Room not found', 404);
        }
        return $rooms;
    }




    public function create($room) {
        if ($room instanceof HomeNet_Model_Room_Interface) {
            $h = $room;
        } elseif (is_array($room)) {
            $h = new HomeNet_Model_Room(array('data' => $room));
        } else {
            throw new HomeNet_Model_Exception('Invalid Room');
        }
        unset($room);
        $room = $this->getMapper()->save($h);

        $houseService = new HomeNet_Model_House_Service();
        $house = $houseService->getObjectById($room->house);
        $houseService->clearCacheById($room->house);

        $types = array('house' => 'House',
            'apartment' => 'Apartment',
            'condo' => 'Condo',
            'other' => '',
            'na' => '');

      //  $mService = new HomeNet_Model_Message_Service();

        //$table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new room ' . $room->name . ' to their ' . $types[$this->house->type] . ' ' . $this->house->name . ' to HomeNet', null, $id);
      //  $mService->add(HomeNet_Model_Message::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new room ' . $room->name . ' to ' . $house->name . ' to HomeNet', null, $room->id);
        $mService = new HomeNet_Model_Message_Service();
        $mService->add(HomeNet_Model_Message::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added &quot;' . $room->name . '&quot; to ' . $house->name . '', null, $house->id);

        //die('add message');
        return $room;
    }

    public function update($room) {
        if ($room instanceof HomeNet_Model_Room_Interface) {
            $h = $room;
        } elseif (is_array($room)) {
            $h = new HomeNet_Model_Room(array('data' => $room));
        } else {
            throw new HomeNet_Model_Exception('Invalid Room');
        }
        $row = $this->getMapper()->save($h);

        $houseService = new HomeNet_Model_House_Service();
        $houseService->clearCacheById($h->house);

        return $row;
    }

    public function delete($room) {
        if (is_int($room)) {
            $h = new HomeNet_Model_Room();
            $h->id = $room;
        } elseif ($room instanceof HomeNet_Model_Room_Interface) {
            $h = $room;
        } elseif (is_array($room)) {
            $h = new HomeNet_Model_Room(array('data' => $room));
        } else {
            throw new HomeNet_Model_Exception('Invalid Room');
        }

        $row = $this->getMapper()->delete($room);

        $houseService = new HomeNet_Model_House_Service();
        $houseService->clearCacheById($row->house);

        return $row;
    }

}