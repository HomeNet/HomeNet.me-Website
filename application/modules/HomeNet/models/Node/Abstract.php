<?php

/*
 * Abstract.php
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
 * Base for HomeNet Node Drivers
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
abstract class HomeNet_Model_Node_Abstract implements HomeNet_Model_Node_Interface {

    public $id;
    public $status;
    public $house;
    public $room;
    public $address;
    public $model;
    public $uplink = 0;
    public $description = '';
    public $created;
    public $settings = array();

   // public $internet = false;
  //  public $ipaddress;
  //  public $status = 1;
  //  public $direction = 1;

    
    public $model_name;
    public $model_settings = array();
    public $plugin;
    public $type;
    public $max_devices;

    protected $_devices;
    
    //types
    const INTERNET = 3;
    const BASESTATION = 2;
    const SENSOR = 1;
    
    const STATUS_LIVE = 1;
    const STATUS_TRASHED = 0;

    public function  __construct(array $config = array()) {

        //load model
        if (isset($config['model']) && $config['model'] instanceof HomeNet_Model_NodeModel_Interface) {
            $this->loadModel($config['model']);
        }

        if(isset($config['data'])){
            $this->fromArray($config['data']);
        }

    }

   public function fromArray(array $array) {

        $vars = array('id', 'status', 'address', 'model', 'uplink', 'house', 'room', 'description', 'created', 'model_name','max_devices', 'driver', 'type', 'ipaddress', 'direction');

        foreach ($array as $key => $value) {
            if (in_array($key, $vars)) {
                $this->$key = $value;
            }
        }
        
       //merge in model settings
        if(!empty($array['model_settings']) && is_array($array['model_settings'])){
            $this->settings = array_merge($this->settings, $array['model_settings']);
        }
        
        
        if(!empty($array['settings']) && is_array($array['settings'])){
            $this->settings = array_merge($this->settings, $array['settings']);
        }
    }

    /**
     * @return array
     */
    public function toArray() {

        return array(
            'id' => $this->id,
            'status' => $this->status,
            'address' => $this->address,
            'model' => $this->model,
            'uplink' => $this->uplink,
            'house' => $this->house,
            'room' => $this->room,
            'description' => $this->description,
            'created' => $this->created,
            'settings' => $this->settings
           );
    }

    public function loadModel(HomeNet_Model_NodeModel_Interface $model){

        $this->model_name = $model->name;
        $this->plugin = $model->plugin;
        $this->model = $model->id;
        $this->type = $model->type;
        $this->settings = array_merge($this->settings, $model->settings);
    }

    public function getDevices(){

        //die(debugArray($this->settings));

        if(!isset($this->_devices)){
            $dService = new HomeNet_Model_Device_Service();
            $this->_devices = $dService->getObjectsByNode($this->id);
        }
        return $this->_devices;
    }

    public function addDevice($position, HomeNet_Model_Device_Abstract $device) {
        $this->_devices[$position] = $device;
    }

//    public function loadRow(HomeNet_Model_Node $row){
//
//        $this->id     = $row->id;
//        $this->node = $row->node;
//        $this->model   = $row->model;
//        $this->settings = unserialize($row->settings);
//
//        //if internet node
//        if(!empty($row->ipaddress)){
//            $this->ipaddress = $row->ipaddress;
//            $this->internet = true;
//        }
//
//       /* $this->order  = $row->order;
//        $this->name   = $row->name;
//        $this->units  = $row->units;*/
//    }

//    public function loadSettings($settings) {
//        $this->settings = $settings;
//    }
//
//    public function getSettings() {
//        return $this->settings;
//    }
 



    public function getSetting($setting){
        if(!empty($this->settings[$setting])){
            return $this->settings[$setting];
        }
        return null;
    }

    public function setSetting($setting, $value){
        $this->settings[$setting] = $value;
    }

     /**
     * get max number of ports
     *
     * @return int
     */
    abstract public function getMaxPorts();


    public function canGenerateCode(){
        return false;
    }

    public function getNodeDriver(){
        throw new HomeNet_Model_Exception('Can\'t generate code fro thsi device');
    }



    /**
     * Generate code for the node
     *
     * @return string
     */
    public function getCode() {

        $licence = '/*
 * Auto Generated Code for HomeNet
 *
 * Copyright (c) 2011 HomeNet.
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
 */';
        return $licence;
    }

    

    /**
     * Generate code for the node
     *
     * @return Zend_Form
     */
    public function getSetupForm() {

        /**
         * @todo find a beter way to do this
         */

        if(!empty($_POST['nodeId'])){
            $this->id = $_POST['nodeId'];
        }

        if (empty($this->id)) {
            $table = new HomeNet_Model_DbTable_Nodes();
            $this->id = $table->fetchNextId($this->house);
        }
       // $this->id = 50;

        $form = new HomeNet_Form_Node();
        $sub = $form->getSubForm('node');
        $id = $sub->getElement('node');
        $id->setValue($this->id);
        
        $table = new HomeNet_Model_DbTable_Nodes();
        $rows = $table->fetchAllInternetNodes();

        $uplink = $sub->getElement('uplink');

        foreach($rows as $value){
            $uplink->addMultiOption($value->id, $value->id);
        }


        return $form;
    }

    public function sendPacket(HomeNet_Model_Packet $packet){
        throw new HomeNet_Model_Exception('This driver can\'t send packets');
    }
    
}