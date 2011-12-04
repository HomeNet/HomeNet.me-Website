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
class Core_Plugin_Widget_Login_Widget extends Core_Model_Plugin_Widget {
    
    
    private function _template(){
        
    }
    
    public function render(){
       // $service = new Content_Model_Section_Service();
       // $objects = $service->getObjects();
        $vars = array();
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $user = Core_Model_User_Manager::getUser();
            $vars['name'] = $user->name;
            $vars['identity'] = $auth->getIdentity();
        }
        
        return $this->renderPartial('login.phtml',$vars, dirname(__FILE__));
    }
    
    
   
    
    
}