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
class Content_Plugin_Template_Pages_Installer extends Content_Model_Plugin_Template {
    
    public function getDependencies(){
        $dependencies = array(
            
        );
        return $dependencies;
    }
    
    public function getTemplates(){
        
        $path = dirname(__FILE__);
        
        $templates = array();
        $templates[] = array(
            'type' => Content_Model_Template::TEMPLATE,
            'url' => 'index',
            'content' => file_get_contents($path.'/index.phtml'));
        return $templates;
    }
    
    
    public function getFields(){
        
         $fields = parent::getFields();
         $fieldSets = $this->getFieldSets();
         $fields['content'] = array(
            'set' => $fieldSets['default'],
            'order' => 2,
            'type' => Content_Model_Field::TEMPLATE,
            'element' => 'Editor',
            'name' => 'content',
            'label' => 'Content',
            'required' => true);
         
         
         
         
         return $fields;
    }
    
//    function getContent(){
//        return;
//    }
//    
//    function getOptionalContent(){
//        return array();
//    }
}