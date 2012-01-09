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
 * @subpackage Index
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * 
 */
class HomeNet_IndexController extends Zend_Controller_Action {

    private $_house;
    
    public function init() {
        $this->_house = $this->_getParam('house');
    }

    public function indexAction() {

        $houseIds = HomeNet_Model_House_Manager::getHouseIds();
        
        //user has houses, send to home page
      //  if($this->_house !== null)
        
        if ($this->_house !== null) {
            die('house not null');
            $this->_helper->viewRenderer('house');
            return $this->houseAction();
        }
        
        
        if (count($houseIds) > 0) {
            $this->_forward('home');
            return;
        }

        //user has no house, display welcome page
        $this->_helper->_layout->setLayout('one-column');
    }

    public function sendPacketAction() {
        // action body
    }

    public function homeAction() {

        
        
        $houseIds = HomeNet_Model_House_Manager::getHouseIds();
        $user = Core_Model_User_Manager::getUser();
        
        //@todo validate house
        //$house = HomeNet_Model_House_Manager::getHouse($this->_house);
        
        $messageService = new HomeNet_Model_Message_Service();        
        //$this->view->alerts = $messageService->getObjectsByHouseOrUser($this->_house, $user);
        $this->view->alerts = $messageService->getObjectsByHousesOrUser($houseIds, $user->id);
        
        //$this->view->alerts = $mService->getObjectsByHousesOrUser($_SESSION['HomeNet']['houses'], $_SESSION['User']['id']);
    }

    public function houseAction() {
        // action body
    }
    public function testAction() {
        
                //$packet = new HomeNet_Model_Packet();
        //$packet->loadXmlRPc(array('packet'=>"DQIAQA/yBfAxMjP+jw==", 'received'=> '2010-11-18 10:23:00'));
        //print_r($packet->getArray());
        // $packet->save();
        //echo 'success';
        
        
//        $apikeys = new HomeNet_Model_Apikey_Service();
//        $apikeys->validate('96b1e1ad70e96197aff8c1b48f1106767e7ecfe7');
//
//                die('done');

      // // $apikeys->getApikeyById($id);
        
//        $messages = new HomeNet_Model_Message_Service();
//
//        $nodes = new HomeNet_Model_Node_Service();
//        $NodeModels = new HomeNet_Model_NodeModel_Service();
//
//
//        $devices = new HomeNet_Model_Device_Service();
//        $deviceModels = new HomeNet_Model_DeviceModel_Service();
//
//        $subdevices = new HomeNet_Model_Component_Service();
//        $subdeviceModels = new HomeNet_Model_ComponentModel_Service();
     //   $dService = new HomeNet_Model_Datapoint_Service();
      //  $dService->add('byte',15,42,'2011-03-11 08:32:21');
        //15 	2011-02-23 04:32:21 	42
        

        //die(debugArray($this->view->getScriptPaths()));
          $packet = new HomeNet_Model_Packet();
          // $packet->apikey = '5e6e55b3d965d47def3b18f5cc95b2414a37ee5a';
          //$packet->timestamp = new Zend_Date();
         /* $_GET['apikey'] = '5e6e55b3d965d47def3b18f5cc95b2414a37ee5a';
          $xml = array(
          'timestamp' => '2011-02-23T03:57:10-05:00',
          'packet' => 'DAIAQ//wAfM0AI4Y'
          );

          $packet->loadXmlRpc($xml);
          $packet->save(); */
        //$packet->loadBase64Packet('DgIAQv/wAPRCoN+A2Ik=');
      
            $packet->timestamp = new Zend_Date();
          $packet->settings = 2;
          $packet->fromNode = 4;
          $packet->fromDevice = 3;
          $packet->toNode =   4095;
          $packet->toDevice = 0;
          //$packet->ttl = null;
          $packet->id = 55;
          $packet->command = 0xF3;
          $packet->payload = new HomeNet_Model_Payload(50, HomeNet_Model_Payload::RAW);

          $packet->payload->setType(HomeNet_Model_Payload::BYTE);
        
        // $packet->payload->setType(HomeNet_Model_Payload::BYTE);
        //$packet->save();
//die(debugArray($packet->payload->getValue()));
        //
        //DAIAQ//wAfMrAL4Q

       

          $service = new HomeNet_Model_Device_Service();

          $driver = $service->getObjectByHouseNodeDevice(8, 5, 3);
          $driver->processPacket($packet);
         die('passed');

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

}