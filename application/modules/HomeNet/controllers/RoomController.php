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

    /**
     * 
     * @var int room id
     */
    private $_id; 
    private $_house;
    private $_room;
    protected $service;
    
    public function init() {
        
        $this->service = new HomeNet_Model_Room_Service();
        $this->view->heading = 'Room'; //for generic templates
        
        $this->view->id = $this->_id = $this->_getParam('id');
        $this->view->house = $this->_house = HomeNet_Model_House_Manager::getHouseById($this->_getParam('house'));
        
        //$this->view->region = $this->_getParam('region');

        
        //setup bread crumbs
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Home',
            'route'  => 'homenet',   
        ));
        
        $this->view->breadcrumbs()->addPage(array(
            'label'  => $this->_house->name,
            'route'  => 'homenet-house',  
            'controller' => 'house',
            'params' => array('house'=>$this->_house->id)
        ));
        
        if($this->_id !== null){
            $this->view->room =  $this->_room = $this->_house->getRoomById($this->_getParam('id'));

            $this->view->breadcrumbs()->addPage(array(
                'label'  => $this->_room->name,
                'route'  => 'homenet-house-id',  
                'controller' => 'room',
                'params' => array('house'=>$this->_house->id, 'id'=>$this->_room->id)
            ));
        }
    }

    public function indexAction() {
           $this->_helper->viewRenderer('info');
            return $this->infoAction();
        
        //return $this->_forward('info');
    }

    public function infoAction() {
        $sService = new HomeNet_Model_Component_Service();
        $this->view->components = $sService->getObjectsByRoom($this->_room->id);
    }

    public function controlAction() {
       
        $sService = new HomeNet_Model_Component_Service();
        $this->view->components = $sService->getObjectsByRoom($this->_room->id);

        if (!$this->getRequest()->isPost()) {
            
            return;
        }

        $component = $_POST['component'];
        
        if(!empty($this->view->components[$component])){
            $this->view->components[$component]->processControlForm($_POST);
        }
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $hService = new HomeNet_Model_House_Service;
        $regionIds = $hService->getRegionsById($this->_house->id);
        
        
        $form = new HomeNet_Form_Room($regionIds);

        $form->addElement('submit', 'submit', array('label' => 'Add'));


        if (!$this->getRequest()->isPost()) {
            $regionElement = $form->getElement('region');
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

        $values['house'] = $this->_house->id;

        $object = $this->service->create($values);

        $this->view->messages()->add('Successfully added room &quot;'.$object->name.'&quot;');
        return $this->_redirect($this->view->url(array('controller'=>'house', 'action'=>'index', 'house' => $this->_house->id),'homenet-house'));
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $homeService = new HomeNet_Model_House_Service();
        $regions = $homeService->getRegionsById($this->_house->id);

        $form = new HomeNet_Form_Room($regions);
        $form->addElement('submit', 'submit', array('label' => 'Update'));

        $service = new HomeNet_Model_Room_Service();
        $object = $service->getObjectById($this->_id);

        if (!$this->getRequest()->isPost()) {
            //load exsiting values
            $form->populate($object->toArray());
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        $object->fromArray($form->getValues());

        //$object->house = $this->view->house;
        
        $service->update($object);

       // $mService = new HomeNet_Model_Message_Service();
       //  $mService->add(HomeNet_Model_Message::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Updated &quot;' . $object->name . '&quot; in ' . $house->name . '', null, $this->view->house);


        $this->view->messages()->add('Successfully updated room &quot;'.$object->name.'&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'room', 'action'=>'index', 'house' => $this->_house->id, 'id'=>$object->id),'homenet-house-id'));
    }

    public function deleteAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        $form = new Core_Form_Confirm();
        
        $service = new HomeNet_Model_Room_Service();
        $object = $service->getObjectById($this->_id);

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            
            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to remove "' . $object->name . '"?'));

            $this->view->form = $form;
            return;
        }

        if (!empty($_POST['delete'])) {
            $name = $object->name;
            $service->delete($object);
            
            $this->view->messages()->add('Successfully deleted room &quot;'.$name.'&quot;');
            return $this->_redirect($this->view->url(array('house' => $this->_house->id), 'homenet-house'));
        }
        return $this->_redirect($this->view->url(array('controller'=>'room', 'action'=>'index', 'house' => $this->_house->id, 'id' => $this->_room->id), 'homenet-house-id'));
    }

}