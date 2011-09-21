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
 * @subpackage ContentCategory
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_ContentCategory_Service {

    /**
     * @var Content_Model_ContentCategory_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Content_Model_ContentCategory_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_ContentCategory_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * @param Content_Model_Section_MapperInterface $mapper 
     */
    public function setMapper(Content_Model_ContentCategory_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param integer $id ContentCategory Id
     * @return Content_Model_ContentCategory
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
     * @param integer $id
     * @return Content_Model_ContentCategory
     * @throws NotFoundException
     */
    public function getObjectsBySet($id) {

        $results = $this->getMapper()->fetchObjectsBySet($id);

//        if (empty($objects)) {
//            throw new NotFoundException('Id: ' . $id . ' Not Found', 404);
//        }
        return $results;
    }

    /**
     * @param string $url
     * @return Content_Model_ContentCategory
     * @throws NotFoundException
     */
    public function getObjectByUrl($url) {

        $category = $this->getMapper()->fetchObjectByUrl($url);

        if (empty($category)) {
            throw new NotFoundException('URL: ' . $url . ' Not Found', 404);
        }
        return $category;
    }

    /**
     * @param Content_Model_ContentCategory_Interface|array $mixed
     * @return Content_Model_ContentCategory
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof Content_Model_ContentCategory_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_ContentCategory(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param Content_Model_ContentCategory_Interface|array $mixed
     * @return Content_Model_ContentCategory
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof Content_Model_ContentCategory_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_ContentCategory(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->save($object);
    }

    /**
     * @param Content_Model_ContentCategory_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {
            $object = new Content_Model_ContentCategory();
            $object->id = $mixed;
        } elseif ($mixed instanceof Content_Model_ContentCategory_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_ContentCategory(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        return $this->getMapper()->delete($object);
    }

    /**
     * @param integer $section Section Id
     * @return boolean Success
     */
    public function deleteBySection($section) {
        return $this->getMapper()->deleteBySection($section);
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
        return $this->getMapper()->deleteAll();
    }

}