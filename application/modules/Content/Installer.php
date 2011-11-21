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
    public $section; 
    public $categorySet;
    public $fieldSet;
    
    /*
     * @todo autpo grant privliges
     */
     
    public function __construct() {
       $this->section = new stdClass;
       $this->categorySet = new stdClass;
       $this->fieldSet = new stdClass;
    }
    
    public function getAdminBlocks(){
        return array(
            array('module' => 'Content', 'widget'=>'AdminSections')
            
        );
    } 
    
    public function getAdminLinks(){
        return array(
            array('title' => 'Category Sets',    'route'=>'content-admin', 'options' =>  array('controller'=>'category-set')),
            array('title' => 'Content Sections', 'route'=>'content-admin', 'options' =>  array('controller'=>'section'))
            
        );
    } 
    
          //  
    
    function installTest() {
        
        $this->uninstallTest(); //remove any old data
        
        $categorySet = new Content_Model_CategorySet();
        $categorySet->package = "test";
        $categorySet->title = 'Test CategorySet';
        $categorySet->visible = true;
        
        $csService = new Content_Model_CategorySet_Service();

        $this->categorySet->test = $csService->create($categorySet);
        
        $section = new Content_Model_Section();
        $section->title = 'Test Section';
        $section->url = 'test_section';
        $section->visible = false;
        
         $manager = new Content_Model_Section_Manager();
        $this->section->test = $manager->createByTemplate($section, 'Base');
        
        $fsService = new Content_Model_FieldSet_Service();
        $results = $fsService->getObjectsBySection($this->section->test->id);
        $this->fieldSet->test = $results[0];
        
        
         
         //self::$testSection = $object->id;
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
