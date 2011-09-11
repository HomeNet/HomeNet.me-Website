<?php

class Content_CategoryController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->controllerTitle = 'Category'; //for generic templates
    }

    public function indexAction()
    {
        $this->view->id = $this->_getParam('id');
        
        if(empty($this->view->id)){
            throw new InvalidArgumentException('Missing Set Id', 404);
        }
        
        $service = new Content_Model_Category_Service();
        $this->view->id = $this->_getParam('id');
        $this->view->assign('objects', $service->getObjectsBySet($this->view->id));
    }

    public function newAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $form = new Content_Form_Category();
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
        //$nodeService = new HomeNet_Model_NodesService();

        $values = $form->getValues();
        $values['set'] = $this->_getParam('id');

        $service = new Content_Model_Category_Service();
        $service->create($values);
        
        return $this->_redirect($this->view->url(array('controller'=>'category', 'action'=>'index', 'id' => $this->_getParam('id')),'content-id').'?message=Successfully added new Set');//
    }

    public function editAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $service = new Content_Model_Category_Service();
        $form = new Content_Form_Category();
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

        return $this->_redirect($this->view->url(array('controller'=>'category', 'action'=>'index', 'id' => $object->set),'content-id').'?message=Updated');//
    }

    public function deleteAction()
    {
        $this->_helper->viewRenderer->setNoController(true); //use generic templates
        
        $cService = new Content_Model_Category_Service();
        $object = $cService->getObjectById($this->_getParam('id'));
        $form = new Core_Form_Confirm();

        if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {

            $form->addDisplayGroup($form->getElements(), 'node', array ('legend' => 'Are you sure you want to delete "'.$object->title.'"?'));

            $this->view->form = $form;
            return;
        }
        
        //@todo check for sections using this set //block if any still do
        
        //@todo also delete amy categories in this set 

        $values = $form->getValues();
        $id = $object->set;
        //need to figure out why this isn't in values
        if(!empty($_POST['delete'])){
            
            $cService->delete($object);
            return $this->_redirect($this->view->url(array('controller'=>'category', 'action'=>'index', 'id' => $id),'content-id').'?message=Deleted');
        }
        return $this->_redirect($this->view->url(array('controller'=>'category', 'action'=>'index', 'id' => $id),'content-id').'?message=Canceled');
    }

    public function hideAction()
    {
        //ajax toggle
    }

    public function showAction()
    {
        //ajax toggle
    }
}