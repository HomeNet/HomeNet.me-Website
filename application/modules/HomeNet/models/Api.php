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
 * Description of XmlRpc
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
abstract class HomeNet_Model_Api {

    protected function _validateApikey($apikey) {
      
        $service = new HomeNet_Model_Apikey_Service();

        if(!$service->validate($apikey)){
            throw new InvalidArgumentException('Invalid Api Key');
        } 
    }
    
    protected function _prepareParams(array $params, $defaults = array()){
        
        foreach($defaults as $key => $value){
            if(array_key_exists($key, $params)){
                $defaults[$key] = $params[$key];
            }
        }
        return $defaults;
    }

        
}