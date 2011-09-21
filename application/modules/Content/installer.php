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
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */

class Content_Installer extends CMS_Installer_Abstract
{
    static public $testSection; 
    
    public function getAdminLinks(){
        return array(
            array('title' => 'Category Sets',    'route'=>'content', 'options' =>  array('controller'=>'category-set')),
            array('title' => 'Content Sections', 'route'=>'content', 'options' =>  array('controller'=>'section'))
        );
    } 
    
    function installTest() {
        
        $section = new Content_Model_Section();
        $section->title = 'Test Section';
        $section->url = 'test_section';
        $section->visible = false;
        
         $manager = new Content_Model_Section_Manager();
        $object = $manager->createByTemplate($section, 'Base');
         
         self::$testSection = $object->id;
         //die(self::$testSection );
     }
    
    
    function uninstallTest() {
        $service = new Content_Model_Category_Service();
        $service->deleteAll();
        
        $service = new Content_Model_CategorySet_Service();
        $service->deleteAll();
        
        $service = new Content_Model_Content_Service();
        $service->deleteAll();
        
        $service = new Content_Model_ContentCategory_Service();
        $service->deleteAll();
        
        $service = new Content_Model_Field_Service();
        $service->deleteAll();
        
        $service = new Content_Model_FieldSet_Service();
        $service->deleteAll();
        
        $service = new Content_Model_Template_Service();
        $service->deleteAll();
        
        $service = new Content_Model_Section_Service();
        $service->deleteAll();
        
    }
}
