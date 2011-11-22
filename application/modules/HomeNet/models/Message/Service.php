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
 * @subpackage Message
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Message_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_Message_MapperInterface
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_Message_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Message_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_Message_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_Message_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * Get messages by user
     * 
     * @param int $user
     * @return HomeNet_Model_Message[] (HomeNet_Model_Message_Interface[])
     * @throws InvalidArgumentException
     */
    public function getObjectsByUser($user) {
        if (empty($user) || !is_numeric($user)) {
            throw new InvalidArgumentException('Invalid User');
        }

        $results = $this->getMapper()->fetchMessagesByUser($user);
        return $results;
    }

    /**
     * Get all messages relevant to the user or to the house
     * 
     * @param integer $house House id
     * @param integer $user User id
     * @return HomeNet_Model_Message[] (HomeNet_Model_Message_Interface[])
     * @throws InvalidArgumentException
     */
    public function getObjectsByHouseOrUser($house, $user) {
        if (empty($house) || !is_numeric($user)) {
            throw new InvalidArgumentException('Invalid House');
        }
        if (empty($user) || !is_numeric($user)) {
            throw new InvalidArgumentException('Invalid User');
        }
        return $this->getMapper()->fetchMessagesByHouseOrUser($house, $user);
    }

    /**
     * Shortcut for creating a new message
     * 
     * @param integer $level
     * @param string $message 
     * @param integer $user User is
     * @param integer $house House id
     * @param integer $room Room id
     * @param integer $subdevice Subdevice id
     * @return HomeNet_Model_Message (HomeNet_Model_Message_Interface)
     */
    public function add($level, $message, $user = null, $house =null, $room = null, $subdevice = null) {

        $row = new HomeNet_Model_Message();
        $row->level = $level;
        $row->message = $message;
        $row->user = $user;
        $row->house = $house;
        $row->subdevice = $subdevice;

        return $this->create($row);
    }

    /**
     * Create a new Message
     * 
     * @param HomeNet_Model_Message_Interface|array $mixed
     * @return HomeNet_Model_Message (HomeNet_Model_Message_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_Message_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Message(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Message');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Update an existing Message
     * 
     * @param HomeNet_Model_Message_Interface|array $mixed
     * @return HomeNet_Model_Message (HomeNet_Model_Message_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_Message_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Message(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Message');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * Delete a Message
     * 
     * @param HomeNet_Model_Message_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = $this->getObjectbyId($mixed);
        } elseif ($mixed instanceof HomeNet_Model_Message_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_Message(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Message');
        }

        return $this->getMapper()->delete($object);
    }

    /**
     * Delete all Messages. Used for unit testing/Will not work in production 
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