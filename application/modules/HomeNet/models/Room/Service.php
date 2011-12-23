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
 * @subpackage Room
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Room_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_Room_MapperInterface
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_Room_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Room_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_Room_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_Room_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * Get Room by id
     * 
     * @param int $id
     * @return HomeNet_Model_Room (HomeNet_Model_Room_Interface)
     * @throw InvalidArgumentException
     * @throw NotFoundException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid Id');
        }

        $result = $this->getMapper()->fetchObjectById((int) $id);

        if (empty($result)) {
            throw new NotFoundException('Room: ' . $id . ' Not Found', 404);
        }
        return $result;
    }

    /**
     * Get all Rooms by house id
     * 
     * @param int $house
     * @return HomeNet_Model_Room (HomeNet_Model_Room_Interface)
     * @throw InvalidArgumentException
     */
    public function getObjectsByHouse($house) {
        if (empty($house) || !is_numeric($house)) {
            throw new InvalidArgumentException('Invalid House');
        }
        $results = $this->getMapper()->fetchObjectsByHouse($house);

//        if (empty($results)) { //will only return
//            throw new HomeNet_Model_Exception('House: '.$house.' Not Found', 404);
//        }
        return $results;
    }

    /**
     * Get all Rooms by for an array of house ids
     * 
     * @param array $houses House Ids
     * @return HomeNet_Model_Room (HomeNet_Model_Room_Interface)
     * @throw InvalidArgumentException
     */
    public function getObjectsByHouses(array $houses) {
        if (empty($houses)) {
            throw new InvalidArgumentException('Invalid Houses');
        }
        $results = $this->getMapper()->fetchObjectsByHouses($houses);

        return $results;
    }

    /**
     * Get all Rooms for House in a specific region 
     * 
     * @param int $id
     * @return HomeNet_Model_Room (HomeNet_Model_Room_Interface)
     * @throw InvalidArgumentException
     */
    public function getObjectsByHouseRegion($house, $region) {
        if (empty($house) || !is_numeric($house)) {
            throw new InvalidArgumentException('Invalid Houses');
        }
        if (empty($region) || !is_numeric($region)) {
            throw new InvalidArgumentException('Invalid Region');
        }
        $results = $this->getMapper()->fetchObjectsByHouseRegion($house, $region);

//        if (empty($room)) {
//            throw new HomeNet_Model_Exception('Room not found', 404);
//        }
        return $results;
    }

    /**
     * Create a new Room
     * 
     * @param HomeNet_Model_Room_Interface|array $mixed
     * @return HomeNet_Model_Room (HomeNet_Model_Room_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_Room_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Room(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Room');
        }

        $result = $this->getMapper()->save($object);
        $houseService = new HomeNet_Model_House_Service();
        $house = $houseService->getObjectById($result->house);
        //$houseService->clearCacheById($result->house);

        $types = $houseService->getTypes();
        $user = Core_Model_User_Manager::getUser();

         $messageService = new HomeNet_Model_Message_Service();
        //$messageService->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $user->name . '</strong> Added ' . $room->name . ' to their ' . $types[$this->house->type] . ' ' . $this->house->name . ' to HomeNet', null, $id);
          $messageService->add(HomeNet_Model_Message::NEWITEM, '<span class="homenet-user" data-object="user" data-id="'.$user->id.'" data-field="name">' . $user->name . '</span> Added a new room 
              <span class="homenet-room" data-object="room" data-id="'.$result->id.'" data-field="name">' . $result->name . '</span> to <span class="homenet-house" data-object="house" data-id="' . $house->id.'" data-field="name">' . $house->name.'</span>', null, $house->id);
        // $mService = new HomeNet_Model_Message_Service();
        // $mService->add(HomeNet_Model_Message::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added &quot;' . $result->name . '&quot; to ' . $house->name . '', null, $house->id);
        //die('add message');
        return $result;
    }

    /**
     * Update an existing Room
     * 
     * @param HomeNet_Model_Room_Interface|array $mixed
     * @return HomeNet_Model_Room (HomeNet_Model_Room_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_Room_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Room(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Room');
        }

        $result = $this->getMapper()->save($object);

        $houseService = new HomeNet_Model_House_Service();
       // $houseService->clearCacheById($result->house);

        return $result;
    }

    /**
     * Delete a Room
     * 
     * @param HomeNet_Model_Room_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if ($mixed instanceof HomeNet_Model_Room_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Room(array('data' => $mixed));
        } elseif (is_numeric($mixed)) {
            $object = $this->getObjectbyId((int) $mixed);
        } else {
            throw new InvalidArgumentException('Invalid Room');
        }

        $result = $this->getMapper()->delete($object);

        $houseService = new HomeNet_Model_House_Service();
        //$houseService->clearCacheById($result->house);

        return $result;
    }

    /**
     * Delete all Rooms. Used for unit testing/Will not work in production 
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