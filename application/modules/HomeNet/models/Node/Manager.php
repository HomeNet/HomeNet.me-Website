<?php

/*
 * Manager.php
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
 * Description of Manager
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class HomeNet_Model_Node_Manager {
    
    public static function getNodesByHouse($house){
        
       // if(!isset($_SESSION['HomeNet']['houses'])){
            $service = new HomeNet_Model_Node_Service();
            return $service->getObjectsByHouse($house);
        //}
        
       // return $_SESSION['HomeNet']['houses'];
    }
    
    public static function getNodeByHouseAddress($house, $address){
         $service = new HomeNet_Model_Node_Service();
         return $service->getObjectByHouseAddress($house, $address);
       // $validHouses = 
        //validate acl for house
        
        
//        if(isset($_SESSION['HomeNet']['houses']) && is_array($_SESSION['HomeNet']['houses']) && isset($_SESSION['HomeNet']['houses'][$house])){
//            return $_SESSION['HomeNet']['houses'][$house];
//        }
//        
//        return null;
    }
    
}