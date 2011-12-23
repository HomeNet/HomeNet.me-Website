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
    private $_room;
    private $_node;
    private $_device;
    private $service;
    
    public function init() {
        $this->view->heading = 'Device'; //for generic templates
        $this->service = new HomeNet_Model_Device_Service();

        $this->view->position = $this->_getParam('position');
        $this->view->id = $this->_id = $this->_getParam('id');
        //build breadcrumbs
        
        $this->view->house = $this->_house = HomeNet_Model_House_Manager::getHouseById($this->_getParam('house'));
       // $this->view->room =  $this->_room = $this->_house->getRoomById($this->_getParam('room'));
        $this->view->node =  $this->_node = HomeNet_Model_Node_Manager::getNodeByHouseAddress($this->_house->id, $this->_getParam('node'));
        
//        $this->view->breadcrumbs()->addPage(array(
//            'label'  => 'Home',
//            'route'  => 'homenet',   
//        ));
//        
//        $this->view->breadcrumbs()->addPage(array(
//            'label'  => $this->_house->name,
//            'route'  => 'homenet-house',  
//            'controller' => 'house',
//            'params' => array('house'=>$this->_house->id)
//        ));
//        
//        $this->view->breadcrumbs()->addPage(array(
//            'label'  => $this->_room->name,
//            'route'  => 'homenet-house',  
//            'controller' => 'room',
//            'params' => array('house'=>$this->_house->id)
//        ));
//        
//        $this->view->breadcrumbs()->addPage(array(
//            'label'  => $this->_node->model_name,
//            'route'  => 'homenet-house-id',  
//            'controller' => 'node',
//            'params' => array('house'=>$this->_house->id,
//               // 'room'=>$this->_room->id,
//                'id'=>$this->_node->address,)
//        ));
        if($this->_id !== null){
            $this->view->device =  $this->_device = $this->service->getObjectByHouseNodeaddressPosition($this->_house->id, $this->_node->address, $this->_id);

//            $this->view->breadcrumbs()->addPage(array(
//            'label'  => $this->_node->model_name,
//            'route'  => 'homenet-house-id',  
//            'controller' => 'node',
//            'params' => array('house'=>$this->_house->id,
//               // 'room'=>$this->_room->id,
//                'id'=>$this->_node->address,)
//            ));
        }

        
    }

    public function indexAction() {
        // action body
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        //check and make sure that there isn't a device there currently
        $model = $this->_getParam('model');

        if(!empty($model)){
           return $this->_forward('new2');
        }

        
        //build form
        //@todo move this to it's own form
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
        $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        return $this->_redirect($this->view->url(array('controller'=>'device', 'action' => 'new', 
            'house' => $this->_house->id, 'node' => $this->_node->address), 'homenet-house-node').'?position='.$this->view->position.'&amp;model='.$form->getValue('model')); 
    }

    public function new2Action(){
        $this->_helper->viewRenderer('new'); //use new template
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        //check position
       // $nService = new HomeNet_Model_Node_Service();
       // $node = $nService->getObjectById($this->view->node);

        $room = $this->_node->room; 
        //die(debugArray($row));
        
        $position = $this->_getParam('position');
        $model = $this->_getParam('model');

        if($position > $this->_node->getMaxPorts()){
            throw new Zend_Exception('Invalid Position');
        }

//        //check to make sure device doesn't already exist
//        $table = new HomeNet_Model_DevicesService();
//        $row = $table->getDeviceByNodePosition($this->view->node, $this->view->position);
//        if(!is_null($row)){
//            throw new Zend_Exception('Device Already Exists');
//        }        

        $object = $this->service->newObjectFromModel($model);
        $object->house = $this->_house->id;
        $object->setHouse($this->_house);
       $object->setRoomId($this->_node->room);

        //get setup form
        $form = $object->getConfigForm();

        $id = $form->createElement('hidden', 'model');
        $id->setDecorators(array('ViewHelper'));
        $id->setValue($model);
        $form->addElement($id); 
        
        $id = $form->createElement('hidden', 'page2');
        $id->setDecorators(array('ViewHelper'));
        $id->setValue(true);
        $form->addElement($id); 
      
        //$form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Device Setup'));
        $form->addElement('submit', 'add', array('label' => 'Add'));

        if (empty($_POST['page2']) || !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        $object->position = $position;
        $object->node = $this->_node->id;


       // try {
            $object->processConfigForm($form->getValues());
            $this->service->create($object);
       // } catch (Zend_Exception $e){
       //     $this->view->error = $e->getMessage();
       //     $this->view->form = $form;
       //     return;
       // }
            $this->view->messages()->add('Successfully created device &quot;' . $object->position . '&quot;');
        return $this->_redirect($this->view->url(array('controller'=>'node', 'house'=>$this->_house->id, 'id'=>$this->_node->address),'homenet-house-id'));
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $object = $this->_device; //$dService->getObjectByNodePosition($this->view->node, $this->view->position);
        var_dump($object);


//$object->setHouse($this->view->house);
                
        //get setup form
        $form = $object->getConfigForm();

        $form->addElement('submit', 'add', array('label' => 'Update'));

        if (!$this->getRequest()->isPost() && !$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

      //  try {
            $object->processConfigForm($form->getValues());

            $this->service->update($object);
      //  } catch (Zend_Exception $e){
        //    $this->view->error = $e->getMessage();
       //     $this->view->form = $form;
          //  return;
       // }
        return;
        $this->view->messages()->add('Successfully updated device &quot;' . $object->position . '&quot;');    
        return $this->_redirect($this->view->url(array('controller' => 'node', 'house'=>$this->_house->id, 'id'=>$this->_node->address),'homenet-house-id'));
    }

    public function deleteAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
         $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to remove device "'.$this->_device->model_name.'"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        //need to figure out why this isn't in values
        if(!empty($_POST['delete'])){
            $this->service->delete($this->_device);
            $this->view->messages()->add('Successfully deleted device &quot;' . $object->position . '&quot;');
            //return $this->_redirect($this->view->url(array('house'=>$this->view->house, 'node'=>$this->view->node),'homenet-node').'?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('controller' => 'node', 'house'=>$this->_house->id, 'node'=>$this->_node->address),'homenet-house-id'));
    }

    public function configureAction() {
        // action body
    }

}

