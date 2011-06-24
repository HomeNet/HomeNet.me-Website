<?php

class CategorySetController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $cService = new Content_Model_CategorySet_Service();
        $this->view->assign('object', $cService->getObjects());
    }

    public function newAction()
    {
        $form = new Core_Form_CategorySet();
        $this->view->assign('form',$form);
        // action body
    }

    public function editAction()
    {
        // action body
    }

    public function deleteAction()
    {
        // action body
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











