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
class HomeNet_Model_Packet_XmlRpc extends HomeNet_Model_Api {

    /**
     * Add Packet to log
     *
     * @param struct $params hashmap of values
     * @return boolean return true if valid
     */
    public function submit($params) {

        $defaults = array('apikey' => null, 'timestamp' => null, 'packet' => null);
        $params = $this->_prepareParams($params, $defaults);

        $manager = new HomeNet_Model_Packet_Manager();
        
        $apikeyService = new HomeNet_Model_Apikey_Service;
        $apikey = $apikeyService->getObjectById($params['apikey']);

       // $rawPacket = $packet;

        //return $decoded = htmlspecialchars(print_r($value,1));

        $packet = new HomeNet_Model_Packet();


        try {
            $packet->loadXmlRpc($params);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $decoded = htmlspecialchars(print_r($packet->getArray(), 1));
        //$decoded = chunk_split(bin2hex(base64_decode($value['packet'])),2,',');
        //file_put_contents(APPLICATION_PATH . '/packet.log', print_r($packet->toArray(),true)."\r\n Base64 decoded: ".$decoded."\r\n",FILE_APPEND);
        //  return $apikey->house .'-'. $packet->fromNode .'-'. $packet->fromDevice;
     
        //@todo move this to a service or manager
        
        
        try {
            $nodeService = new HomeNet_Model_Node_Service();

            $node = $nodeService->getObjectByHouseAddress($apikey->house, $packet->fromNode);
            //@todo validate this this is a  internetnode
            //update uplink's ip address if it changed
            $uplinkNode = $nodeService->getObjectById($node->uplink);

            if (isset($_SERVER['REMOTE_ADDR']) && ($uplinkNode->getSetting('ipaddress') != $_SERVER['REMOTE_ADDR'])) {
                $uplinkNode->setSetting('ipaddress', $_SERVER['REMOTE_ADDR']);
                $nodeService->update($uplinkNode);
            }
        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage());
        }


        $deviceService = new HomeNet_Model_Device_Service();

        //@throws NotFoundException || InvaildArgumentException //Pass these errors to the user
        $object = $deviceService->getObjectByHouseNodeaddressPosition($apikey->house, $packet->fromNode, $packet->fromDevice);
       // var_dump($object);
        $object->processPacket($packet);
        
        return true;
    }
//recompile2
}