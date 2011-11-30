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
 * @subpackage Room
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * 
 * 
 */
class HomeNet_RoomController extends Zend_Controller_Action {

    public function init() {
        $this->view->house = $this->_getParam('house');
        $this->view->room = $this->_getParam('room');
        $this->view->region = $this->_getParam('region');
    }

    public function indexAction() {
        return $this->_forward('info');
    }

    public function infoAction() {
        $hService = new HomeNet_Model_House_Service();
        $house = $hService->getObjectById($this->view->house);
        $room = $house->getRoomById($this->view->room);

        $this->view->roomName = $room->name;

        $sService = new HomeNet_Model_Component_Service();
        $this->view->subdevices = $sService->getObjectsByRoom($this->view->room);
    }

    public function controlAction() {
        $hService = new HomeNet_Model_House_Service();
        $house = $hService->getObjectById($this->view->house);
        $room = $house->getRoomById($this->view->room);

        $this->view->roomName = $room->name;

        $sService = new HomeNet_Model_Component_Service();
        $this->view->subdevices = $sService->getObjectsByRoom($this->view->room);

        if (!$this->getRequest()->isPost()) {
            
            return;
        }

        //die(debugArray($this->view->subdevices));

        $subdevice = $_POST['subdevice'];
        
        if(!empty($this->view->subdevices[$subdevice])){
            $this->view->subdevices[$subdevice]->processControlForm($_POST);
        }


    }

    public function addAction() {
        $form = new HomeNet_Form_Room();

        $form->addElement('submit', 'submit', array('label' => 'Add'));

        $regionElement = $form->getElement('region');

        $hService = new HomeNet_Model_House_Service();
        $house = $hService->getObjectById($this->view->house);

        $regions = $hService->getHouseRegionNames($this->view->house);
        //die(debugArray($_SESSION['HomeNet']));
        foreach ($regions as $region) {
            $regionElement->addMultiOption($region['id'], $region['name']);
        }

        if (!$this->getRequest()->isPost()) {
            $regionElement->setValue($this->view->region);
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        $rService = new HomeNet_Model_Room_Service();
        $room = new HomeNet_Model_Room(array('data' => $values));
        $room->house = $this->view->house;

        $rService->create($room);

        $hService->clearCacheById($this->view->house);

        //redirect to the next step
        return $this->_redirect($this->view->url(array('house' => $this->view->house), 'homenet-house') . '?message=Added Room'); //
    }

    public function editAction() {
        $form = new HomeNet_Form_Room();
        $form->addElement('submit', 'submit', array('label' => 'Update'));

        $regionElement = $form->getElement('region');

        $hService = new HomeNet_Model_House_Service();
        $house = $hService->getObjectById($this->view->house);
        $room = $house->getRoomById($this->view->room);

        $regions = $hService->getHouseRegionNames($this->view->house);

        //die(debugArray($_SESSION['HomeNet']));
        foreach ($regions as $region) {
            $regionElement->addMultiOption($region['id'], $region['name']);
        }

        if (!$this->getRequest()->isPost()) {

            //load exsiting values
            
            /*
              if(is_null($row)){
              throw new Zend_Exception('');
              }
             */
            $form->populate($room->toArray());

            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        $room->fromArray($form->getValues());

        $room->house = $this->view->house;
        $rService = new HomeNet_Model_Room_Service();
        $rService->update($room);

        $mService = new HomeNet_Model_Message_Service();
        $mService->add(HomeNet_Model_Message::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Updated &quot;' . $room->name . '&quot; in ' . $house->name . '', null, $this->view->house);

        $hService->clearCacheById($this->view->house);

       // die($this->view->url(array('house' => $this->view->house, 'room' => $room->id, 'action'=>'index'), 'homenet-room'));

        //redirect to the next step
        return $this->_redirect($this->view->url(array('house' => $this->view->house, 'room' => $room->id, 'action'=>'index'), 'homenet-room') . '?message=Updated Room'); //
    }

    public function removeAction() {



        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $rService = new HomeNet_Model_Room_Service();
            $room = $rService->getObjectById($this->view->room);

            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to remove "' . $room->name . '"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        //need to figure out why this isn't in values
        if (!empty($_POST['delete'])) {
            $rService = new HomeNet_Model_Room_Service();
            $room = $rService->getObjectById($this->view->room);
            $rService->delete($room);

            return $this->_redirect($this->view->url(array('house' => $this->view->house), 'homenet-house') . '?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('house' => $this->view->house, 'room' => $this->view->room), 'homenet-room') . '?message=Canceled');
    }

}