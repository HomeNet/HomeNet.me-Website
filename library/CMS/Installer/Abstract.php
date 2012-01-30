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
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * @package CMS
 * @subpackage Installer
 * @author Matthew Doll <mdoll at homenet.me>
 */
abstract class CMS_Installer_Abstract {

    /**
     * Name of the module, allows for parts of the installer to be automated
     * 
     * @var string
     */
    public $module = null;

    /**
     * Get Array of Optional Content that can be installed
     * 
     * @return array
     */
    public function getOptionalContent() {
        // array('value' => 'Display Title');
        return array();
    }

    /**
     * Execute code at the beginning of the process
     * 
     * @return void
     */
    public function installPre() {
        
    }

    /**
     * Returns Array of Create Table Queors
     * 
     * @return array
     */
    public function installTables() {
        /* array('test_table'=>'`id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
          `package` varchar(32) DEFAULT NULL,
          `title` varchar(128) NOT NULL,
          `visible` tinyint(1) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`)'); */
        return array();
    }

    /**
     * Execute custom code right after any new tables are installed
     * 
     * @return void
     */
    public function installPostTables() {
        
    }

    /**
     * List of User Acl's to Install; Most of the time this should be empty
     * 
     * @return array
     */
    public function getUserAcl() {
        // return $acl[] = array()


        return array();
    }

    /**
     * List of Group Acl to Install
     * 
     * @return array
     */
    public function getGroupAcl() {
        return array();
    }

    /**
     * Execute code to install any mandatory content for the module
     * 
     * @return void
     */
    public function installContent() {
        
    }

    /**
     * Execute code to install any selected optional contnent
     * 
     * @return void
     */
    public function installOptionalContent(array $list) {
        
    }

    /**
     * Execute code at the end of the install process
     * 
     * @return void
     */
    public function installPost() {
        
    }
    
    /**
     * Execute code to install test
     * 
     * @retrun void
     */
    public function installTest(){
        
    }

    /**
     * Execute code at the beginning of the uninstall process
     * 
     * @return void
     */
    public function uninstallPre() {
        
    }

    /**
     * List of tables to drop from the database
     * @return array
     */
    public function uninstallTables() {
        //array('test_table', 'test_table2')
        return array();
    }

    /**
     * Overide this if you need a custom uninstaller
     * 
     * return bool; true: autodelete all entries by module | false: custom coded uninstaller
     */
    public function uninstallUserAcl() {
        return true;
    }

    /**
     * Overide this if you need a custom uninstaller
     * 
     * return bool; true: autodelete all entries by module | false: custom coded uninstaller
     */
    public function uninstallGroupAcl() {
        return true;
    }

    /**
     * Execute code at the end of the uninstall process
     * 
     * @return void
     */
    public function uninstallPost() {
        
    }
    
    /**
     * Execute code at the end of the testing process
     * 
     * @return void
     */
    public function uninstallTest() {
        
    }
    /**
     * Get Links to show in the admin Menu
     * 
     * @return array
     */
    public function getAdminLinks(){
        return array();
    } 
    public function getAdminBlocks(){
        return array();
    } 
    
    public function getDependencies(){
        return array();
    }
}