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
 * @subpackage House
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_HouseController extends Zend_Controller_Action
{

    private $_house;
    
    protected $service;
    
    
    public function init()
    {
        
        $this->view->heading = 'Home'; //for generic templates

        //$this->_house = $this->_getParam('house');
        $this->service = new HomeNet_Model_House_Service();

        $acl = new HomeNet_Model_Acl($this->_getParam('house'));
        $acl->checkAccess('house', $this->getRequest()->getActionName());

        
        $this->view->house = $this->_house = HomeNet_Model_House_Manager::getHouseById($this->_getParam('house'));
        
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
    }

    public function indexAction()
    {
        //$house = $this->service->getObjectById($this->_house->id);

        //$this->view->house = $house;
        
        $nodeService = new HomeNet_Model_Node_Service();
        $this->view->nodes = $nodeService->getObjectsByHouse($this->_house->id);
    }

    public function addAction()
    {

    }

    public function editAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $form = new HomeNet_Form_House();
        $form->addElement('submit', 'submit', array('label' => 'Update'));

        //$service = new HomeNet_Model_House_Service();
        $object = $this->_house;

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
        
        $this->service->update($object);

       // $mService = new HomeNet_Model_Message_Service();
       //  $mService->add(HomeNet_Model_Message::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Updated &quot;' . $object->name . '&quot; in ' . $house->name . '', null, $this->view->house);


        $this->view->messages()->add('Successfully updated &quot;'.$object->name.'&quot;');
        return $this->_redirect($this->view->url(array('controller'=>'house', 'action'=>'index', 'house' => $this->_house->id),'homenet-house'));
    }

    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        $form = new Core_Form_Confirm();
        
        $object = $this->_house;

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            
            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to remove "' . $object->name . '"?'));

            $this->view->form = $form;
            return;
        }

        if (!empty($_POST['confirm'])) {
            $name = $object->name;
            $object->status = HomeNet_Model_House::STATUS_DELETED;
            $this->service->updated($object);
            
            $this->view->messages()->add('Successfully deleted &quot;'.$name.'&quot;');
            return $this->_redirect($this->view->url(array('house' =>  $this->_house->id), 'homenet-house'));
        }
        return $this->_redirect($this->view->url(array('controller'=>'house', 'action'=>'index', 'house' => $this->_house->id), 'homenet-house'));
    }
}