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
 * @package HomeNet
 * @subpackage Device
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
abstract class HomeNet_Model_Device_Abstract implements HomeNet_Model_Device_Interface {

    public $id;
    public $status;
    public $house;
    public $node;
    public $model;
    public $position;
    public $components = 0;
    public $created;
    public $fixed;
    public $settings = array();

    public $plugin;
    public $model_name;

    /**
     * @var HomeNet_Model_House_Interface 
     */
    protected $_house;
    protected $room;
    protected $_node;
    protected $_components;

    const STATUS_LIVE = 1;
    const STATUS_TRASHED = 0;


    /**
     * @param array $config
     */
    public function __construct(array $config = array()) {
        if (isset($config['data'])) {
            $this->fromArray($config['data']);
        }
        //load model
        if (isset($config['model']) && $config['model'] instanceof HomeNet_Model_DeviceModel_Interface) {
            $this->loadModel($config['model']);
        }
    }

    /**
     * @param array $array
     */
    public function fromArray(array $array) {

        $vars = array('id', 'status', 'house', 'node', 'model', 'position', 'components', 'created' , 'plugin', 'fixed', 'model_name', 'room');

        foreach ($array as $key => $value) {
            if (in_array($key, $vars)) {
                $this->$key = $value;
            }
        }

        if(!empty($array['settings']) && is_array($array['settings'])){
            $this->settings = array_merge($this->settings, $array['settings']);
        }
    }

    /**
     * @return array
     */
    public function toArray() {

        $array = array(
            'id' => $this->id,
            'status' => $this->status,
            'house' => $this->house,
            'node' => $this->node,
            'model' => $this->model,
            'position' => $this->position,
            'components' => $this->components,
            'created' => $this->created,
            'fixed' => $this->fixed,
            'settings' => $this->settings,
            'plugin' => $this->plugin,
            'model_name' => $this->model_name);
        return $array;
    }

    /**
     * @param HomeNet_Model_ComponentModelInterface $model
     */
    public function loadModel(HomeNet_Model_DeviceModel_Interface $model) {
        $class = 'HomeNet_Plugin_Device_'.$model->plugin.'_Device';
        if (!($this instanceof $class)) {
            throw new Zend_Exception('Wrong driver ' . $model->plugin . ' Loaded');
        }
        
        //@todo rewrite this, it seems messy

        $this->plugin = $model->plugin;
        $this->model_name = $model->name;
        $this->model = $model->id;

        if(!empty($model->settings) && is_array($model->settings)){
            $this->settings = array_merge($this->settings, $model->settings);
        }
        
        $this->_components = array();
        if (!empty($model->components)) {
            $cService = new HomeNet_Model_Component_Service();
            
            $array = $model->components;
            
            foreach($array as $value){
                $component = $cService->newObjectFromModel($value['model']);
                $component->fromArray($value);//load other values
                $this->_components[] = $component;
            }
        }
    }

    public function getComponents($search = true) {

        if(!isset($this->_components) && empty($this->id)){
            $this->_components = array();
            
            return $this->_components;
        }
        
        if(!isset($this->_components) && $search){
            $service = new HomeNet_Model_Component_Service();
            $this->_components = $service->getObjectsByDevice($this->id);
        } else {
            //$this->_components = array();
        }

        //die(debugArray($this->_components));

        return $this->_components;
    }

    public function setPosition($position) {
        $this->position = $position;
    }

    public function getPosition() {
        return $this->position;
    }

//    public function setNode($node) {
//        $this->node = $node;
//    }
//
//    public function getNode() {
//        return $this->node;
//    }

    public function setHouse(HomeNet_Model_House_Interface $house) {
        $this->_house = $house;
    }

    public function getHouse() {
        
        if($this->_house === null){
            $this->_house = HomeNet_Model_House_Manager::getHouseById($this->house);
        }
        
        return $this->_house;
    }

    public function setRoomId($room) {
        $this->room = $room;
    }

    public function getRoomId() {
        return $this->room;
    }

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
     * Generate code for the node
     *
     * @return string
     */
    public function getIncludes() {

        $array = array();

        $includes = $this->getSetting('includes');
        if(is_array($includes)){
            foreach($includes as $value){
                $array[$value] = $value;
            }
        }
        //die(debugArray($this->settings));//

        return $array;
    }

     public function generateCustomCode() {
        return '';
    }

    public function getDeviceDriver(){
        return $this->getSetting('driver');
    }

    public function getDeviceVariable(){
        return strtolower($this->getSetting('driver')).$this->position;
    }

    public function getDeviceOptions() {
        return 'stack';
    }


    public function generateCodeInit() {
        return '';
    }

    public function generateCodeSetup() {
        return '';
    }

    public function generateCodeLoop() {
        return '';
    }

    public function getSchedule() {
        return array();
    }

    public function getInterrupts() {
        return array();
    }

    

    /**
     * Generate code for the node
     *
     * @return CMS_Form
     */
    public function getConfigForm() {
        $rooms = array();

        $rooms['0'] = 'None';

        $regions = array();

        //die($this->getHouse());

        $housesService = new HomeNet_Model_House_Service();
        $house = $housesService->getObjectByIdWithRooms($this->house);

        $r = $housesService->getRegionsById($this->house);

        
        $regions = $housesService->getRegions($r);
        
//        foreach($r as $region){
//            $regions[$region['id']] = $region['name'];
//        }

        foreach($house->rooms as $room){
                $region = $regions[$room->region];
                $rooms[$region][$room->id] = $room->name;
            }

        $form = new Zend_Form();

        $components = $this->getComponents();

      //  debugArray($this->settings);
       //die(debugArray($components));

        foreach ($components as $position => $component) {

            $sub = $component->getConfigForm();

            
            if (!empty($component)) {
                
                //set default name
                $name = $sub->getElement('name');//
                //die($this->settings['component'.$position.'name']);
                if(!empty($this->settings['component'.$position.'name'])){
                    $name->setValue($this->settings['component'.$position.'name']); 
                } 
               
                $room = $sub->getElement('room');
                $room->setMultiOptions($rooms);

                if(empty($component->room)){
                    $room->setValue($this->getRoomId());
                }

                $sub->setLegend('Component ' . $position . ': ' . $component->name);

                $form->addSubForm($sub, 'component' . $position);
            }
        }

        //die(debugArray($sub));

        return $form;
    }

    public function processConfigForm($values) {
        $components = $this->getComponents();
        foreach ($components as $key => $value) {
            if (!empty($value) && !empty($values['component' . $key])) {
                $value->processConfigForm($values['component' . $key]);
            }
        }
    }

    public function getControlForm() {
        return null;
    }

    





//    public function add() {
//        $table = new HomeNet_Model_DbTable_Devices();
//        $row = $table->createRow();
//
//        $row->node = $this->node;
//        $row->model = $this->model;
//        $row->position = $this->position;
//        $row->settings = serialize($this->settings);
//
//        $row->save();
//
//        //create components
//        $id = $row->id;
//
//        foreach ($this->components as $key=>$value) {
//            if (!empty($value)) {
//                $value->device = $id;
//                $value->position = $key;
//                $value->add();
//            }
//        }
//
//        return $id;
//    }
//
//    public function update() {
//        $table = new HomeNet_Model_DbTable_Devices();
//        $row = $table->fetchRowById($this->id);
//
//        $row->node = $this->node;
//        $row->model = $this->model;
//        $row->position = $this->position;
//        $row->settings = serialize($this->settings);
//
//        $row->save();
//
//        $id = $row->id;
//
//        foreach ($this->components as $value) {
//            if (!empty($value)) {
//                $value->device = $id;
//                $value->update();
//            }
//        }
//
//        return $id;
//    }
//
//    public function delete() {
//        $table = new HomeNet_Model_DbTable_Devices();
//        $row = $table->fetchRowById($this->id);
//        $row->delete();
//
//        foreach ($this->components as $value) {
//            if (!empty($value)) {
//                $value->delete();
//            }
//        }
//    }

   

    public function processPacket(HomeNet_Model_Packet $packet){
        
        $supportedCommands = array(HomeNet_Model_Packet::BINARY, HomeNet_Model_Packet::BOOLEAN, HomeNet_Model_Packet::FLOAT, HomeNet_Model_Packet::BYTE, HomeNet_Model_Packet::INT, HomeNet_Model_Packet::LONG);
        if(!in_array($packet->command, $supportedCommands)){
            throw new UnexpectedValueException('this device doesn\'t can\'t process command '.print_r($packet->command,1));
        }

        $value = $packet->payload->getValue();

        //die(debugArray($value));
/*
        if(empty($value[$this->order+1])){
            throw new Zend_Exception('Invalid value at position '.(string)$this->order);
        }
*/
        $components = $this->getComponents();
       // var_dump($components);
      //  exit;
        foreach ($components as $object) {
            if (!empty($object) && !empty($value[$object->order+1])) { //->get('YYYY-MM-dd HH:mm:ss')
                
                $object->saveDatapoint($packet->timestamp, $value[$object->order+1]);
            }
        }
    }


}