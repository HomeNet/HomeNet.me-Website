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
class HomeNet_Model_Payload
{
    private $_type = self::BLANK;
    private $_data = '';

    const BLANK   = 0;
    const BYTE    = 1;
    const STRING  = 2;
    const INT     = 3;
    const FLOAT   = 4;
    const LONG    = 5;
    const BINARY  = 6;
    const BOOLEAN = 7;
    const RAW = 10;

    const ACK = 11;

    public function __construct($payload, $type = SELF::RAW) {


        if($type === null){
            if(is_array($payload)) {
                $type = $this->guessType($payload[0]);
            } else {
                $type = $this->guessType($payload);
            }

        }
        if(is_string($type)){
            switch ($type) {
                case 'BYTE':
                    $this->_type = self::BYTE;
                    break;
                case 'STRING':
                    $this->_type = self::STRING;
                    break;
                case 'INT':
                    $this->_type = self::INT;
                    break;
                case 'FLOAT':
                    $this->_type = self::FLOAT;
                    break;
                case 'LONG':
                    $this->_type = self::LONG;
                    break;
                case 'BINARY':
                    $this->_type = self::BINARY;
                    break;
                case 'BOOLEAN':
                    $this->_type = self::BOOLEAN;
                    break;
                 case 'RAW':
                    $this->_type = self::RAW;
                    break;
                default:
                    break;
            }
        }



        switch ($type) {
            case self::RAW:
                $this->_data = $payload;
                break;
            case self::BYTE:
                $this->setByte($payload);
                break;
            case self::STRING:
                $this->setString($payload);
                break;
            case self::INT:
                $this->setInt($payload);
                break;
            case self::FLOAT:
                $this->setFloat($payload);
                break;
            case self::LONG:
                $this->setLong($payload);
                break;
            case self::BINARY:
                $this->setBinary($payload);
                break;
            case self::BOOLEAN:
                $this->setBoolean($payload);
                break;
            default:
                break;
        }
    }

    public function setType($type){
        $this->_type = $type;
    }


    public function guessType($payload){
        if(is_int($payload)) {
            if($payload < 256) {
                return self::BYTE;
            }
            return self::INT;
        } elseif (is_string($payload)){
            return self::STRING;
        } elseif (is_float($payload)){
            return self::FLOAT;
        } elseif (is_long($payload)){
            return self::LONG;
        }

    }

    public function setByte($payload){
        $this->_type = self::BYTE;
        $this->_data = $this->pack('C*',$payload,'int');
        //$this->_data = pack('C*',$payload);

    }

    public function setString($payload){
        $this->_type = self::STRING;
        $this->_data = $payload;
    }

    public function setInt($payload){

        $this->_type = self::INT;
        $this->_data = $this->pack('v',$payload,'int');
    }

    public function setFloat($payload){

        $this->_type = self::FLOAT;
        $this->_data = $this->pack('f',$payload,'float');
    }

    public function setLong($payload){
        $this->_type = self::LONG;
        $this->_data = $this->pack('V',$payload,'int');
    }

    public function setBinary($payload){
        $this->_type = self::BINARY;
        $array = str_split($payload,8);
        
        $array = array_map("bindec", $array);
        //die(debugArray($array));

        $this->_data = $this->pack('C',$array,'int');
    }

    public function pack($code,$mixed,$type){
        
        if(is_array($mixed)){
            $bytes = '';
            foreach ($mixed as $value){
               settype($value, $type);
                $bytes .= pack($code, $value);
            }
            return $bytes;
        }
        settype($mixed, $type);
        return pack($code, $mixed);
    }


    public function setBoolean($payload){
        
    }

    public function getType(){
        return $this->_type;
    }

    public function getByte(){
        return unpack('C*',$this->_data);
    }

    public function getString(){
        return $this->_data;
    }

    public function getInt(){
        return unpack('v*',$this->_data);
    }

    public function getFloat(){
        return unpack('f*',$this->_data);
    }




    public function getLong(){
        return unpack('V*',$this->_data);
    }

    public function getBinary(){
        $array = unpack('C*',$this->_data);
        $array = array_map('decbin',$array);
        return implode('',$array);
    }

    public function getBoolean(){

    }

    public function getByteArray(){
        if(empty($this->_data)){
            return array();
        }

        return unpack('C*',$this->_data);
    }

    public function getByteString(){
        if(empty($this->_data)){
            return '';
        }

        return $this->_data;
    }

    public function getValue(){



          switch ($this->_type) {
            case self::BYTE:
                 return $this->getByte();
                break;
            case self::STRING:
                 return $this->getString();
                break;
            case self::INT:
                 return $this->getInt();
                break;
            case self::FLOAT:
                 return $this->getFloat();
                break;
            case self::LONG:
                 return $this->getLong();
                break;
            case self::BINARY:
                 return $this->getBinary();
                break;
            case self::BOOLEAN:
                return $this->getBoolean();
                break;
            default:
                break;
        }
    }
}