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
    
    
     public function isAllowed($role  = null, $resource = null, $privilege = null) {
       
         
         if(!$this->hasResource($resource)){
             $this->addResource($resource);
         }
         
         return parent::isAllowed($role, $resource, $privilege);
    }
    
    public function addResource($resource, $parent = null) {
        
        if($resource instanceof CMS_Acl_Parent_Interface){
            $parent = $resource->getParent();
            if(!$this->hasResource($parent)){
                $this->addResource($parent);
            }
        } 
        
        parent::addResource($resource, $parent);
    }
    
    /**
     * I was annyoed that hasResource was not defined in Zend Acl
     * 
     * @param Zend_Acl_Resource $resource
     * @return Boolean 
     */
    public function hasResource($resource){
        return $this->has($resource);
    }
    
    
}
