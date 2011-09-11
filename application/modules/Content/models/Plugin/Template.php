<?php

/*
 * Interface.php
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
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
abstract class Content_Model_Plugin_Template {
    
    function getFieldSets(){
        $fieldSets = array();
        $fieldSets['publish'] = array('title' => 'Publish', 'visible' => true);
        return $fieldSets;
    }
    
    function getFields($fieldSets){
          $fields = array();
         $fields[] = array(
            'set' => $fieldSets['publish'],
            'order' => 2,
            'type' => Content_Model_Field::SYSTEM,
            'element' => 'text',
            'name' => 'title',
            'name_label' => 'Title',
            'default_value' => '',
            'validators' => array(),
            'filters' => array(),
            'locked' => false,
            'required' => true,
            'visible' => true);
         $fields[] = array(
            'set' => $fieldSets['publish'],
            'order' => 2,
            'type' => Content_Model_Field::SYSTEM,
            'element' => 'url',
            'name' => 'url',
            'name_label' => 'Url',
            'default_value' => '',
            'validators' => array(),
            'filters' => array(),
            'locked' => false,
            'required' => true,
            'visible' => true);
         $fields[] = array(
            'set' => $fieldSets['publish'],
            'order' => 2,
            'type' => Content_Model_Field::SYSTEM,
            'element' => 'status',
            'name' => 'status',
            'name_label' => 'Status',
            'default_value' => '1',
            'validators' => array(),
            'filters' => array(),
            'locked' => false,
            'required' => true,
            'visible' => true);
         return $fields;
    }
    
    function getContent(){
        return array();
    }
    
    function getOptionalContent(){
        return array();
    }
}