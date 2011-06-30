<?php

/*
 * Acl.php
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
 * Description of Acl
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class CMS_Acl extends Zend_Acl {
    
    private $_module;private $_userId;
    
    public function __construct($module = null, $userId = null) {
        $this->_module = $module;
        $this->_userId = 'u_'.$userId;
    }
    
     public function isAllowed($controller = null, $action = null, $object = null, $resource = null) {
        
         if(!is_null($controller)){
             $controller = 'c_'.$controller;
         }
         if(!is_null($object)){
             $controller = $controller.'_'.$object;
         }
         
         if(!is_null($resource)){
             $resource = $this->_user;
         }
         
         
         return parent::isAllowed($resource,$controller,$action);
    }
    
    
}
