<?php

/*
 * Installer.php
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
 * Description of Installer
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */   
class Content_Plugin_Template_Base_Installer extends Content_Model_Plugin_Template {
    
    function getDependencies(){
        $dependencies = array(
            
        );
        return $dependencies;
    }
    
    
    function getFields(){
         $fields = parent::getFields();
//         $fields[] = array(
//            'set' => $fieldSets['publish'],
//            'order' => 2,
//            'type' => Content_Model_Field::USER,
//            'element' => 'textarea',
//            'name' => 'content',
//            'label' => 'Content',
//            'value' => '',
//            'validators' => array(),
//            'filters' => array(),
//            'locked' => false,
//            'required' => true,
//            'visible' => true);
         return $fields;
    }
    
//    function getContent(){
//        return array();
//    }
//    
//    function getOptionalContent(){
//        return array();
//    }
}