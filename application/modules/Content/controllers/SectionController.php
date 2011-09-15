<?php

class Content_SectionController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->controllerTitle = 'Section'; //for generic templates
    }

    public function indexAction()
    {
        $service = new Content_Model_Section_Service();
        $this->view->objects = $service->getObjects();
    }

    public function newAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $form = new Content_Form_Section();
        $form->addElement('submit', 'submit', array('label' => 'Create'));
        $this->view->assign('form',$form);

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
        $values = $form->getValues();

        $service = new Content_Model_Section_Manager();
        $service->createByTemplate($values);
        
        return $this->_redirect($this->view->url(array('controller'=>'section', 'action'=>'index'),'content').'?message=Successfully added new Set');//
    }

    public function editAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Section_Service();
        $form = new Content_Form_Section();
        $form->addElement('submit', 'submit', array('label' => 'Update'));
        
        if (!$this->getRequest()->isPost()) {
            //load exsiting values
            $object = $service->getObjectById($this->_getParam('id'));
            
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
         $object = $service->getObjectById($this->_getParam('id'));
         $object->fromArray($values);
        $service->update($object);

        return $this->_redirect($this->view->url(array('controller'=>'section'),'content').'?message=Updated');//
    }

    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $csService = new Content_Model_Section_Service();
        $object = $csService->getObjectById($this->_getParam('id'));
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to delete "'.$object->title.'"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();

        //need to figure out why this isn't in values
        if(!empty($_POST['delete'])){
            
            $csService->delete($object);
            return $this->_redirect($this->view->url(array('controller'=>'section'),'content').'?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('controller'=>'section'),'content').'?message=Canceled');
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