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
 * @subpackage Setup
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_SetupController extends Zend_Controller_Action {

    private $step = 0;
    private $_house = null;
    private $_housesService = null;

    public function init() {

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('testConection', 'html')
                ->addActionContext('testBase', 'html')
                ->addActionContext('testLed', 'html')
                ->initContext();

        if ($this->getRequest()->isXmlHttpRequest()) {
            return;
        }
    }

    public function indexAction() {

        $housesService = new HomeNet_Model_House_Service();
        $ids = $housesService->getHouseIdsByUser();

        //send new home net users to the welocme page
        if (count($ids) == 0) {
            return $this->_forward('welcome');
        }

        $houses = $housesService->getObjectsByIds($ids);

        $continueSetup = array();

        foreach ($houses as $house) {
            /* @var $house HomeNet_Model_HouseInterface */

            $step = $house->getSetting('setup');
            if (!is_null($step)) {
                $continueSetup[$house->id] = $house;
            }
        }

        if (empty($continueSetup)) {
            return $this->_forward('create');
        }

        $this->_setParam('houses', $continueSetup);

        return $this->_forward('continue');
    }

    //new user
    public function welcomeAction() {

    }

    //setup in progress
    public function continueAction() {

        $this->view->houses = $this->_getParam('houses');
    }

    //creating a new house
    public function createAction() {
        
    }

    public function stepAction() {

        //check that it's the right step for the house'
        $this->_housesService = new HomeNet_Model_House_Service();

        $step = $this->_getParam('step');

        if ($step > 1) {
            $this->_house = $this->_housesService->getObjectById($this->_getParam('house'));
        }

        //die(debugArray($this->_house));



        if (($step > 0) && ($step < 10)) {
            $this->_helper->viewRenderer('step' . $step);
            $action = 'step' . $step . 'Action';
            return $this->$action();
        }

        $this->_forward('error');
    }

    public function step1Action() {
        $form = new HomeNet_Form_House();
        $form->addElement('submit', 'submit', array('label' => 'Next'));


        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $form->setAction($this->view->url(array('action' => 'step', 'step' => 1), 'homenet-setup-index'));
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        $house = new HomeNet_Model_House(array('data' => $values));
        $house->setSetting('setup', 2);
        $house = $this->_housesService->create($house);

        //redirect to the next step
        return $this->_redirect($this->view->url(array('house' => $house->id, 'action' => 'step', 'step' => 2), 'homenet-setup'));
    }

    public function step2Action() {

        $form = new HomeNet_Form_Room();

        $region = $form->getElement('region');

        //die(debugArray($this->_house));

        $r = $this->_house->regions;

        //die(debugArray($this->house));

        $list = array('1' => 'First Floor',
            '2' => 'Second Floor',
            '3' => 'Third Floor',
            '4' => 'Forth Floor',
            '5' => 'Sixth Floor',
            'B' => 'Basement',
            'A' => 'Attic',
            'O' => 'Outdoors');
        foreach ($r as $value) {
            $region->addMultiOption($value, $list[$value]);
        }

        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('action' => 'step', 'step' => 2), 'homenet-setup'));
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        $roomsService = new HomeNet_Model_Room_Service();
        $room = new HomeNet_Model_Room(array('data' => $values));
        $room->house = $this->_house->id;
        // die(debugArray($room));
        $roomsService->create($room);

        //save status
        $this->_house->setSetting('setup', 3);
        $this->_housesService->update($this->_house);

        //redirect to the next step
        return $this->_redirect($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 3), 'homenet-setup'));
    }

    public function step3Action() {

        $housesService = new HomeNet_Model_House_Service();
        $rooms = $this->_house->getRooms();

        $aService = new HomeNet_Model_Apikey_Service();
        $keys = $aService->getObjectsByHouseUser($this->_house->id);

        // die(debugArray($keys[0]));
        //die(print_r($keys->toArray(),1));
        if (count($keys) == 0) {
            $apikey = $aService->createApikeyForHouse($this->_house->id);

            // die(debugArray($apikey));

            $this->view->apikey = $apikey->id;
        } else {
            $node = $keys[0];
            $this->view->apikey = $node->id;
        }

        $form = new CMS_Form();
        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('action' => 'step', 'step' => 3), 'homenet-setup'));
            $this->view->form = $form;
            return;
        }

        $nService = new HomeNet_Model_Node_Service();

        $ids = $nService->getInternetIdsByHouse($this->_house->id);



//        $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
//        $select->setIntegrityCheck(false)
//                ->where('house = ?', $this->_house->id)
//                ->join('homenet_nodes_internet', 'homenet_nodes_internet.id = homenet_nodes.id');
//
//        $rows = $table->fetchAll($select);
        // die(print_r($ids,1));

        if (empty($ids)) {


            $model = 0;
            $id = 0;

            $client = new Zend_XmlRpc_Client('http://' . $_SERVER['REMOTE_ADDR'] . ':2443/RPC2'); //,$client);
            $key = "";
            try {
                $key = $client->call('HomeNet.getApikey');
            } catch (Exception $e) {
                $result = $e->getMessage();
            }


            if (empty($key)) {
                $this->view->error = "<strong>Missing Api Key</strong><br /> Please Re Enter your API Key into the HomeNet App";
                $this->view->form = $form;
                return;
            }

            $aService = new HomeNet_Model_Apikey_Service();
            try {
                $aService->validate($key, $this->_house->id);
            } catch (Exception $e) {
                $result = $e->getMessage();
                $this->view->error = "<strong>Invalid Api Key</strong><br /> Please Re Enter your API Key into the HomeNet App";
                $this->view->form = $form;
                return;
            }


            try {
                $model = $client->call('HomeNet.getNodeModel');
                $id = $client->call('HomeNet.getNodeId');
            } catch (Exception $e) {
                $result = $e->getMessage();
            }

            if (!empty($result)) {
                $this->view->error = "<strong>Could not connect to your HomeNet.</strong><br /> Please check your firewall settings and port forwarding for port 2443";
                $this->view->form = $form;
                return;
            }

            $rooms = $this->_house->getRooms();

            foreach ($rooms as $room) {
                break;
            }

            //die(debugArray($room));

            $nService = new HomeNet_Model_Node_Service();

            $node = $nService->newObjectByModel(1);
//die(debugArray($node));
            //$node = new HomeNet_Model_InternetNode();
            //$node->model = $model;
            $node->node = $id;
            $node->house = $this->_house->id;
            $node->room = $room->id;

            $node->description = 'Auto created Node by HomeNet';
            $node->ipaddress = $_SERVER['REMOTE_ADDR'];
            //$node->addInternet($_SERVER['REMOTE_ADDR']);

            $nService->create($node);


            //save status
            $this->_house->setSetting('setup', 4);
            $this->_housesService->update($this->_house);
        }
        //redirect to the next step
        return $this->_redirect($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 4), 'homenet-setup'));
    }

    public function step4Action() {
        $type = array(1 => 'Wireless Sensor Node', 2 => 'Wired Base Station', 3 => 'Internet Node');

        $form = new HomeNet_Form_Node();
        $form->removeElement('uplink');
        $form->removeElement('node');
        $form->removeElement('description');
        //$sub = $form->getSubForm('node');

        $model = $form->getElement('model');

        $nmService = new HomeNet_Model_NodeModel_Service();
        $objects = $nmService->getObjectsByStatus(1);

        // die(debugArray($models));

        $models = array();
        foreach ($objects as $value) {
            if ($value->type == 2) {
                $models[$type[$value->type]][$value->id] = $value->name;
            }
        }

        $model->addMultiOptions($models);

        $uplink = $form->getElement('uplink');

        $submit = $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $form->setAction($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 4), 'homenet-setup'));
            $this->view->form = $form;
            return;
        }
        //save
        //get room
        $rooms = $this->_house->getRooms();

        foreach ($rooms as $room) {
            break;
        }

        $nService = new HomeNet_Model_Node_Service();

        $values = $form->getValues();

        $ids = $nService->getInternetIdsByHouse($this->_house->id);
        $uplink = current($ids);

        $node = $nService->newObjectByModel($values['model']);


        $node->fromArray($values);

        $node->description = 'Auto created Node by HomeNet';

        $node->node = 1;
        $node->house = $this->_house->id;
        $node->room = $room->id;
        $node->uplink = $uplink;


        $node = $nService->create($node);

        $dService = new HomeNet_Model_Device_Service();
        $device = $dService->getObjectByModel(9); //9 = Status Leds
//        public $id = null;
        $device->node = $node->id;
//    public $model = null;
        $device->position = 1;
//    public $subdevices = 0;
//    public $created = null;
//    public $settings = array();
        $subdevices = $device->getComponents();
        $subdevices[0]->name = 'Red LED';
        $subdevices[0]->room = $room->id;
        $subdevices[1]->name = 'Green LED';
        $subdevices[1]->room = $room->id;

        $dService->create($device);

        $this->view->node = 1;


        //save status
        $this->_house->setSetting('setup', 5);
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 5), 'homenet-setup'));
    }

    public function step5Action() {

        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectByHouseNode($this->_house->id, 1);

        $this->view->code = $node->getCode();



        $form = new CMS_Form();
        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 5), 'homenet-setup'));
            $this->view->form = $form;
            return;
        }


        //save status
        $this->_house->setSetting('setup', 6);
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 6), 'homenet-setup'));
    }

    public function step6Action() {

        //decide whether to show continue or finish based on node type

        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectByHouseNode($this->_house->id, 1);
        $driver = $node->driver;

        $form = new CMS_Form();

        if ($driver != 'HomeNet_Model_Node_Arduino') {
            $form->addElement('submit', 'submit', array('label' => 'Next'));
        }
        $form->addElement('submit', 'finish', array('label' => 'Finish'));


        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('action' => 'step', 'step' => 6), 'homenet-setup'));
            $this->view->form = $form;
            return;
        }

        if (isset($_POST['finish'])) {
            $this->_house->clearSetting('setup');
            $this->_housesService->update($this->_house);

            return $this->_redirect($this->view->url(array('house' => $this->_house->id, 'action' => 'finish'), 'homenet-setup'));
        }
        //send packet to get model
        //save status
        $this->_house->setSetting('setup', 7);
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 7), 'homenet-setup'));
    }

    public function step7Action() {

        $type = array(1 => 'Wireless Sensor Node', 2 => 'Wired Base Station', 3 => 'Internet Node');

        $form = new HomeNet_Form_Node();
        $form->removeElement('uplink');
        $form->removeElement('node');
        $form->removeElement('description');
        //$sub = $form->getSubForm('node');

        $model = $form->getElement('model');

        $nmService = new HomeNet_Model_NodeModel_Service();
        $objects = $nmService->getObjectsByStatus(1);

        // die(debugArray($models));

        $models = array();
        foreach ($objects as $value) {
            if ($value->type == 1) {
                $models[$type[$value->type]][$value->id] = $value->name;
            }
        }

        $model->addMultiOptions($models);

        $uplink = $form->getElement('uplink');

        $submit = $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }
        //save
        //get room
        $rooms = $this->_house->getRooms();

        foreach ($rooms as $room) {
            break;
        }
        $values = $form->getValues();

        $nService = new HomeNet_Model_Node_Service();

        $ids = $nService->getInternetIdsByHouse($this->_house->id);
        $uplink = current($ids);



        $node = $nService->newObjectByModel($values['model']);


        $node->fromArray($values);
        $node->description = 'Auto created Node by HomeNet';
        $node->node = 2;
        $node->house = $this->_house->id;
        $node->room = $room->id;
        $node->uplink = $uplink;

        $node = $nService->create($node);

        $dService = new HomeNet_Model_Device_Service();
        $device = $dService->getObjectByModel(9); //9 = Status Leds
//        public $id = null;
        $device->node = $node->id;
//    public $model = null;
        $device->position = 1;

        $subdevices = $device->getComponents();
        $subdevices[0]->name = 'Red LED';
        $subdevices[0]->room = $room->id;
        $subdevices[1]->name = 'Green LED';
        $subdevices[1]->room = $room->id;

//    public $subdevices = 0;
//    public $created = null;
//    public $settings = array();
        $dService->create($device);

        $dService = new HomeNet_Model_Device_Service();
        $device = $dService->getObjectByModel(6); //6 = Simple LED
//        public $id = null;
        $device->node = $node->id;
//    public $model = null;
        $device->position = 2;
//    public $subdevices = 0;
//    public $created = null;
//    public $settings = array();
        $subdevices = $device->getComponents();
        $subdevices[0]->name = 'Simple LED';
        $subdevices[0]->room = $room->id;
        $dService->create($device);

        //save status
        $this->_house->setSetting('setup', 8);
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 8), 'homenet-setup'));
    }

    public function step8Action() {
        //led code
        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectByHouseNode($this->_house->id, 2);

        $this->view->code = $node->getCode();



        $form = new CMS_Form();
        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 8), 'homenet-setup'));
            $this->view->form = $form;
            return;
        }


        //save status
        $this->_house->setSetting('setup', 9);
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 9), 'homenet-setup'));
    }

    public function step9Action() {

        $form = new CMS_Form();

        $form->addElement('submit', 'finish', array('label' => 'Finish'));


        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('house' => $this->_house->id, 'action' => 'step', 'step' => 9), 'homenet-setup'));
            $this->view->form = $form;
            return;
        }


        $this->_house->clearSetting('setup');
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('house' => $this->_house->id, 'action' => 'finish'), 'homenet-setup'));
    }

    public function finishAction() {
        //test remote led
    }

    public function testBaseAction() {
        $this->_helper->layout()->disableLayout();

        $packet = new HomeNet_Model_Packet();
        if ($this->_getParam('command') == 'on') {
            $packet->buildUdp(4095, 0, 1, 1, HomeNet_Model_Packet::ON, new HomeNet_Model_Payload(0, HomeNet_Model_Payload::BYTE));
        } else {
            $packet->buildUdp(4095, 0, 1, 1, HomeNet_Model_Packet::OFF, new HomeNet_Model_Payload(0, HomeNet_Model_Payload::BYTE));
        }
        try {
            $packet->sendXmlRpc($_SERVER['REMOTE_ADDR']);
        } catch (Exception $e) {
            $this->view->error = '<strong>Could not connect to your HomeNet.</strong><br />Make sure the HomeNetApp is running, If problem persists, check your firewall settings and port forwarding for port 2443';
            return;
        }

        $this->view->success = "<strong>Packet Sent: {$this->_getParam('command')}</strong>";
    }

    public function testLedAction() {
        $this->_helper->layout()->disableLayout();
        $packet = new HomeNet_Model_Packet();

        $payload = new HomeNet_Model_Payload(0, HomeNet_Model_Payload::BYTE);

        if ($this->_getParam('command') == 'off') {
            $packet->buildUdp(4095, 0, 2, 2, HomeNet_Model_Packet::OFF, $payload);
        } else {
            $packet->buildUdp(4095, 0, 2, 2, HomeNet_Model_Packet::ON, $payload);
        }
        try {
            $packet->sendXmlRpc($_SERVER['REMOTE_ADDR']);
        } catch (Exception $e) {
            $this->view->error = '<strong>Could not connect to your HomeNet.</strong><br /> Make sure the HomeNetApp is running, If problem persists, check your firewall settings and port forwarding for port 2443';
            return;
        }

        $this->view->success = "<strong>Packet Sent</strong>";
    }

    public function errorAction() {
        // action body
    }

    public function testConnectionAction() {
        $this->_helper->layout()->disableLayout();

        $result = "Unknown";

        $ipaddress = $_SERVER['REMOTE_ADDR'];

        $client = new Zend_XmlRpc_Client('http://' . $ipaddress . ':2443/RPC2'); //,$client);

        try {
            $result = $client->call('HomeNet.testConnection', "test");
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        if ($result == 'true') {
            $this->view->success = "<strong>Test Successful.</strong><br /> Your firewall is properly configured";
        } else {
            $this->view->error = "<strong>Could not connect to your HomeNet.</strong><br /> Please check your firewall settings and port forwarding for port 2443";
        }
    }

}

