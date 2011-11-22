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
abstract class HomeNet_Model_Subdevice_Abstract implements HomeNet_Model_Subdevice_Interface {

    public $settings = array();
    public $id = null;
    //public $node;
    public $device;
    public $model;
    public $position = 0;
    public $order = 0;
    public $room = null;
    public $name;
    public $permissions = '';

    public $driver = null;
    public $modelName = null;

    private $_house;
    private $_controls;

    private $_controlForm;

    /**
     * @var HomeNet_Model_DbTable_DatapointsAbstract
     */
    private $_datapointService;

    //public $units = '';

     public function __construct(array $config = array()) {
        //load data
        if (isset($config['data'])) {
            $this->fromArray($config['data']);
        }
        
        //load model
        if (isset($config['model']) && $config['model'] instanceof HomeNet_Model_SubdeviceModel_Interface) {
            $this->loadModel($config['model']);
        }



    }
    
     public function fromArray(array $array) {

        $vars = array('id', 'device', 'model', 'room', 'position', 'order', 'name', 'permissions');

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
            'device' => $this->device,
            'room' => $this->room,
            'model' => $this->model,
            'position' => $this->position,
            'order' => $this->order,
            'name' => $this->name,
            'settings' => $this->settings,
            'permissions' => $this->permissions);

        return $array;
    }

    public function getSetting($setting){
        if(isset($this->settings[$setting])){
            return $this->settings[$setting];
        }
        return null;
    }

    public function setSetting($setting, $value){
        $this->settings[$setting] = $value;
    }

    public function clearSetting($setting){
        unset($this->settings[$setting]);
    }

    /**
     * @param HomeNet_Model_SubdeviceModelInterface $model
     */
    public function loadModel(HomeNet_Model_SubdeviceModel_Interface $model) {
        $this->modelName = $model->name;
        $this->model = $model->id;
        $this->driver = $model->driver;
        //$this->type = $model->type;
        if(is_array($model->settings)){
        $this->settings = array_merge($this->settings, $model->settings);
        }
    }

    

    public function setHouse($house) {
        $this->_house = $house;
    }

    public function getHouse() {
        return $this->_house;
    }

    public function setRoom($room) {
        $this->room = $room;
    }

    public function getRoom() {
        return $this->room;
    }

    

    /**
     * Form for setting settings
     *
     * @return Zend_Form
     */
    public function getSettingsForm() {
        $form = new Zend_Form();

        $showControl = $form->createElement('checkbox', 'showControl');
        $showControl->setLabel('Show Controls: ');
        $form->addElement($showControl);

        $showGraphs = $form->createElement('checkbox', 'showGraphs');
        $showGraphs->setLabel('Show Graphs: ');
        $form->addElement($showGraphs);

        return $form;
    }

    /**
     * Form for user config
     *
     * @return Zend_Form
     */
    public function getConfigForm() {

        $sub = new Zend_Form_SubForm();

        $name = $sub->createElement('text', 'name');
        $name->setLabel('Name: ');
        if (empty($this->name)) {
            $name->setValue($this->modelName);
        } else {
            $name->setValue($this->name);
        }
        $name->addFilter('StripTags');
        $sub->addElement($name);

        $room = $sub->createElement('select', 'room');
        $room->setLabel('Room: ');
        $room->setValue($this->room);
        $sub->addElement($room);

        return $sub;
    }

    public function hasSummary() {
        return false;
    }

    public function getSummary() {
        return false;
    }

    public function hasLastDataPoint() {
        return false;
    }

    public function getLastDataPoint() {
        return array();
    }


    public function hasGraphs() {
        return false;
    }

    /**
     * Get graph array
     *
     * @return null
     */
    public function getGraphPresets() {
        return null;
    }

    /**
     * Get stats graph
     *
     * @return Zend_Form
     */
    public function getGraph(Zend_Date $start, Zend_Date $end, $width = 200, $height = 100) {
        return 'Graph Abstract';
    }

    /**
     * Get Datapoints
     *
     * @return Zend_Form
     */
    public function getDataPoints($start, $end, $density = null) {
        return '';
        //  throw new Zend_Exception('This subdevice doesn\'t have datapoints');
    }

    /**
     * Process config form
     *
     * @return Zend_Form
     */
    public function processConfigForm($values) {
        $this->name = $values['name'];
        $this->room = $values['room'];
    }

    /**
     * Does this subdevice have a control form
     *
     * @return boolean
     */
    public function addControl($name, $element, $elementOptions, $action, $actionOptions, $visible = true) {
        $this->_controls[$name] = array(
            'element' => $element,
            'elementOptions' => $elementOptions,
            'action' => $action,
            'actionOptions' => $actionOptions,
            'visible' => $visible);
    }

    /**
     * Does this subdevice have a control form
     *
     * @return boolean
     */
    public function hasControls() {
        return false;
    }

    /**
     * Build the controls for this subdevice
     *
     * @return null
     */
    public function buildControls() {
        return null;
    }

    /**
     * Form for user control form
     *
     * @return Zend_Form
     */
    public function getControlForm() {

        //die(debugArray($this->_controls));

        if(!empty($this->_controlForm)){
            return $this->_controlForm;
        }

        $this->buildControls();

        
        if (empty($this->_controls)) {
            return '';
        }

        $form = new CMS_Form(array('disableLoadDefaultDecorators' => true));
        //$form->setAction('/homenet/setup/step1');
        //$form->setDecorators(array('FormElements','Form'));
        $form->setDecorators(array('FormElements','Form'));
        $id = $form->createElement('hidden', 'subdevice');
        $id->setDecorators(array('ViewHelper'));
        $id->setValue($this->id);
        $form->addElement($id);
        
        foreach ($this->_controls as $name => $control) {
            if (!is_null($control['element'])) {


                $control['elementOptions']['id'] = $control['element'].$this->id;

                $element = $form->createElement($control['element'], $name, $control['elementOptions'] );
                //$element->setAttrib('id', $control['element'].$this->id);
                $element->removeDecorator('DtDdWrapper')
                     ->removeDecorator('HtmlTag');
            //->removeDecorator('Label');

                $form->addElement($element);
                if ($control['element'] != 'submit') {
                    $element2 = $form->createElement('submit', $name . 'Submit', array('Label' => 'Go'));
                    $element2->removeDecorator('DtDdWrapper');
                    $form->addElement($element2);
                }
            }
        }
        $form->removeDecorator('DtDdWrapper');
        $this->_controlForm = $form;
        return $form;
    }

    /**
     * Build the controls for this subdevice
     *
     * @return null
     */
    public function processControlForm($post){

        $form = $this->getControlForm();

        if (empty($form)) {
            return false;
        }

        if(!$form->isValid($post)){
            return false;
        }

        $values = $form->getValues($post);

        unset($post['subdevice']);

        $action = null;

        //die(debugArray($post));

        foreach($this->_controls as  $name => $control){

            if(!empty($post[$name])){

                if ($control['element'] != 'submit' && empty($post[$name.'Submit'])) {
                    continue;
                }

                if($control['element'] == 'submit'){
                    $values[$name] = true;
                }
                
                $class = 'HomeNet_Model_Action_'. ucfirst($control['action']);

                if(!class_exists($class)){
                    throw new HomeNet_Model_Exception('Can\'t find class '.$name);
                }

                $action = new $class($control['actionOptions']);
                $action->action($values[$name]);
                break;
            }
        }

        if(is_null($action)){
            throw new HomeNet_Model_Exception('Can\'t find matching action');
        }
        return true;

    }



    

    public function add() {
        $table = new HomeNet_Model_DbTable_Subdevices();
        $row = $table->createRow();

        //$row->node = $this->node;
        $row->device = $this->device;
        $row->model = $this->model;
        $row->order = $this->order;
        $row->name = $this->name;
        $row->position = $this->position;
        $row->room = $this->room;
        $row->settings = serialize($this->settings);

        $row->save();

        return $row->id;
    }

    public function update() {
        $table = new HomeNet_Model_DbTable_Subdevices();
        $row = $table->fetchRowById($this->id);

        //$row->node = $this->node;
        $row->device = $this->device;
        $row->model = $this->model;
        $row->order = $this->order;
        $row->name = $this->name;
        $row->position = $this->position;
        $row->room = $this->room;
        $row->settings = serialize($this->settings);

        $row->save();

        return $row->id;
    }

    public function delete() {
        $table = new HomeNet_Model_DbTable_Subdevices();
        $row = $table->fetchRowById($this->id);
        $row->delete();
    }


    /**
     * @return HomeNet_Model_DbTable_DatapointsAbstract
     */
    public function getDatapointService(){

        if(!empty($this->_datapointService)){
            return $this->_datapointService;
        }

        if (empty($this->settings['datatype'])) {
            throw new Zend_Exception($this->modelName.' '.$this->name.' doesn\'t have a datatype');
        }

        $this->_datapointService = new HomeNet_Model_Datapoint_Service();
        $this->_datapointService->setType($this->settings['datatype']);

//        $class = 'HomeNet_Model_DbTable_Datapoints' . ucfirst($this->settings['datatype']);
//
//        if (!class_exists($class)) {
//            throw new Zend_Exception('Invalid Datatype: ' . $class);
//        }
//
//        $this->_datapointService = new $class();
//
       return $this->_datapointService;
    }


    public function saveDatapoint($value, $timestamp) {
        throw new Zend_Exception('This subdevice doesn\'t save datapoints');
    }

}