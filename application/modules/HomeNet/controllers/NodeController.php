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
 * @subpackage Node
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * 
 */
class HomeNet_NodeController extends Zend_Controller_Action {

    private $_id;
    private $_house;
    
    public function init() {
        $this->view->node = $this->_id = $this->_getParam('id');
        $this->view->house = $this->_house = $this->_getParam('house');
        
        
        $this->view->room = $this->_getParam('room');
        $this->view->region = $this->_getParam('region');
        
    }

    public function indexAction() {
        if($this->_id !== null){
            return $this->_forward('configure');
        }
            
        $service = new HomeNet_Model_Node_Service();
        $this->view->objects = $service->getObjectsByHouse($this->view->house);
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        $type = array(1 => 'Wireless Sensor Node', 2 => 'Wired Base Station', 3 => 'Internet Node');

        $form = new HomeNet_Form_Node();
        //$sub = $form->getSubForm('node');

        $uplink = $form->getElement('uplink');

        $nService = new HomeNet_Model_Node_Service();
        $ids = $nService->getInternetIdsByHouse($this->view->house);

        $uplink->addMultiOptions(array_flip($ids));


        $submit = $form->addElement('submit', 'submit', array('label' => 'Next'));


        if (!$this->getRequest()->isPost()) {

            $id = $form->getElement('node');

            //$nodeService = new HomeNet_Model_DbTable_Nodes();
            $id->setValue($nService->getNextIdByHouse($this->view->house));

            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }
        
        //save
        //$nodeService = new HomeNet_Model_NodesService();

       $values = $form->getValues();

        $node = $nService->newObjectByModel($values['model']);
        $node->fromArray($values);

        $node->house = $this->view->house;
        $node->room = $this->view->room;

        $node = $nService->create($node);


        $this->view->node = $node->id;
        $this->view->done = true;
      
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        $type = array(1 => 'Sensor Node', 2 => 'Base Station', 3 => 'Internet Node');

        $form = new HomeNet_Form_Node();
        $form->removeElement('model');

        $uplink = $form->getElement('uplink');

        $nService = new HomeNet_Model_Node_Service();
        $ids = $nService->getInternetIdsByHouse($this->view->house);

        $uplink->addMultiOptions(array_flip($ids));

        $submit = $form->addElement('submit', 'submit', array('label' => 'Update'));
        
        if (!$this->getRequest()->isPost()) {
            //load exsiting values
            $node = $nService->getObjectById($this->view->node);
            
            $values = $node->toArray();
//die(debugArray($values));
            $form->populate(array('description'=>$values['description'],'uplink'=>$values['uplink'],'node'=>$values['node']));

            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        //save

        $node = $nService->getObjectById($this->view->node);

        $values = $form->getValues();
        $node->fromArray($values);
        $node->house = $this->view->house;
        $node->room = $this->view->room;

        $nService->update($node);

        return $this->_redirect($this->view->url(array('house'=>$this->view->house),'homenet-node-index').'?message=Updated');//
    }

    public function deleteAction() {

        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        $nService = new HomeNet_Model_Node_Service();
        $node = $nService->getObjectById($this->view->node);
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to remove node "'.$node->node.'"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        //need to figure out why this isn't in values
        if(!empty($_POST['delete'])){
            
            $nService->delete($node);
            return $this->_redirect($this->view->url(array('house'=>$this->view->house),'homenet-node-index').'?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('house'=>$this->view->house),'homenet-node-index').'?message=Canceled');
    }

    public function codeAction() {

        $nodesService = new HomeNet_Model_Node_Service();
        $node = $nodesService->getObjectById($this->view->node);
       // $node->loadDevices();

        $this->view->code = htmlspecialchars($node->getCode());
    }

    public function configureAction() {
        $node = $this->_getParam('node');
/**
 * @todo this might be faster as a join
 */
        $nService = new HomeNet_Model_Node_Service();
        $this->view->node = $nService->getObjectById($node);

       // die(debugArray($this->view->node));

        $nodeModels = new HomeNet_Model_NodeModel_Service();
        $model = $nodeModels->getObjectById($this->view->node->model);
        $this->view->maxDevices = $model->max_devices;


        
        //$settings = unserialize($model->settings);

        $dService = new HomeNet_Model_Device_Service();

        $d = $dService->getObjectsByNode($node);

        $devices = array();
        foreach($d as $value){
           $devices[$value->position] = $value;
        }

        $this->view->devices = $devices;
    }

}

