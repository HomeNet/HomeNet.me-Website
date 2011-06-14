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
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package HomeNet
 * @subpackage Packet
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Packet {

    const BATTERYLEVEL    = 0x03;
    const MEMORYFREE      = 0x04;
    const PING            = 0x33;
    const PONG            = 0x3E;
    const ACK             = 0x11;
    const GETNODEID       = 0x21;
    const SETNODEID       = 0x22;
    const GETDEVICE       = 0x23;
    const SETDEVICE       = 0x24;

    const AUTOSENDSTART   = 0xB1;
    const AUTOSENDSTOP    = 0xB2;

    const ON              = 0xC0;
    const OFF             = 0xC1;
    const LEVEL           = 0xC2;
    const CLEAR           = 0xC3;

    const GETVALUE        = 0xD0;
    const GETBYTE         = 0xD1;
    const GETSTRING       = 0xD2;
    const GETINT          = 0xD3;
    const GETFLOAT        = 0xD4;
    const GETLONG         = 0xD5;
    const GETBINARY       = 0xD6;
    const GETBOOLEAN      = 0xD7;

    const SETVALUE        = 0xE0;
    const SETBYTE         = 0xE1;
    const SETSTRING       = 0xE2;
    const SETINT          = 0xE3;
    const SETFLOAT        = 0xE4;
    const SETLONG         = 0xE5;
    const SETBINARY       = 0xE6;
    const SETBOOLEAN      = 0xE7;

    const VALUE      = 0xF0;
    const BYTE       = 0xF1;
    const STRING     = 0xF2;
    const INT        = 0xF3;
    const FLOAT      = 0xF4;
    const LONG       = 0xF5;
    const BINARY     = 0xF6;
    const BOOLEAN    = 0xF7;

    /**
     * Date/Time Packet Received
     *
     *  @var Zend_Date
     */
    public $timestamp;

    /**
     * @var string
     */
    public $apikey;

    /**
     * @var int
     */
    public $settings;

    /**
     * @var int
     */
    public $fromNode;

    /**
     * @var int
     */
    public $fromDevice;
    public $toNode;
    public $toDevice;
    public $ttl = null;
    public $id;
    public $command;
    public $reply = null;

    /**
     * @var HomeNet_Model_Payload
     */
    public $payload;
    public $checksum;

    public function __toString() {
        return print_r($this->getArray(), 1);
    }

    public function buildUdp($fromNode, $fromDevice, $toNode, $toDevice, $command,HomeNet_Model_Payload $payload = null){

        $this->settings = 0;
        $this->fromNode = $fromNode;
        $this->fromDevice = $fromDevice;
        $this->toNode = $toNode;
        $this->toDevice = $toDevice;
        $this->ttl = null;
        $this->id = rand(0, 255);
        $this->command = $command;
        $this->payload = $payload;
    }

    /**
     * format packet as an array for database
     *
     * @return array
     */
    public function getArray() {

        if(empty($this->timestamp)){
            $this->timestamp = new Zend_Date();
        }

        return Array('received' => $this->timestamp->get('YYYY-MM-dd HH:mm:ss'),
            'apikey' => $this->apikey,
            'settings' => $this->settings,
            'from_node' => $this->fromNode,
            'from_device' => $this->fromDevice,
            'to_node' => $this->toNode,
            'to_device' => $this->toDevice,
            'ttl' => $this->ttl,
            'id' => $this->id,
            'command' => $this->command,
            'reply' => $this->reply,
            'payload' => $this->payload->getByteString(),
            'checksum' => $this->checksum);
    }

    /** 
     * Save packet database
     */
    public function save() {
        // HomeNet_Model_DbTable_Packets
        // echo $this->_packet;

        $table = new HomeNet_Model_DbTable_Packets();
        $table->insert($this->getArray());
        //die($this->_packet);
    }

    /**
     * decodes a base64 packet in to a HomeNet_Packet object
     *
     * @param string $base64
     * @return HomeNet_Packet
     */
    public function loadBase64Packet($base64) {

        $binaryString = base64_decode($base64);

        $binaryArray = unpack('C*', $binaryString);

        $this->settings = $binaryArray[2] & 0x0F;
        $this->fromNode = ($binaryArray[3] << 4) | ($binaryArray[4] >> 4);
        $this->fromDevice = $binaryArray[4] & 0x0F;
        $this->toNode =   ($binaryArray[5] << 4) | ($binaryArray[6] >> 4);
        $this->toDevice = $binaryArray[6] & 0x0F;
        //$packet->ttl = null;
        $this->id = $binaryArray[7];
        $this->command = $binaryArray[8];
        if (($binaryArray[2] & 128) == 1) {
            $this->reply = $binaryArray[9];
        }

        $this->checksum = (array_pop($binaryArray)) | (array_pop($binaryArray) << 8);
        $payload = substr($binaryString, 8, -2);
        if(is_string($payload)){
            $this->payload = new HomeNet_Model_Payload($payload, HomeNet_Model_Payload::RAW);
        }//set type
        
        /*
         *    const BYTE       = 0xF1;
    const STRING     = 0xF2;
    const INT        = 0xF3;
    const FLOAT      = 0xF4;
    const LONG       = 0xF5;
    const BINARY     = 0xF6;
    const BOOLEAN    = 0xF7;
         * 
         */

        $type = HomeNet_Model_Payload::RAW;

        switch ($this->command) {
                case self::BYTE :
                    $type = HomeNet_Model_Payload::BYTE;
                    break;
                case self::STRING :
                    $type = HomeNet_Model_Payload::STRING;
                    break;
                case self::INT :
                    $type = HomeNet_Model_Payload::INT;
                    break;
                case self::FLOAT :
                    $type = HomeNet_Model_Payload::FLOAT;
                    break;
                case self::LONG :
                    $type = HomeNet_Model_Payload::LONG;
                    break;
                case self::BINARY :
                    $type = HomeNet_Model_Payload::BINARY;
                    break;
                case self::BOOLEAN :
                    $type = HomeNet_Model_Payload::BOOLEAN;
                    break;
            }
            $this->payload->setType($type);


    }
    
    public function getBase64Packet() {
        $binaryArray = Array();
        
        $binaryArray[0] = 0; // length
        $binaryArray[1] = $this->settings;//settings
        $binaryArray[2] = ($this->fromNode >> 4);//fromNode top 8
        $binaryArray[3] = (($this->fromNode & 0x0F) << 4) | ($this->fromDevice & 0x0F);//fromDevice
        $binaryArray[4] = ($this->toNode >> 4);//toNode
        $binaryArray[5] = (($this->toNode & 0x0F) << 4) | ($this->toDevice & 0x0F);//toDevice
        $binaryArray[6] = rand(0,255);//id
        $binaryArray[7] = $this->command;//command

       //die(debugArray($this->payload));
        //$binaryArray[8];//payload
        //if(!is_null($this->payload)){
            $payload = $this->payload->getByteArray();
        
            $binaryArray = array_merge($binaryArray, $payload);
       // }

        $binaryArray[0] = count($binaryArray)+2; //update length

        $crc = 0;

        foreach($binaryArray as $value){
            $crc = $this->_crc16_update($crc,$value);
        }

        $binaryArray[] = ($crc >> 8) & 0xFF;
        $binaryArray[] = $crc & 0xFF;

        $bin = '';
        foreach($binaryArray as $value){
            $bin .= pack('C',$value);
        }

        return base64_encode($bin);
    }

    public function loadXmlRpc($array) {
        $this->loadBase64Packet($array['packet']);
        //$this->received = $array['received'];
        $this->apikey = $_GET['apikey'];
        $this->timestamp = new Zend_Date($array['timestamp'], Zend_Date::ISO_8601);
    }
    
    public function sendXmlRpc($ip){
      
    $client = new Zend_XmlRpc_Client('http://' . $ip . ':2443/xmlrpc');//'http://131.247.40.156:8081/RPC2'
  //  $client->setSkipSystemLookup();
    
    $xmlrpc = array();
    $xmlrpc['packet'] = $this->getBase64Packet();
    $date = new Zend_Date();
    $xmlrpc['timestamp'] = $date->get(Zend_Date::ISO_8601);
    
    $result = $client->call('HomeNet.packet', new Zend_XmlRpc_Value_Struct($xmlrpc));
    return $result;
    }

    public function processPacket()
    {

    }


    private function  _crc16_update($crc, $a)
    {
        $crc ^= $a;
        for ($i = 0; $i < 8; ++$i) {
            if ($crc & 1) {
                $crc = ($crc >> 1) ^ 0xA001;
            } else {
                $crc = ($crc >> 1);
            }
        }
        return $crc;
    }
}