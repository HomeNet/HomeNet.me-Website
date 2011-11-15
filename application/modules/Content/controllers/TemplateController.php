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
 * @package Content
 * @subpackage Template
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_TemplateController extends Zend_Controller_Action
{

    public function init()
    {
         //for generic templates
        $this->view->id = $this->_getParam('id');
    }
    
    private function _loadSection($id){
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Admin',
            'route'  => 'admin'          
        ));
        
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Content',
            'route'  => 'content-admin',  
            'module' => 'Content',
            'controller' => 'section',
        ));
        
        $sService = new Content_Model_Section_Service();
        $section = $sService->getObjectById($id);
       // 
        
        $this->view->breadcrumbs()->addPage(array(
            'label'  => $section->title,
            'route'  => 'content-admin-id',  
            'module' => 'Content',
            'controller' => 'section',
            'params' => array('id'=>$id)
        ));
        
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Templates',
            'route'  => 'content-admin-id',  
            'module' => 'Content',
            'controller' => 'template',
            'params' => array('id'=>$id)
        ));
        
        $this->view->heading = $section->title.' Template';
        
       return $section;
    }

    public function indexAction()
    {
        $section = $this->_loadSection($this->view->id);
        $this->view->heading = $section->title . ' Templates';
        
        $service = new Content_Model_Template_Service();
        $this->view->objects = $service->getObjectsBySection($this->view->id);
        
        
       
    }

    public function newAction()
    {
        $this->_loadSection($this->view->id);
        
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $form = new Content_Form_Template();
        $form->addElement('submit', 'submit', array('label' => 'Create'));
        $this->view->assign('form',$form);
        
        
        
        //$this->_helper->viewRenderer('../generic/new');

        if (!$this->getRequest()->isPost()) {
            //first
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
        $values['owner'] = Core_Model_User_Manager::getUser()->id; 
        $values['section'] = $this->view->id;
        $values['type'] = Content_Model_Template::USER;
        $service = new Content_Model_Template_Service();
        $object = $service->create($values);
        
        return $this->_redirect($this->view->url(array('controller'=>'template', 'action'=>'index', 'id'=>$object->section),'content-admin-id').'?message=Successfully added new Set');//
    }

    public function editAction()
    {
        
        
        
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Template_Service();
        $form = new Content_Form_Template();
        $form->addElement('submit', 'submit', array('label' => 'Update'));
        $form->addElement('hidden', 'section');
        $form->addElement('hidden', 'id');
        
        if (!$this->getRequest()->isPost()) {
            //load exsiting values
            $object = $service->getObjectById($this->_getParam('id'));
            $this->_loadSection($object->section);
            
            $values = $object->toArray();

            $form->populate($values);

            $this->view->form = $form;
            return;
        }
        
        $this->_loadSection($_POST['section']);

        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        //save
        $values = $form->getValues();
        // $object = $service->getNewestObjectById($this->_getParam('id'));
         $values['owner'] = $_SESSION['User']['id']; 
        // $object->fromArray($values);
        $object = $service->create($values);

        return $this->_redirect($this->view->url(array('controller'=>'template', 'action'=>'index', 'id'=>$object->section),'content-admin-id').'?message=Updated');//
    }

    public function deleteAction()
    {
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Delete',
            'uri' => '#'
        ));
        
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Template_Service();
        $object = $service->getObjectById($this->_getParam('id'));
  
        $this->_loadSection($object->section);
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to delete "'.$object->url.'"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        //need to figure out why this isn't in values
        if(!empty($_POST['delete'])){
            
            $service->deleteById($object->id);
            return $this->_redirect($this->view->url(array('controller'=>'template', 'action'=>'index', 'id'=>$object->section),'content-admin-id').'?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('controller'=>'template', 'action'=>'index', 'id'=>$object->section),'content-admin-id').'?message=Canceled');
    }

    public function hideAction()
    {
        // action body
    }

    public function showAction()
    {
        // action body
    }
}