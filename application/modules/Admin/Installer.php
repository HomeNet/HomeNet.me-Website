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
class Admin_Installer extends CMS_Installer_Abstract {
    public function getAdminBlock(){
        
    }
    
    public function getAdminLinks(){
        return array(
            array('title' => 'Routes', 'route'=>'admin', 'options' => array('controller'=>'route')),
            array('title' => 'Menus',  'route'=>'admin', 'options' =>  array('controller'=>'menu')),
            array('title' => 'Groups',    'route'=>'admin', 'options' =>  array('controller'=>'group')),
            array('title' => 'Users',     'route'=>'admin', 'options' =>  array('controller'=>'user'))
        );
    }
}