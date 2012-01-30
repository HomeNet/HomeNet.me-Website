<?php

/*
 * Acl.php
 * 
 * Copyright (c) 2012 Matthew Doll <mdoll at homenet.me>.
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
class HomeNet_Model_Acl {
    
    private $_acl;
    private $_houseId;
    
    public function __construct($house) {
        
        if($house instanceof HomeNet_Model_House_Interface){
            $house = $house->id;
        }
        
        $aManager = Core_Model_Acl_Manager::getInstance();
        $this->_acl = $aManager->getUserAclCollection('homenet', $house);
      //  $this->_acl->removeRole(new CMS_Acl_Role_Group(1)); //remove everyone hack @todo find better solution
        $this->_houseId = $house;
    }
    
    
    
    
    public function isAllowed($controller, $action=null, $object = null){

        $cResource = new CMS_Acl_Resource_Controller($controller);

        $user = Core_Model_User_Manager::getUser();
        
        return $this->_acl->isAllowed($user, $cResource, $action);

//        if (!$this->acl->isAllowed($user, $cResource, $action)) {
//
//            $config = Zend_Registry::get('config');
//            
//             if ($user->id == $config->site->user->guest) { //if guest
//                return $this->_redirect($this->view->url(array(),'login'));// 
//            } else {                
//                return $this->_forward('noauth', 'error', 'core');
//            }
//        }
    }
    
    public function checkAccess($controller, $action=null){
        if(!$this->isAllowed($controller, $action)){
            throw new NotAllowedException("Permission denied to view house: {$this->_houseId}, controller: $controller, action: $action");
//          if (!$this->acl->isAllowed($user, $cResource, $action)) {
//            $config = Zend_Registry::get('config');
//            
//             if ($user->id == $config->site->user->guest) { //if guest
//                return $this->_redirect($this->view->url(array(),'login'));// 
//            } else {                
//                return $this->_forward('noauth', 'error', 'core');
//            }
//        }
        }
    }
}
