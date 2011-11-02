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
class Content_Model_Content implements Content_Model_Content_Interface {

    private $_values = array('autosave' => false, 'status' => -1, 'visible' => false);
    private $_objects = array();
    private $_metadata = null;

    // public $id, $revision, $owner, $autosave = false, $section, $status = -1, $created, $expires, $author, $editor, $title, $url, $content, $visible = true;

    public function __construct(array $config = array()) {
        if (isset($config['data'])) {
            $this->fromArray($config['data']);
        }
        if (isset($config['metadata'])) {
            $this->_metadata($config['metadata']);
        }
    }

    public function getSection() {
        if (is_null($this->_metadata)) {
            $this->loadMetadata();
        }
        return $this->_metadata;
    }

    public function __get($name) {


        if (!isset($this->_objects[$name])) {

            $fields = $this->getSection()->getFields();
            // $fields = array();
            //unset($fields['url']);
            if (isset($fields[$name])) {
                //create object

                $class = 'Content_Plugin_Element_' . ucfirst($fields[$name]->element) . '_Element';
                if (!class_exists($class, true)) {
                    throw new Exception('Element not found: ' . $class);
                }

                $data = null;
                if(isset($this->_values[$name])){
                    $data = $this->_values[$name];
                }

                $this->_objects[$name] = new $class(array(
                            'data' => $data,
                            'options' => $fields[$name]->options));
                //$this->_objects[$name] = $this->_values[$name];
            } elseif (isset($this->_values[$name])) {
                //id or revision or something without metadata
                $this->_objects[$name] = $this->_values[$name];
            } else {
                return null;
            }
        }


        return $this->_objects[$name];
    }

    public function loadMetadata() {
        if (is_null($this->_metadata)) {
            if (!isset($this->_values['section'])) {
                throw new Exception('Section Required');
            }
            $service = new Content_Model_Section_Service();
            $this->_metadata = $service->getMetadataById($this->_values['section']);
        }
    }

    public function __set($name, $value) {
        $this->_values[$name] = $value;
    }

    public function __unset($name) {
        unset($this->_values[$name]);
    }

    public function __isset($name) {
        return isset($this->_values[$name]);
    }

    public function fromArray(array $array) {

        //  $vars = get_object_vars($this);
        // die(debugArray($vars));
        //  foreach ($array as $key => $value) {
        //     if (array_key_exists($key, $vars)) {
        //     $this->$key = $value;
        //   }
        // }
        $this->_values = array_merge($this->_values, $array);
    }

    /**
     * @return array
     */
    public function toArray() {

        $array = $this->_values;

        foreach ($this->_objects as $key => $value) {
            if (is_object($value)) {
                $array[$key] = $value->getValue();
            }
        }

        return $array;
    }
    
    public function toObjects() {

        $array = $this->_values;

        $fields = $this->getSection()->getFields();

        foreach ($fields as $name => $value) {

            if (!isset($this->_objects[$name])) {

                $class = 'Content_Plugin_Element_' . ucfirst($value->element) . '_Element';
                if (!class_exists($class, true)) {
                    throw new Exception('Element not found: ' . $class);
                }

                $data = null;
                if (isset($this->_values[$name])) {
                    $data = $this->_values[$name];
                }

                $this->_objects[$name] = new $class(array(
                            'data' => $data,
                            'options' => $value->options));
                //$this->_objects[$name] = $this->_values[$name];
            }
            $array[$name] = $this->_objects[$name];
        }

        return $array;
    }

    public function getForm() {

        $form = new CMS_Form();

        $fields = $this->getSection()->getFields();
        
        $sets = array();

        foreach ($fields as $key => $field) {

            $object = $this->$key;
         //   echo $key;
        //  echo debugArray($object->getValue());
            $options = array(
                'name' => $field->name,
                'label' => $field->label,
                'description' => $field->description,
                'value' => $object->getFormValue(),
                'required' => $field->required,
            );
            if(empty($sets[$field->set])){
                $sets[$field->set] = array();
            }
           $sets[$field->set][] = $field->name;
            
          //  debugArray($object->getValue());
            // $options['validators'] = $field->validators; // array('alnum', array('regex', false, '/^[a-z]/i')  );
            // $options['filters'] = $field->filters; //array('StringToLower');
            //$options['attrib'] = $field->attributes;


            $e = $object->getElement($options, $field->options);
            $form->applyDefaultDecorators($e);
            $form->addElement($e);
        }
        
        $service = new Content_Model_FieldSet_Service();
        $fieldSets = $service->getObjectsBySection($this->section);

        foreach($fieldSets as $value){
            if(isset($sets[$value->id])){
            $form->addDisplayGroup($sets[$value->id], 'set-'.$value->id, array('legend' => $value->title));
            }
        }
        

       // $form->addDisplayGroup($form->getElements(), 'main', array('legend' => $this->getSection()->title));

        return $form;
    }
    
    public function save(){
        $fields = $this->getSection()->getFields();
        

        foreach ($fields as $key => $field) {

            $object = $this->$key;
            $object->save();
        }
    }

}