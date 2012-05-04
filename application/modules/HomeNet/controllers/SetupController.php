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

    private $_wizard;
    private $_house;
    private $_housesService;

    public function init() {

        $this->_wizard = $this->_getParam('wizard');
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

            $step = $house->getSetting('wizard');
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

//    //creating a new house
//    public function createAction() {
//        
//    }

    private function getNextWizardUrl() {
        if ($this->_wizard !== null) {
            $pos = array_search($this->_wizard, $this->WIZARD);
            if (empty($this->WIZARD[$pos + 1])) {
                throw new OutOfRangeException('Item Not Found in Wizard List');
            }
            $next = $this->WIZARD[$pos + 1];

            //save this to the house
            if ($next !== end($this->WIZARD)) {
                $this->_house->setSetting('wizard', $next);
            } else {
                $this->_house->clearSetting('wizard');
            }
            $this->_housesService->update($this->_house);
            return $this->view->url(array('controller' => 'setup', 'house' => $this->_house->id, 'action' => 'wizard', 'wizard' => $next), 'homenet-house-setup');
        }
    }

    private $WIZARD = array('house', 'room', 'controller', 'base', 'baseCode', 'baseTest', 'remote', 'remoteCode', 'remoteTest', 'finished');

    //'network','networkConfig'
    //private $currentWizard;

    public function wizardAction() {

        //check that it's the right step for the house'
        $this->_housesService = new HomeNet_Model_House_Service();

        if ($this->_wizard == null) {
            $this->_wizard = 'house'; //set first action
        }

        if ($this->_wizard !== 'house') {

            $acl = new HomeNet_Model_Acl($this->_getParam('house'));
            $acl->checkAccess('setup', 'add');

            $this->_house = $this->_housesService->getObjectById($this->_getParam('house'));

            //test action on house
            if ($this->_wizard !== $this->_house->getSetting('wizard')) {
                $this->_forward('error');
                return;
            }
        }

        //die(debugArray($this->_house));



        if (in_array($this->_wizard, $this->WIZARD)) {

            $this->_helper->viewRenderer('wizard' . ucfirst($this->_wizard));
            $action = $this->_wizard . 'Wizard';
            return $this->$action();
        }

        $this->_forward('error');
    }

    protected function houseWizard() {
        $form = new HomeNet_Form_House();
        $form->addElement('submit', 'submit', array('label' => 'Next'));


        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        //create house
        $this->_house = new HomeNet_Model_House(array('data' => $values));
        $this->_house = $this->_housesService->create($this->_house);

        //add default user permissions
        $houseUserService = new HomeNet_Model_HouseUser_Service();
        $user = Core_Model_User_Manager::getUser();
        $houseUserService->add($this->_house->id, $user->id, HomeNet_Model_HouseUser::PERMISSION_ADMIN);

        //add message        
        $messageService = new HomeNet_Model_Message_Service();
        $url = $this->view->url(array('controller' => 'setup', 'house' => $this->_house->id, 'action' => 'index'), 'homenet-house');
        $messageService->add(HomeNet_Model_Message::NEWITEM, 'Congrates on starting your HomeNet. If you need to, you can return to the <a href="' . $url . '">Setup Wizard</a>', $user->id);



        //redirect to the next step
        return $this->_redirect($this->getNextWizardUrl());
    }

    public function roomWizard() {


        $form = new HomeNet_Form_Room($this->_house->regions); //limit regions to what was previously selected  

        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        $roomsService = new HomeNet_Model_Room_Service();
        $room = new HomeNet_Model_Room(array('data' => $values));
        $room->house = $this->_house->id;
        // die(debugArray($room));
        $roomsService->create($room);



        //redirect to the next step
        return $this->_redirect($this->getNextWizardUrl());
    }

    public function controllerWizard() {

        $housesService = new HomeNet_Model_House_Service();
        $rooms = $this->_house->getRooms();

        $aService = new HomeNet_Model_Apikey_Service();
        $keys = $aService->getObjectsByHouseUser($this->_house->id, Core_Model_User_Manager::getUser()->id);

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
            $this->view->form = $form;
            return;
        }

        $nService = new HomeNet_Model_Node_Service();

        $ids = $nService->getUplinksByHouse($this->_house->id); //getIdsByHouseType($this->_house->id, HomeNet_Model_Node::INTERNET);

        if (empty($ids)) {
            $model = 0;
            $id = 0;
            $apikey = "";

            if (APPLICATION_ENV == 'production') { //@todo really need to setup a mock object to test this
                $remote = new HomeNet_Model_Remote_XmlRpc(array('ipaddress' => $_SERVER['REMOTE_ADDR']));

                try {
                    $apikey = $remote->getApikey();
                } catch (Exception $e) {
                    $result = $e->getMessage();
                }

                if (empty($apikey)) {
                    $this->view->error = "<strong>Missing Api Key</strong><br /> Please Re Enter your API Key into the HomeNet App " . $result;
                    $this->view->form = $form;
                    return;
                }

                $aService = new HomeNet_Model_Apikey_Service();
                if (!$aService->validate($apikey, $this->_house->id)) {
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
            }


            $rooms = $this->_house->getRooms();

            foreach ($rooms as $room) {
                break;
            }

            //add Core Networks
            $networkService = new HomeNet_Model_Network_Service();
            $ip = $networkService->add(1, $this->_house->id);
            $serial = $networkService->add(2, $this->_house->id);

            $ipaddress = '127.0.0.1'; //localhost
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            }

            //add homenet app 
            $nodeService = new HomeNet_Model_Node_Service();
            $nodeService->add(1, $this->_house->id, $room->id, array($ip->id => $ipaddress, $serial->id => null), 'Local Controller', null, null, 1);

//            $node = $nService->newObjectFromModel(1);
//
//            $node->address = $id;
//            $node->house = $this->_house->id;
//            $node->room = $room->id;
//
//            $node->description = 'Auto created Node by HomeNet';
//            $node->setSetting('ipaddress', $_SERVER['REMOTE_ADDR']);
//
//            $nService->create($node);
        }
        //redirect to the next step
        return $this->_redirect($this->getNextWizardUrl());
    }

//        public function networkWizard() {
//        
//
//        $form = new HomeNet_Form_NetworkTypes(); //limit regions to what was previously selected  
//
//        $form->addElement('submit', 'submit', array('label' => 'Next'));
//
//        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
//            $this->view->form = $form;
//            return;
//        }
//
//        $values = $form->getValues();
//
//        $service = new HomeNet_Model_Network_Service();
//        
//        
//        foreach($values['network-types'] as $value){
//            $service->add($value,$this->_house->id);
//        }
//
//        //redirect to the next step
//        return $this->_redirect($this->getNextWizardUrl());
//    }
    public function baseWizard() {

        $form = new HomeNet_Form_Node(2, $this->_house->id);  //only load model types 2
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

        $nService = new HomeNet_Model_Node_Service();

        $values = $form->getValues();

        $ids = $nService->getUplinksByHouse($this->_house->id); //getIdsByHouseType($this->_house->id, HomeNet_Model_Node::INTERNET);
        if (!$ids) {
            throw new Exception('Missing Internet ID');
        }
        $uplink = current($ids);


        //get addtional networks
        $nodeModelService = new HomeNet_Model_NodeModel_Service();
        $model = $nodeModelService->getObjectById($values['model']);



        $networks = array_flip(array_diff($model->network_types, array(1, 2)));

        $networkService = new HomeNet_Model_Network_Service();
        foreach ($networks as $key => $value) {
            $networks[$key] = $networkService->add($key, $this->_house->id)->id;
        }

        $rooms = $this->_house->getRooms();

        foreach ($rooms as $room) {
            break;
        }



        //add homenet app 
        $nodeService = new HomeNet_Model_Node_Service();
        $nodeService->add($values['model'], $this->_house->id, $room->id, null, 'Base Station Node', null, 1, 2);


//        $node = $nService->newObjectFromModel($values['model']);
//
//        $node->fromArray($values);
//
//        $node->description = 'Auto created Node by HomeNet Setup';
//
//        
//        $rooms = $this->_house->getRooms();
//        $room = $rooms[0];
//        
//        $node->address = 1;
//        $node->house = $this->_house->id;
//        $node->room = $room->id;
//        $node->uplink = $uplink;
//        
//        $node = $nService->create($node);
        // $this->view->node = 1;

        return $this->_redirect($this->getNextWizardUrl());
    }

    public function networkConfigWizard() {

        die('network config');
        $objects = $service->getObjectsByHouse($this->_house->id);

        if (empty($objects)) {
            throw new UnexpectedValueException('No networks found');
        }

        $first = $objects[0];

        $form = $first->getConfigForm();

        //$form = new CMS_Form; //get config form  

        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        die('save config');



        //redirect to the next step
        return $this->_redirect($this->getNextWizardUrl());
    }

    public function baseCodeWizard() {

        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectByHouseAddress($this->_house->id, 2);

        $this->view->code = $node->getCode();



        $form = new CMS_Form();
        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $this->view->form = $form;
            return;
        }

        return $this->_redirect($this->getNextWizardUrl());
    }

    public function baseTestWizard() {

        //decide whether to show continue or finish based on node type

        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectByHouseAddress($this->_house->id, 2);
        $plugin = $node->plugin;

        $form = new CMS_Form();

        if ($plugin != 'Arduino') {
            $form->addElement('submit', 'submit', array('label' => 'Next'));
        }
        $form->addElement('submit', 'finish', array('label' => 'Finish'));


        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $this->view->form = $form;
            return;
        }

        if (isset($_POST['finish'])) {
            $this->_house->clearSetting('setup');
            $this->_housesService->update($this->_house);

            return $this->_redirect($this->view->url(array('controller' => 'setup', 'house' => $this->_house->id, 'action' => 'wizard', 'wizard' => 'finished'), 'homenet-house-setup'));
        }
        //send packet to get model

        return $this->_redirect($this->getNextWizardUrl());
    }

    public function remoteWizard() {

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

        $nodeService = new HomeNet_Model_Node_Service();

       // $ids = $nodeService->getUplinksByHouse($this->_house->id); //getIdsByHouseType($this->_house->id, HomeNet_Model_Node::INTERNET);
      //  $uplink = current($ids);

        //get room
        $rooms = $this->_house->getRooms();
        $room = $rooms[0];
        
       $node = $nodeService->add($values['model'], $this->_house->id, $room->id, null, 'Sensor Node', null, 1, 3);

        // $node->fromArray($values);
//        $node = $nodeService->newObjectFromModel($values['model']);
//        $node->description = 'Auto created Node by HomeNet';
//        $node->address = 2;
//        $node->house = $this->_house->id;
//        $node->room = $room->id;
//        $node->uplink = $uplink;
//
//        $node = $nodeService->create($node);
//
//        $dService = new HomeNet_Model_Device_Service();
//        $device = $dService->newObjectFromModel(16); //= Status Leds
//
//        $device->house = $this->_house->id;
//        $device->node = $node->id;
//        $device->position = 1;
//        $device->setRoom($room);
//
//        $components = $device->getComponents();
//        $components[0]->name = 'Red LED';
//        $components[1]->name = 'Green LED';
//
////    public $components = 0;
////    public $created = null;
////    public $settings = array();
//        $dService->create($device);

        $dService = new HomeNet_Model_Device_Service();
        $device = $dService->newObjectFromModel(15); //15 = Simple LED

        $device->house = $this->_house->id;
        $device->node = $node->id;
        $device->position = 2;
        $device->setRoomId($room->id);
//    public $components = 0;
//    public $created = null;
//    public $settings = array();
        $components = $device->getComponents();
        $components[0]->name = 'Simple LED';
        $components[0]->room = $room->id;
        $dService->create($device);

        return $this->_redirect($this->getNextWizardUrl());
    }

    public function remoteCodeWizard() {
        //led code
        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectByHouseAddress($this->_house->id, 3);

        $this->view->code = $node->getCode();

        $form = new CMS_Form();
        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $this->view->form = $form;
            return;
        }

        return $this->_redirect($this->getNextWizardUrl());
    }

    public function remoteTestWizard() {

        $form = new CMS_Form();

        $form->addElement('submit', 'finish', array('label' => 'Finish'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $this->view->form = $form;
            return;
        }

        return $this->_redirect($this->getNextWizardUrl());
    }

    public function finishedWizard() {
       // $acl = new HomeNet_Model_Acl($this->_getParam('house'));
       // $acl->checkAccess('setup', 'add');
        //test remote led
    }

    public function testPacketAction() {
        //die('todo');
        $form = new HomeNet_Form_Packet();


        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }
//        $values = $form->getValues();
//        $values = $values['packet'];
//
//        // die(print_r($values,1));
//
//        $packet = new HomeNet_Model_Packet();
//        $command = explode('|', $values['command']);
//        $packet->buildUdp($values['fromNode'], $values['fromDevice'], $values['toNode'], $values['toDevice'], $command[0], new HomeNet_Model_Payload($values['payload'], $command[1]));
//        //die($packet->getBase64Packet());
//
//        $packet->sendXmlRpc($_SERVER['REMOTE_ADDR']);
//        $this->view->sent = true;
//        $this->view->form = $form;
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
        
        if(APPLICATION_ENV == 'testing'){
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        }

        $remote = new HomeNet_Model_Remote_XmlRpc(array('ipaddress' => $_SERVER['REMOTE_ADDR']));

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

