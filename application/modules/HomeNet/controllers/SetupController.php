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

    private $_step = 0;
    private $_house = null;
    private $_housesService = null;

    public function init() {
        
        $this->_step = $this->_getParam('id');
        

//        $ajaxContext = $this->_helper->getHelper('AjaxContext');
//        $ajaxContext->addActionContext('testConection', 'html')
//                ->addActionContext('testBase', 'html')
//                ->addActionContext('testLed', 'html')
//                ->initContext();
//
//        if ($this->getRequest()->isXmlHttpRequest()) {
//            return;
//        }
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
            if ($step !== null) {
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

        $step = $this->_step;

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
            $form->setAction($this->view->url(array('controller'=>'setup', 'action' => 'step', 'id' => 1), 'homenet-id'));
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        $house = new HomeNet_Model_House(array('data' => $values));
        $house->setSetting('setup', 2);
        $house = $this->_housesService->create($house);
        
        $messageService = new HomeNet_Model_Message_Service();
        $url = $this->view->url(array('controller'=>'setup', 'house' => $house->id, 'action' => 'index'), 'homenet-house-id');
        $messageService->add(HomeNet_Model_Message::NEWITEM, 'Congrates on starting your HomeNet. If you need to, you can return to the <a href="'.$url.'">Setup Wizard</a>', $user->id);
       

        //redirect to the next step
        return $this->_redirect($this->view->url(array('controller'=>'setup', 'house' => $house->id, 'action' => 'step', 'id' => 2), 'homenet-house-id'));
    }

    public function step2Action() {
        

        $form = new HomeNet_Form_Room($this->_house->regions); //limit regions to what was previously selected  

        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('controller'=>'setup', 'action' => 'step', 'id' => 2), 'homenet-house-id'));
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
        return $this->_redirect($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 3), 'homenet-house-id'));
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

            $this->view->apikey = $apikey->id;
        } else {
            $node = $keys[0];
            $this->view->apikey = $node->id;
        }

        $form = new CMS_Form();
        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('controller'=>'setup', 'action' => 'step', 'id' => 3), 'homenet-house-id'));
            $this->view->form = $form;
            return;
        }

        $nService = new HomeNet_Model_Node_Service();

        $ids = $nService->getUplinksByHouse($this->_house->id);//getIdsByHouseType($this->_house->id, HomeNet_Model_Node::INTERNET);

        if (empty($ids)) {
            $model = 0;
            $id = 0;
            $apikey = "";
            
            
            $remote = new HomeNet_Model_Remote_XmlRpc(array('ipaddress'=> $_SERVER['REMOTE_ADDR']));
            
            try {
                $apikey = $remote->getApikey();
            } catch (Exception $e) {
                $result = $e->getMessage();
            }

            if (empty($apikey)) {
                $this->view->error = "<strong>Missing Api Key</strong><br /> Please Re Enter your API Key into the HomeNet App ".$result;
                $this->view->form = $form;
                return;
            }

            $aService = new HomeNet_Model_Apikey_Service();
            if(!$aService->validate($apikey, $this->_house->id)) {
                $this->view->error = "<strong>Invalid Api Key</strong><br /> Please Re Enter your API Key into the HomeNet App";
                $this->view->form = $form;
                return;
            }


            try {
                $model = $remote->getNodeModel();
                $id = $remote->getNodeAddress();
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

            $nService = new HomeNet_Model_Node_Service();

            $node = $nService->newObjectFromModel(1);

            $node->address = $id;
            $node->house = $this->_house->id;
            $node->room = $room->id;

            $node->description = 'Auto created Node by HomeNet';
            $node->setSetting('ipaddress', $_SERVER['REMOTE_ADDR']);

            $nService->create($node);

            //save status
            $this->_house->setSetting('setup', 4);
            $this->_housesService->update($this->_house);
        }
        //redirect to the next step
        return $this->_redirect($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 4), 'homenet-house-id'));
    }

    public function step4Action() {
        
        $form = new HomeNet_Form_Node(2,$this->_house->id);  //only load model types 2
        $form->removeElement('uplink');
        $form->removeElement('address');
        $form->removeElement('description');
        $form->removeElement('room');

        $submit = $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $form->setAction($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 4), 'homenet-house-id'));
            $this->view->form = $form;
            return;
        }
        
        $nService = new HomeNet_Model_Node_Service();

        $values = $form->getValues();

        $ids = $nService->getUplinksByHouse($this->_house->id);//getIdsByHouseType($this->_house->id, HomeNet_Model_Node::INTERNET);
        if(!$ids){
            throw new Exception('Missing Internet ID');
        }
        $uplink = current($ids);

        $node = $nService->newObjectFromModel($values['model']);

        $node->fromArray($values);

        $node->description = 'Auto created Node by HomeNet Setup';

        
        $rooms = $this->_house->getRooms();
        $room = $rooms[0];
        
        $node->address = 1;
        $node->house = $this->_house->id;
        $node->room = $room->id;
        $node->uplink = $uplink;
        
        $node = $nService->create($node);

        $dService = new HomeNet_Model_Device_Service();
        $device = $dService->newObjectFromModel(16); // = Status Leds
        $device->house = $this->_house->id;
        $device->node = $node->id;
        $device->position = 1;
        $device->setRoom($room);

        $components = $device->getComponents(false);

        $components[0]->name = 'Red LED';
        $components[1]->name = 'Green LED';

        $dService->create($device);

       // $this->view->node = 1;


        //save status
        $this->_house->setSetting('setup', 5);
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 5), 'homenet-house-id'));
    }

    public function step5Action() {

        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectByHouseAddress($this->_house->id, 1);

        $this->view->code = $node->getCode();



        $form = new CMS_Form();
        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 5), 'homenet-house-id'));
            $this->view->form = $form;
            return;
        }


        //save status
        $this->_house->setSetting('setup', 6);
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 6), 'homenet-house-id'));
    }

    public function step6Action() {

        //decide whether to show continue or finish based on node type

        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectByHouseAddress($this->_house->id, 1);
        $plugin = $node->plugin;

        $form = new CMS_Form();

        if ($plugin != 'Arduino') {
            $form->addElement('submit', 'submit', array('label' => 'Next'));
        }
        $form->addElement('submit', 'finish', array('label' => 'Finish'));


        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('controller'=>'setup', 'action' => 'step', 'id' => 6), 'homenet-house-id'));
            $this->view->form = $form;
            return;
        }

        if (isset($_POST['finish'])) {
            $this->_house->clearSetting('setup');
            $this->_housesService->update($this->_house);

            return $this->_redirect($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'finish'), 'homenet-house-id'));
        }
        //send packet to get model
        //save status
        $this->_house->setSetting('setup', 7);
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 7), 'homenet-house-id'));
    }

    public function step7Action() {

        $form = new HomeNet_Form_Node(1, $this->_house->id); //limit model type
        $form->removeElement('uplink');
        $form->removeElement('address');
        $form->removeElement('description');
        $form->removeElement('room');

        $submit = $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }
        //save
        

        $values = $form->getValues();

        $nService = new HomeNet_Model_Node_Service();

        $ids = $nService->getUplinksByHouse($this->_house->id);//getIdsByHouseType($this->_house->id, HomeNet_Model_Node::INTERNET);
        $uplink = current($ids);

        //get room
        $rooms = $this->_house->getRooms();
        $room = $rooms[0];

       // $node->fromArray($values);
        $node = $nService->newObjectFromModel($values['model']);
        $node->description = 'Auto created Node by HomeNet';
        $node->address = 2;
        $node->house = $this->_house->id;
        $node->room = $room->id;
        $node->uplink = $uplink;

        $node = $nService->create($node);

        $dService = new HomeNet_Model_Device_Service();
        $device = $dService->newObjectFromModel(16); //= Status Leds

        $device->house = $this->_house->id;
        $device->node = $node->id;
        $device->position = 1;
        $device->setRoom($room);

        $subdevices = $device->getComponents();
        $subdevices[0]->name = 'Red LED';
        $subdevices[1]->name = 'Green LED';

//    public $subdevices = 0;
//    public $created = null;
//    public $settings = array();
        $dService->create($device);

        $dService = new HomeNet_Model_Device_Service();
        $device = $dService->newObjectFromModel(15); //15 = Simple LED

        $device->house = $this->_house->id;
        $device->node = $node->id;
        $device->position = 2;
        $device->setRoom($room);
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

        return $this->_redirect($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 8), 'homenet-house-id'));
    }

    public function step8Action() {
        //led code
        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectByHouseAddress($this->_house->id, 2);

        $this->view->code = $node->getCode();



        $form = new CMS_Form();
        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 8), 'homenet-house-id'));
            $this->view->form = $form;
            return;
        }


        //save status
        $this->_house->setSetting('setup', 9);
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 9), 'homenet-house-id'));
    }

    public function step9Action() {

        $form = new CMS_Form();

        $form->addElement('submit', 'finish', array('label' => 'Finish'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->setAction($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'step', 'id' => 9), 'homenet-house-id'));
            $this->view->form = $form;
            return;
        }

        $this->_house->clearSetting('setup');
        $this->_housesService->update($this->_house);

        return $this->_redirect($this->view->url(array('controller'=>'setup', 'house' => $this->_house->id, 'action' => 'finish'), 'homenet-house-id'));
    }

    public function finishAction() {
        //test remote led
    }
    
    public function testPacketAction(){
        die('todo');
         $form = new HomeNet_Form_Packet();
        $form->setAction('/homenet/index/test');

        if (!$this->getRequest()->isPost()) {
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }
        $values = $form->getValues();
        $values = $values['packet'];

        // die(print_r($values,1));

        $packet = new HomeNet_Model_Packet();
        $command = explode('|', $values['command']);
        $packet->buildUdp($values['fromNode'], $values['fromDevice'], $values['toNode'], $values['toDevice'], $command[0], new HomeNet_Model_Payload($values['payload'], $command[1]));
        //die($packet->getBase64Packet());

        $packet->sendXmlRpc($_SERVER['REMOTE_ADDR']);
        $this->view->sent = true;
        $this->view->form = $form;
    }

    public function testBaseAjaxAction() {
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

    public function testLedAjaxAction() {
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

    public function testConnectionAjaxAction() {
        $this->_helper->layout()->disableLayout();

        $result = "Unknown";       
        
        $remote = new HomeNet_Model_Remote_XmlRpc(array('ipaddress'=> $_SERVER['REMOTE_ADDR']));
            
            try {
                $result = $remote->testConnection();
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

