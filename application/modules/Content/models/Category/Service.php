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
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package Content
 * @subpackage Category
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_Category_Service {

    /**
     * @var Content_Model_Category_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Content_Model_Category_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_Category_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * @param Content_Model_Section_MapperInterface $mapper 
     */
    public function setMapper(Content_Model_Category_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }
    
//     /**
//     * @param int $id
//     * @return Content_Model_Category
//     * @throws NotFoundException
//     */
//    public function getObject($mixed) {
//        
//        if (is_numeric($mixed)) {
//            $result = $this->getObjectById((int) $mixed);
//        } elseif (is_string($mixed)) {
//            $result = $object = $this->getObjectByUrl($mixed);
//        } elseif (is_array($mixed) && !empty($mixed['id'])) {
//            $result = $object = $this->getObjectById($mixed['id']);
//        } else {
//            throw new InvalidArgumentException('Invalid Category');
//        }
//
//
//        if (empty($result)) {
//            throw new NotFoundException('Id: ' . $mixed . ' Not Found', 404);
//        }
//        return $result;
//    }

    /**
     * @param int $id
     * @return Content_Model_Category
     * @throws NotFoundException
     */
    public function getObjectById($id) {

        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('Id: ' . $id . ' Not Found', 404);
        }
        return $result;
    }

    /**
     * @param int $id Content Category Set Id
     * @return Content_Model_Category
     */
    public function getObjectsBySet($id) {

        $results = $this->getMapper()->fetchObjectsBySet($id);

        if (empty($results)) {
            throw new NotFoundException('Id: ' . $id . ' Not Found', 404);
        }
        return $results;
    }

    /**
     * @param string $url Content Category Url
     * @return Content_Model_Category
     * @throws NotFoundException
     */
    public function getObjectByUrl($url) {

        $result = $this->getMapper()->fetchObjectByUrl($url);

        if (empty($result)) {
            throw new NotFoundException('URL: ' . $url . ' Not Found', 404);
        }
        return $result;
    }

    /**
     * @param Content_Model_Category_Interface|array $mixed
     * @return Content_Model_Category
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof Content_Model_Category_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Category(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Category');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param Content_Model_Category_Interface|array $mixed
     * @return Content_Model_Category
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof Content_Model_Category_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Category(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Category');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param Content_Model_Category_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new Content_Model_Category();
            $object->id = $mixed;
        } elseif ($mixed instanceof Content_Model_Category_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Category(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Category');
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