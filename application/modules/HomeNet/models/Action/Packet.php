
<?php

/*
 * Packet.php
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
class HomeNet_Model_Action_Packet extends HomeNet_Model_Action_Abstract {

    function action($value) {

        //die('called');

        if (!empty($this->_options['toNode']) && !empty($this->_options['toDevice'])) {
            /**
             * @todo give another way to define packet
             */
        }

        if (empty($this->_options['command'])) {
            throw new HomeNet_Model_Exception('Missing Command');
        }

        $command = $this->_options['command'];
        
        $commands = include(dirname(__FILE__).'/../../data/commands.php');
        
        if(empty($commands[$command]['type'])){
            throw new HomeNet_Model_Exception('Invalid Command');
        }
        
        $payload = null;

        if (isset($this->_options['payload'])) {

            if($this->_options['payload'] instanceof HomeNet_Model_Payload){
                $payload = $this->_options['payload'];
            } else {
                $payload = new HomeNet_Model_Payload($this->_options['payload'],$commands[$command]['type']);
            }
        } else {
            if (isset($this->_options['callback'])) {
                $value = call_user_func($this->_options['callback'], $value);

            }


            $payload = new HomeNet_Model_Payload($value,$commands[$command]['type']);
        }

        //die(debugArray($value));

        $dService = new HomeNet_Model_Device_Service();
        $object = $dService->getObjectByIdWithNode($this->_options['device']);

        if (empty($object)) {
            throw new HomeNet_Model_Exception('Can\'t find device ' . $this->_options['device']);
        }

        //$row->uplink
        if (empty($object->uplink)) {
            throw new HomeNet_Model_Exception('Node missing Uplink',404);
        }

        $packet = new HomeNet_Model_Packet();
        /**
         * @todo offer other packet types
         */
        $packet->buildUdp(4095, 0, $object->address, $object->device, $command, $payload);

        //die(debugArray($packet->getArray()));
        //get upload link if it has one or it self
        $nService = new HomeNet_Model_Node_Service();
    //    var_dump($this->_options);
    
        //throw new Exception('Trace this');
        $node = $nService->getObjectByHouseAddress($object->house, $object->uplink);
        $node->sendPacket($packet);
    }

}