<?php

class Content_FieldController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->controllerTitle = 'Field'; //for generic templates
        $this->view->id = $this->_getParam('id');
    }

    public function indexAction()
    {
        $service = new Content_Model_Field_Service();
        $this->view->objects = $service->getObjectsBySection($this->view->id);
    }

    public function newAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $form = new Content_Form_Field($this->view->id);
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
        $values['section'] = $this->_getParam('id');
        $service = new Content_Model_Field_Service();
        $service->create($values);
        
        return $this->_redirect($this->view->url(array('controller'=>'field', 'action'=>'index', 'id'=>$values['section']),'content-id').'?message=Successfully added new Set');//
    }

    public function editAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Field_Service();
        $object = $service->getObjectById($this->_getParam('id'));
        
        $form = new Content_Form_Field($object->section);
        $form->addElement('submit', 'submit', array('label' => 'Update'));
        $form->addElement('hidden', 'section');
        
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
         $object = $service->getObjectById($this->_getParam('id'));
         $object->fromArray($values);
        $service->update($object);

        return $this->_redirect($this->view->url(array('controller'=>'field', 'action'=>'index', 'id'=>$values['section']),'content-id').'?message=Updated');//
    }

    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Field_Service();
        $object = $service->getObjectById($this->_getParam('id'));
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to delete "'.$object->label.'"?'));

            $this->view->form = $form;
            return;
        }

        $values = $form->getValues();
        
        $section = $object->section;

        //need to figure out why this isn't in values
        if(!empty($_POST['delete'])){
            
            $service->delete($object);
            return $this->_redirect($this->view->url(array('controller'=>'field', 'action'=>'index', 'id'=>$section)).'?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('controller'=>'field', 'action'=>'index', 'id'=>$section)).'?message=Canceled');
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