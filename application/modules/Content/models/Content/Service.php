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
 * @subpackage Content
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_Content_Service {

    /**
     * @var Content_Model_Content_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Content_Model_Content_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_Content_MapperDbTable();
        }

        return $this->_mapper;
    }

    public function setMapper(Content_Model_Content_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    //

    public function addCustomTable($section, $fields = array()) {

        return $this->getMapper()->addCustomTable($section);


        //check if table exisits
        //false
        //create table with columns
        //true
        //check to see
        ////throw Exception 

        /* CREATE TABLE IF NOT EXISTS `content_section_5` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `revision` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`,`revision`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
         */
    }

    public function addCustomField(Content_Model_Field_Interface $field) {
        //alter table
        //ALTER TABLE `content_section_5` ADD `content` LONGTEXT NOT NULL 
        return $this->getMapper()->addCustomField($field);
    }

    public function renameCustomField(Content_Model_Field_Interface $old, Content_Model_Field_Interface $new) {
        //alter table
        //ALTER TABLE `content_section_5` ADD `content` LONGTEXT NOT NULL 
        return $this->getMapper()->renameCustomField($old, $new);
    }

    public function removeCustomField(Content_Model_Field_Interface $field) {
        //alter table
        //ALTER TABLE `content_section_5` DROP `content`
        return $this->getMapper()->removeCustomField($field);
    }

    public function removeCustomTable($section) {
        //drop table
        //DROP TABLE `content_section_5`
        return $this->getMapper()->removeCustomTable($section);
    }

    /**
     * @param int $id
     * @return Content_Model_Content_Interface 
     */
    public function getObjectByIdRevision($id, $revision) {
        $object = $this->getMapper()->fetchObjectByIdRevision($id, $revision);

        if (empty($object)) {
            throw new NotFoundException('Content not found', 404);
        }
        return $object;
    }

    /**
     * @param int $section
     * @return Content_Model_Content_Interface[]
     */
    public function getObjectsBySection($section) {
        $objects = $this->getMapper()->fetchObjectsBySection($section);

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $objects;
    }

    /**
     * @param int $id
     * @return Content_Model_Content_Interface[]
     */
    public function getObjectById($id) {
        $Object = $this->getMapper()->fetchObjectById($id);

        if (empty($Object)) {
            throw new NotFoundException('Content not found', 404);
        }
        return $Object;
    }

    public function getObjectByUrl($url) {
        $object = $this->getMapper()->fetchObjectByUrl($url);

        if (empty($object)) {
            throw new NotFoundException('Content not found', 404);
        }
        return $object;
    }

//    public function getObjectsByIdHouse($id,$house){
//        $apikeys = $this->getMapper()->fetchObjectsByIdHouse($id,$house);
//
//        if (empty($apikeys)) {
//            return array();
//            //throw new Content_Model_Exception('Apikey not found', 404);
//        }
//        return $apikeys;
//    }

    /**
     * @param mixed $content
     * @throws InvalidArgumentException 
     */
    public function create($content) {
        if ($content instanceof Content_Model_Content_Interface) {
            $h = $content;
        } elseif (is_array($content)) {
            $h = new Content_Model_Content(array('data' => $content));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $content
     * @throws InvalidArgumentException 
     */
    public function update($content) {
        if ($content instanceof Content_Model_Content_Interface) {
            $h = $content;
        } elseif (is_array($content)) {
            $h = new Content_Model_Content(array('data' => $content));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }

        return $this->getMapper()->save($h);
    }

    /**
     * @param mixed $content
     * @throws InvalidArgumentException 
     */
    public function delete($content) {
        if (is_int($content)) {
            $h = new Content_Model_Content();
            $h->id = $content;
        } elseif ($content instanceof Content_Model_Content_Interface) {
            $h = $content;
        } elseif (is_array($content)) {
            $h = new Content_Model_Content(array('data' => $content));
        } else {
            throw new InvalidArgumentException('Invalid Content');
        }

        return $this->getMapper()->delete($h);
    }

    public function deleteBySection($section) {
        $this->getMapper()->deleteBySection($section);
    }

    public function deleteAll() {
        if (APPLICATION_ENV != 'production') {
            $this->getMapper()->deleteAll();
            return;
        }
        throw new Exception("Not Allowed");
    }

}