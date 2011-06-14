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
     * @var HomeNet_Model_Message_MapperInterface
     */
    protected $_mapper;

    /**
     * @return HomeNet_Model_Message_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_Message_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(HomeNet_Model_Message_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id
     * @return HomeNet_Model_MessageInterface
     */
    public function getObjectsByUser($user) {
        $messages = $this->getMapper()->fetchMessagesByUser($user);

        if (empty($messages)) {
           // throw new HomeNet_Model_Exception('User not found', 404);
        }
        return $messages;
    }

    public function getObjectsByHouseOrUser($house, $user) {
        return $this->getMapper()->fetchMessagesByHouseOrUser($house, $user);
    }

    public function add($level, $message, $user = null, $house =null, $room = null, $subdevice = null) {

        $row = new HomeNet_Model_Message();
        $row->level = $level;
        $row->message = $message;
        $row->user = $user;
        $row->house = $house;
        $row->subdevice = $subdevice;

        $this->create($row);
    }

    public function create($message) {
        if ($message instanceof HomeNet_Model_Message_Interface) {
            $h = $message;
        } elseif (is_array($message)) {
            $h = new HomeNet_Model_Message(array('data' => $message));
        } else {
            throw new HomeNet_Model_Exception('Invalid Room');
        }

        return $this->getMapper()->save($h);
    }

    public function update($message) {
        if ($message instanceof HomeNet_Model_Message_Interface) {
            $h = $message;
        } elseif (is_array($message)) {
            $h = new HomeNet_Model_Message(array('data' => $message));
        } else {
            throw new HomeNet_Model_Exception('Invalid Room');
        }

        return $this->getMapper()->save($h);
    }

    public function delete($message) {
        if (is_int($message)) {
            $h = new HomeNet_Model_Message();
            $h->id = $message;
        } elseif ($message instanceof HomeNet_Model_Message_Interface) {
            $h = $message;
        } elseif (is_array($message)) {
            $h = new HomeNet_Model_Message(array('data' => $message));
        } else {
            throw new HomeNet_Model_Exception('Invalid Room');
        }

        return $this->getMapper()->delete($h);
    }

}