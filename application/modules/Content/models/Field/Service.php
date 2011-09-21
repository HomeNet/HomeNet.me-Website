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
 * @subpackage Field
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_Field_Service {

    /**
     * @var Content_Model_Field_MapperInterface
     */
    protected $_mapper;

    /**
     * @return Content_Model_Field_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_Field_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * @param Content_Model_Section_MapperInterface $mapper 
     */
    public function setMapper(Content_Model_Field_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * @param int $id   Field Id
     * @return Content_Model_Field
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
     * @param int $section Content Section Id
     * @param string $name  Field Name
     * @return Content_Model_Field_Interface 
     * @throws NotFoundException
     */
    public function getObjectBySectionName($section, $name) {
        $result = $this->getMapper()->fetchObjectBySectionName($section, $name);

        if (empty($result)) {
            throw new NotFoundException('Section: ' . $section . ' Not Found', 404);
        }
        return $result;
    }

    /**
     * @param int $section Content Section Id
     * @return Content_Model_Field_Interface[]
     */
    public function getObjectsBySection($section) {
        $results = $this->getMapper()->fetchObjectsBySection($section);

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $results;
    }
    
     public function getMetadataBySection($section) {
         
        $fields = array();
        $results = $this->getObjectsBySection($section);
        foreach($results as $value){
            $fields[$value->name] = new Content_Model_Field(array('data'=>$value->toArray()));
        }
         
       

//        if (empty($contents)) {
//            throw new Exception('Apikey not found', 404);
//        }
        return $fields;
    }

    /**
     * @param Content_Model_Field_Interface|array $mixed
     * @return Content_Model_Field
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof Content_Model_Field_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Field(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }
        $field = $this->getMapper()->save($object);

        // if($field->type != Content_Model_Field::SYSTEM){
        $service = new Content_Model_Content_Service();
        $service->addCustomField($field);
        //  }

        return $field;
    }

    /**
     * @param Content_Model_Field_Interface|array $mixed
     * @return Content_Model_Field
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof Content_Model_Field_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Field(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        $found = null;

        try {
            $found = $this->getObjectBySectionName($object->section, $object->name);
        } catch (NotFoundException $e) {
            
        }

        if (!is_null($found)) {
            if($found->id != $object->id){
            throw new DuplicateEntryException('Name already exists ');
            }
        }

        $old = $this->getObjectById($object->id);


        $field = $this->getMapper()->save($object);

        if ($field->type != Content_Model_Field::SYSTEM) {
            $service = new Content_Model_Content_Service();
            $service->renameCustomField($old, $field);
        }

        return $field;
    }

    /**
     * @param Content_Model_Field_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if (is_int($mixed)) {

            $object = $this->getObjectById($mixed);
            //$h = new Content_Model_Field();
            //$h->id = $sectionField;
        } elseif ($mixed instanceof Content_Model_Field_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new Content_Model_Field(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid Object');
        }

        if ($object->type != Content_Model_Field::SYSTEM) {
            $service = new Content_Model_Content_Service();
            //die('remove field');
            $service->removeCustomField($object);
        }

        return $this->getMapper()->delete($object);
    }

    public function deleteBySection($section) {
        return $this->getMapper()->deleteBySection($section);
    }

    /**
     * Delete all data. Used for unit testing/Will not work in production 
     *
     * @return boolean Success
     * @throws NotAllowedException
     */
     public function deleteAll(){
        if(APPLICATION_ENV == 'production'){
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }

}