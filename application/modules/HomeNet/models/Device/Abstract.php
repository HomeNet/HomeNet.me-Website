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

    public $id = null;
    public $house = null;
    public $node = null;
    public $model = null;
    public $position = null;
    public $components = 0;
    public $created = null;
    public $settings = array();


    public $plugin = null;
    public $modelName = null;


    protected $_house;
    protected $_room;
    protected $_components;



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

        $vars = array('id', 'node', 'model', 'position', 'components', 'created' , 'driver', 'modelName');

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
            'node' => $this->node,
            'model' => $this->model,
            'position' => $this->position,
            'components' => $this->components,
            'created' => $this->created,
            'settings' => $this->settings,
            'plugin' => $this->plugin,
            'modelName' => $this->modelName);
        return $array;
    }

    /**
     * @param HomeNet_Model_ComponentModelInterface $model
     */
    public function loadModel(HomeNet_Model_DeviceModel_Interface $model) {
        if (!($this instanceof $model->driver )) {
            throw new Zend_Exception('Wrong driver ' . $model->driver . ' Loaded');
        }

      //  die(debugArray($model));

        $this->driver = $model->driver;
        $this->modelName = $model->name;
        $this->model = $model->id;

        if(!empty($model->settings) && is_array($model->settings)){
            $this->settings = array_merge($this->settings, $model->settings);
        }

        if (!empty($model->settings['components'])) {
            $smService = new HomeNet_Model_ComponentModel_Service();
            $this->_components = $smService->getComponentsByIds($model->settings['components']);
        }
    }

    /**
     * @param HomeNet_Model_Device_Abstract
     */
    public function getDriver() {
         if(empty($this->driver)){
            throw new HomeNet_Model_Exception('Missing Device Driver');
        }

        if(!class_exists($this->driver)){
            throw new HomeNet_Model_Exception('Device Driver '.$this->driver.' Doesn\'t Exist');
        }

        return new $this->driver(array('data' => $this->toArray()));
    }

    public function getComponents($search = true) {

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

    public function setNode($node) {
        $this->node = $node;
    }

    public function getNode() {
        return $this->node;
    }

    public function setHouse($house) {
        $this->_house = $house;
    }

    public function getHouse() {
        return $this->_house;
    }

    public function setRoom($room) {
        $this->_room = $room;
    }

    public function getRoom() {
        return $this->_room;
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
     * @return Zend_Form
     */
    public function getConfigForm() {
        $rooms = array();

        $rooms['0'] = 'None';

        $regions = array();

        //die($this->getHouse());

        $housesService = new HomeNet_Model_House_Service();
        $house = $housesService->getObjectByIdWithRooms($this->getHouse());

        $r = $housesService->getHouseRegionNames($this->getHouse());

        foreach($r as $region){
            $regions[$region['id']] = $region['name'];
        }

        foreach($house->rooms as $room){
                $region = $regions[$room->region];
                $rooms[$region][$room->id] = $room->name;
            }

        $form = new Zend_Form();

        $components = $this->getComponents();

        //debugArray($this->settings);
       // die(debugArray($components));

        foreach ($components as $position => $subdevice) {

            $sub = $subdevice->getConfigForm();

            
            if (!empty($subdevice)) {
                
                //set default name
                $name = $sub->getElement('name');//
                if(empty($subdevice->name) && !empty($this->settings['subdevice'.$position.'name'])){
                    $name->setValue($this->settings['subdevice'.$position.'name']); 
                } 
               
                $room = $sub->getElement('room');
                $room->setMultiOptions($rooms);

                if(empty($subdevice->room)){
                    $room->setValue($this->getRoom());
                }

                $sub->setLegend('Component ' . $position . ': ' . $subdevice->name);

                $form->addSubForm($sub, 'subdevice' . $position);
            }
        }

        //die(debugArray($sub));

        return $form;
    }

    public function processConfigForm($values) {
        $components = $this->getComponents();
        foreach ($components as $key => $value) {
            if (!empty($value) && !empty($values['subdevice' . $key])) {
                $value->processConfigForm($values['subdevice' . $key]);
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

        $supportedCommands = array(HomeNet_Model_Packet::BINARY, HomeNet_Model_Packet::FLOAT, HomeNet_Model_Packet::BYTE, HomeNet_Model_Packet::INT, HomeNet_Model_Packet::LONG);
        if(!in_array($packet->command, $supportedCommands)){
            throw new Zend_Exception('this device doesn\'t can\'t process command '.print_r($packet->command,1));
        }

        $value = $packet->payload->getValue();

        //die(debugArray($value));
/*
        if(empty($value[$this->order+1])){
            throw new Zend_Exception('Invalid value at position '.(string)$this->order);
        }
*/
        $components = $this->getComponents();
        foreach ($components as $subdevice) {
            if (!empty($subdevice) && !empty($value[$subdevice->order+1])) {
                $subdevice->saveDatapoint($value[$subdevice->order+1], $packet->timestamp->get('YYYY-MM-dd HH:mm:ss'));
            }
        }
    }


}