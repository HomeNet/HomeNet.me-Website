<?php

class Admin_UserController extends Zend_Controller_Action
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
            'label'  => 'Users',
            'route'  => 'admin',  
            'module' => 'admin',
            'controller' => 'user'
        ));
        
        $this->view->heading = 'User';
        
       //return $section;
    }

    public function indexAction() {
        $service = new Core_Model_User_Service();
        $this->view->objects = $service->getObjects();
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $form = new Admin_Form_User();
        $form->addElement('submit', 'submit', array('label' => 'Create'));
        $this->view->assign('form', $form);

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
            //First Time or Failed validation; redisplay form
            $this->view->form = $form;
            return;
        }

        //save
        $values = $form->getValues();

        $service = new Core_Model_User_Service();
        $object = $service->create($values);
        
        $auth = new Core_Model_Auth_Internal();
        $auth->add(array('id'=>$object->id, 'username'=>$object->username, 'password'=>$values['password']));

        $this->view->messages()->add('Successfully Added User &quot;' . $object->username . '&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'user', 'action' => 'index'), 'admin'));
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $service = new Core_Model_User_Service();
        $form = new Admin_Form_User();
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
        $result = $service->update($object);
        
        if(!empty($values['password'])){
            $auth = new Core_Model_Auth_Internal();
            $auth->changePassword($result->id, $values['password']);
        }
        

        $this->view->messages()->add('Successfully Updated User &quot;' . $object->username . '&quot;');
        return $this->_redirect($this->view->url(array('controller' => 'user', 'action' => 'index'), 'admin'));
    }

    public function deleteAction() {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates

        $service = new Core_Model_User_Service();
        $object = $service->getObjectById($this->_getParam('id'));
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array('legend' => 'Are you sure you want to delete "' . $object->username . '"?'));

            $this->view->form = $form;
            return;
        }

        if (!empty($_POST['confirm'])) {

            $title = $object->username;
            $service->delete($object);
            
            $this->view->messages()->add('Successfully Deleted User &quot;' . $title . '&quot;');
        }
        return $this->_redirect($this->view->url(array('controller' => 'user', 'action' => 'index'), 'admin'));
    }
}