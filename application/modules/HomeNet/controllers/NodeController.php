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
    private $_node;
    /**
     *
     * @var HomeNet_Model_Node_Service
     */
    private $service;

    public function init() {
        
        $acl = new HomeNet_Model_Acl($this->_getParam('house'));
        $acl->checkAccess('house', $this->getRequest()->getActionName());
        
        $this->view->heading = 'Node'; //for generic templates
        
        $this->view->id = $this->_id = $this->_getParam('id');
        $this->view->house = $this->_house = HomeNet_Model_House_Manager::getHouseById($this->_getParam('house'));
        
        $this->service = new HomeNet_Model_Node_Service($this->_house->id);
        
        
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
        
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Nodes',
            'route'  => 'homenet-house',  
            'controller' => 'node',
            'params' => array('house'=>$this->_house->id)
        ));
        
        
        if($this->_id !== null){
            $this->view->node =  $this->_node = HomeNet_Model_Node_Manager::getNodeByHouseAddress($this->_house->id, $this->_getParam('id'));

            $this->view->breadcrumbs()->addPage(array(
            'label'  => $this->_node->model_name,
            'route'  => 'homenet-house-id',  
            'controller' => 'node',
            'params' => array('house'=>$this->_house->id,
               // 'room'=>$this->_room->id,
                'id'=>$this->_node->address,)
            ));
        }
    }

    public function indexAction() {
        if ($this->_id !== null) {
            $this->_helper->viewRenderer('configure');
            return $this->configureAction();
           // return $this->_forward('configure');
        }
        

        $this->view->objects = $this->service->getObjects();
    }
    
    public function trashedAction() {
         $this->view->objects = $this->service->getTrashedObjects();
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $form = new HomeNet_Form_Node(null, $this->_house->id);
        //$sub = $form->getSubForm('node');
        //array_flip($ids)
        $submit = $form->addElement('submit', 'submit', array('label' => 'Next'));

        if (!$this->getRequest()->isPost()) {

            $address = $form->getElement('address');
            $address->setValue($this->service->getNextAddress());

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


        $node = $this->service->newObjectFromModel($values['model']);
        $node->fromArray($values);
        $node->status = HomeNet_Model_Node::STATUS_LIVE;
        $node->house = $this->_house->id;
 
        $node = $this->service->create($node);
        
        $this->view->messages()->add('Successfully added node '.$node->address.' &quot;' . $node->description . '&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'node', 'action'=>'configure', 'house' => $this->_house->id, 'id'=>$node->address), 'homenet-house-id')); //
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $form = new HomeNet_Form_Node(null, $this->_house->id);
        $form->removeElement('model');

        $submit = $form->addElement('submit', 'submit', array('label' => 'Update'));

        $object = $this->_node;

        if (!$this->getRequest()->isPost()) {
            //load exsiting values
            $values = $object->toArray();
            $form->populate(array('description' => $values['description'], 'uplink' => $values['uplink'], 'address' => $values['address']));

            $this->view->form = $form;
            return;
        }

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        //save
        $values = $form->getValues();
        $object->fromArray($values);

        $this->service->update($object);

        $this->view->messages()->add('Successfully updated node &quot;' . $object->address . '&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'node', 'house' => $this->_house->id), 'homenet-house')); //
    }
    
    public function trashAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $object = $this->_node;
        
        $form = new Core_Form_Confirm('Trash');

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to trash node "' . $object->address . '"?'));
            $this->view->form = $form;
            return;
        }

        if (!empty($_POST['confirm'])) {
            $name = $object->address;
            $this->service->trash($object);//($object);
            $this->view->messages()->add('Successfully trashed node &quot;' . $name . '&quot;');
        }
        return $this->_redirect($this->view->url(array('controller' => 'node', 'house' => $this->_house->id), 'homenet-house'));
    }
    public function untrashAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $object = $this->_node;
        
        $form = new Core_Form_Confirm('Untrash');

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to undelete node "' . $object->address . '"?'));
            $this->view->form = $form;
            return;
        }

        if (!empty($_POST['confirm'])) {
            $name = $object->address;
            $this->service->untrash($object);//($object);
            $this->view->messages()->add('Successfully untrashed node &quot;' . $name . '&quot;');
        }
        return $this->_redirect($this->view->url(array('controller' => 'node', 'house' => $this->_house->id), 'homenet-house'));
    }

    public function deleteAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $object = $this->_node;
        
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to delete node "' . $object->address . '"?
                All associated data will be deleted. Be sure to export any data you wish to keep before continuing.'));
            $this->view->form = $form;
            return;
        }

        if (!empty($_POST['confirm'])) {
            $name = $object->address;
            $this->service->delete($object);//($object);
            $this->view->messages()->add('Successfully deleted node &quot;' . $name . '&quot;');
        }
        return $this->_redirect($this->view->url(array('controller' => 'node', 'house' => $this->_house->id), 'homenet-house'));
    }

    public function codeAction() {
        $this->view->code = htmlspecialchars($this->_node->getCode());
    }

    public function configureAction() {

        
        $this->view->maxDevices = $this->_node->max_devices;



        //$settings = unserialize($model->settings);

        $dService = new HomeNet_Model_Device_Service($this->_house->id);

        $d = $dService->getObjectsByNode($this->_node->id);

        $devices = array();
        foreach ($d as $value) {
            $devices[$value->position] = $value;
        }

        $this->view->devices = $devices;
    }

}

