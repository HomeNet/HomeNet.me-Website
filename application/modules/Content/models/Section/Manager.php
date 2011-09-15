<?php

/*
 * Manager.php
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
 * Description of Manager
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Content_Model_Section_Manager {

    function getTemplates() {
        $templates = array();

        $current = dirname(__FILE__);

        $path = realpath($current . '/../../plugins/Template');

        foreach (scandir($path) as $file) {

            if (($file != '.') && ($file != '..') && is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                $iniPath = $path . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . 'Plugin.ini';
                if (file_exists($iniPath)) {
                    // require_once($iniPath);
                    // $className = 'Content_Plugin_Template_' . $file . '_Installer';
                    // if (class_exists($className)) {
                    //$templates[$file] = $className;
                    $templates[$file] = new Zend_Config_Ini($iniPath);
                    //$templates[$file] = $config->toArray();
                    // } else {
                    //     throw new Exception('Can Not load Template, Class DNE: '.$file );
                    //  }
                } //throw new Exception('Can Not load Template: '.$classPath );
            }
        }
        return $templates;
    }

    function getForm($section) {
        
        $sService = new Content_Model_Section_Service();
        $sObject = $sService->getObjectById($section);

        $fService = new Content_Model_Field_Service();
        $objects = $fService->getObjectsBySection($section);

        $form = new CMS_Form();

        foreach ($objects as $field) {
            /* @var $field Content_Model_Field */
            $class = 'Content_Plugin_Element_' . ucfirst($field->element) . '_Element';

            if (!class_exists($class, true)) {
                throw new Exception('Element not found: ' . $class);
            }
            /* @var $element Content_Model_Plugin_Element */
            $element = new $class();

            $options = array();
            
            $options['name'] = $field->name;
            $options['label'] =  $field->label;
            $options['description'] =  $field->description;
            $options['value'] =  $field->value;
            $options['required'] = $field->required;

           // $options['validators'] = $field->validators; // array('alnum', array('regex', false, '/^[a-z]/i')  );
           // $options['filters'] = $field->filters; //array('StringToLower');
            //$options['attrib'] = $field->attributes;
//die(debugArray($options));

            $e = $element->getElement($options);
            
            $form->addElement($e);
        }
        
        $form->addDisplayGroup($form->getElements(), 'main', array('legend' => $sObject->title));
        
        return $form;
    }

    /**
     * Create a New Content Section from Template
     * 
     * @param string $class
     * @param array|Content_Model_Section_Interface $values
     * @param array $options Installer Options
     * 
     * @return Content_Model_Section_Interface
     */
    function createByTemplate($values, $template = null, $options = null) {

        if (is_null($template)) {
            if (!empty($values['template'])) {
                $template = $values['template'];
            } else {
                throw new InvalidArgumentException('Missing Template Value');
            }
        }

        //get Installer
        $class = 'Content_Plugin_Template_' . $template . '_Installer';

        if (!class_exists($class, true)) {
            throw new Exception('Could Not Load Class: ' . $class);
        }

        $plugin = new $class();

        //check dependencies
        foreach ($plugin->getDependencies() as $class) {
            if (!class_exists($class, true)) {
                //@todo create custom exception to catch later
                throw new Exception('Dependency ' . $class . ' Not Found');
            }
        }



        //create section
        $sService = new Content_Model_Section_Service();

        if (is_array($values)) {
            $section = new Content_Model_Section();
            $section->fromArray($values);
            $section->package = $template;
            //id 	package 	visible 	title 	url 	description  
        } elseif ($values instanceof Content_Model_Section_Interface) {
            $section = $values;
        } else {
            throw new InvalidArgumentException('Invalid Section');
        }

        $sObject = $sService->create($section);




        //install fieldsets
        $fieldSets = array();

        $fsService = new Content_Model_FieldSet_Service();

        foreach ($plugin->getFieldSets() as $name => $fieldSet) {
            $fieldSet['section'] = $sObject->id;
            $fsObject = $fsService->create($fieldSet);
            $fieldSets[$name] = $fsObject->id;
        }

        //install fields



        $fsService = new Content_Model_Field_Service();
        $order = 0;
        foreach ($plugin->getFields($fieldSets) as $field) {
            $field['package'] = $template;
            $field['order'] = $order;
            $field['section'] = $sObject->id;

            $fsObject = $fsService->create($field);



            $order++;
        }




        return $sObject;
    }

    function delete($section) {
        $service = new Content_Model_Section_Service();
        $service->delete($section);
    }

}