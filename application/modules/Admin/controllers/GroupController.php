<?php

class Admin_GroupController extends Zend_Controller_Action
{

    private $_id;
    
    public function init()
    {
        $this->view->id = $this->_id = $this->_getParam('id');
        $this->_setupCrumbs();
    }
    
     private function _setupCrumbs(){
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Admin',
            'route'  => 'admin',   
        ));
        
    
        $this->view->breadcrumbs()->addPage(array(
            'label'  => 'Groups',
            'route'  => 'admin',  
            'module' => 'admin',
            'controller' => 'group'
        ));
        
        $this->view->heading = 'Group';
        
       //return $section;
    }

    public function indexAction() {
        $service = new Core_Model_Group_Service();
        $this->view->objects = $service->getObjects();
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $form = new Admin_Form_Group();
        $form->addElement('submit', 'submit', array('label' => 'Create'));
        $this->view->assign('form', $form);

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            //First Time or Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        //save
        $values = $form->getValues();

        $service = new Core_Model_Group_Service();
        $object = $service->create($values);

        $this->view->messages()->add('Successfully Added Group &quot;' . $object->title . '&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'group', 'action' => 'index'), 'admin'));
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $service = new Core_Model_Group_Service();
        $form = new Admin_Form_Group();
        $form->addElement('submit', 'submit', array('label' => 'Update'));
        
        $object = $service->getObjectById($this->_id);
        
        if (!$this->getRequest()->isPost()) {
            //load exsiting values
            $values = $object->toArray();
            $form->populate($values);

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
        $service->update($object);

        $this->view->messages()->add('Successfully Updated Group &quot;' . $object->title . '&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'group', 'action' => 'index'), 'admin'));
    }

    public function deleteAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $cService = new Core_Model_Group_Service();
        $object = $cService->getObjectById($this->_getParam('id'));
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to delete "' . $object->title . '"?'));

            $this->view->form = $form;
            return;
        }

        if (!empty($_POST['confirm'])) {

            $title = $object->title;
            $cService->delete($object);
            
            $this->view->messages()->add('Successfully Deleted Group &quot;' . $title . '&quot;');
        }
        return $this->_redirect($this->view->url(array('controller' => 'group', 'action' => 'index'), 'admin'));
    }
}