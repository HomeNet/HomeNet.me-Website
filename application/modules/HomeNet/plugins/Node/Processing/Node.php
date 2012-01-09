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
 * Processing Installer
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class HomeNet_Plugin_Node_Processing_Node extends HomeNet_Model_Node_Abstract {

    public $type = self::INTERNET;

    /**
     * get max number of ports
     *
     * @return int
     */
    public function getMaxPorts(){
        return 0;
    }

    public function sendPacket(HomeNet_Model_Packet $packet){

        //die(debugArray($this));
        
        $ipaddress = $this->getSetting('ipaddress');

        if($ipaddress === null){
            throw new HomeNet_Model_Exception('Internet Node not loaded');
        }
        
        $packet->sendXmlRpc($ipaddress);
    }

}