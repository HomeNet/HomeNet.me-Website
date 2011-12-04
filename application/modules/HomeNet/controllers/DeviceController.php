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
 * @subpackage Device
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * 
 */
class HomeNet_DeviceController extends Zend_Controller_Action {

    private $_id;
    private $_house;
    
    public function init() {
        $this->view->house = $this->_getParam('house');
        $this->view->node = $this->_getParam('node');
        $this->view->position = $this->_getParam('position');
    }

    public function indexAction() {
        // action body
    }

    public function addAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        //check and make sure that there isn't a device there currently
        $model = $this->_getParam('model');

        if(!empty($model)){
           return $this->_forward('add2');
        }

        $form = new CMS_Form();

        $model = $form->createElement('select', 'model');
        $model->setLabel("Model: ");
        $form->addElement($model);

        $dmService = new HomeNet_Model_DeviceModel_Service();
        $rows = $dmService->getObjectsByStatus(1);

        $categories = $dmService->getCategories();

        $devices = array();

        foreach ($rows as $value) {
            $devices[$categories[$value->category]][$value->id] = $value->name;
        }

        $model->setMultiOptions($devices);

        $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Device Setup'));
        $submit = $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost()) {

            // $id = $sub->getElement('node');
            // $table = new HomeNet_Model_NodesServices();
            //  $id->setValue($table->fetchNextId($this->_getParam('house')));

            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        return $this->_redirect($this->view->url(array('house' => $this->view->house, 'node' => $this->view->node, 
            'position' => $this->view->position, 'model'=>$form->getValue('model'), 'action' => 'add'), 'homenet-device')); //
    }

    public function add2Action(){

        //check position
        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectById($this->view->node);

        $room = $node->room; 
        //die(debugArray($row));

        if($this->view->position > $node->getMaxPorts()){
            throw new Zend_Exception('Invalid Position');
        }

//        //check to make sure device doesn't already exist
//        $table = new HomeNet_Model_DevicesService();
//        $row = $table->getDeviceByNodePosition($this->view->node, $this->view->position);
//        if(!is_null($row)){
//            throw new Zend_Exception('Device Already Exists');
//        }

        //get deviceModel
        $model = $this->_getParam('model');

        $dService = new HomeNet_Model_Device_Service();
        $driver = $dService->getObjectByModel($model);
        $driver->setHouse($this->view->house);
        $driver->setRoom($room);

        //get setup form
        $form = $driver->getConfigForm();

        $id = $form->createElement('hidden', 'page2');
        $id->setDecorators(array('ViewHelper'));
        $id->setValue('true');
        $form->addElement($id);      
      
        //$form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Device Setup'));
        $submit = $form->addElement('submit', 'add', array('label' => 'Add'));

        if (empty($_POST['page2'])) {
            //first view
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        $driver->setPosition($this->view->position);
        $driver->setNode($this->view->node);


       // try {
            $driver->processConfigForm($form->getValues());
            $dService->create($driver);
       // } catch (Zend_Exception $e){
       //     $this->view->error = $e->getMessage();
       //     $this->view->form = $form;
       //     return;
       // }
        return $this->_redirect($this->view->url(array('house'=>$this->view->house, 'node'=>$this->view->node),'homenet-node').'?message=Device Added');//
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        //check to make sure device doesn't already exist
        $dService = new HomeNet_Model_Device_Service();
        $driver = $dService->getObjectByNodePosition($this->view->node, $this->view->position);
        $driver->setHouse($this->view->house);
                
        //get setup form
        $form = $driver->getConfigForm();

       // die(debugArray($form));

        //$form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Device Setup'));
        $form->addElement('submit', 'add', array('label' => 'Update'));

        if (!$this->getRequest()->isPost()) {
            //first view
            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }
//die(debugArray($form->getValues()));
        //$driver->setPosition($this->view->position);
        //$driver->setNode($this->view->node);

      //  try {
            $driver->processConfigForm($form->getValues());
           // die(debugArray($driver));
            $dService->update($driver);
      //  } catch (Zend_Exception $e){
        //    $this->view->error = $e->getMessage();
       //     $this->view->form = $form;
          //  return;
       // }
        return $this->_redirect($this->view->url(array('house'=>$this->view->house, 'node'=>$this->view->node),'homenet-node').'?message=Device Updated');//
    }

    public function deleteAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
         $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $dService = new HomeNet_Model_Device_Service();
            $row = $dService->getObjectByNodePosition($this->view->node, $this->view->position);
            //die(debugArray($row));

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to remove device "'.$row->modelName.'"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        //need to figure out why this isn't in values
        if(!empty($_POST['delete'])){
            $dService = new HomeNet_Model_Device_Service();
            $driver = $dService->getObjectByNodePosition($this->view->node, $this->view->position);
            $dService->delete($driver);
            return $this->_redirect($this->view->url(array('house'=>$this->view->house, 'node'=>$this->view->node),'homenet-node').'?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('house'=>$this->view->house, 'node'=>$this->view->node),'homenet-node').'?message=Canceled');
    }

    public function configureAction() {
        // action body
    }

}

