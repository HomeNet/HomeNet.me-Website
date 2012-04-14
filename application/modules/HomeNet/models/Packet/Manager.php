<?php

class HomeNet_Model_Packet_Manager
{
    /**
     * @var HomeNet_Model_Packet
     */
    private $_packet;

    public function  __construct(HomeNet_Model_Packet $packet = null) {
        $this->_packet = $packet;
    }

    public function parse(){
        //check the api key
        try {
            $table = new HomeNet_Model_DbTable_Apikeys();
            $apikey = $table->fetchRowById($this->apikey);

            $table = new HomeNet_Model_DbTable_Devices();
            $device = $table->fetchRowByHouseNodeDevice($apikey->house, $this->_packet->fromNode, $this->_packet->fromDevice);

            if(!$device && (!$device->command ==  $this->_packet->command)){
                throw new Zend_Exception('Cannot find matching device');
            }
            $this->saveDatapoint();
            /*
            $table = new HomeNet_Model_DbTable_Components();
            $component = $table->fetchAllByDevice($this->fromDevice);*/

        } catch (Zend_Exception $e){
            return $e->message();
        }
        return 'true';
    }

    /**
     * @throws Zend_Exception
     */
    public function validateApikey(){
        $apikey = new HomeNet_Model_DbTable_Apikeys();
        $apikey->validate($this->_packet->apikey);
    }

    public function getDevice(){
        $device = new HomeNet_Model_DbTable_Devices();
        $device->fetchHouseNodeDevice();
    }

    public function saveDatapoint(){
        switch ($this->_packet->command) {

            case HomeNet_Model_Packet::BYTE:
                $this->saveByte();
                break;
            case HomeNet_Model_Packet::STRING:
                //$this->saveString();
                break;
            case HomeNet_Model_Packet::INT:
                $this->saveInt();
                break;
            case HomeNet_Model_Packet::FLOAT:
                $this->saveFloat();
                break;
            case HomeNet_Model_Packet::LONG:
                //$this->ping();
                break;
            case HomeNet_Model_Packet::BOOLEAN:
                //$this->ping();
                break;
            case HomeNet_Model_Packet::BINARY:
                //$this->ping();
                break;
            case HomeNet_Model_Packet::VALUE:
                //$this->ping();
                break;
            default:
                break;
        }
    }

    public function ping(){
        $this->_packet->payload->getString();
    }

    public function saveInt(){

    }

    public function saveFloat(){

    }

    public function saveByte(){

    }



}

