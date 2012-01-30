<?php

class HomeNet_ComponentController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function indexAction()
    {
        // action body
    }

    public function graphAjaxAction()
    {
        $acl = new HomeNet_Model_Acl($this->_getParam('house'));
        $acl->checkAccess('house', 'index');
        
        
        
        $this->_helper->layout()->disableLayout();

        $service = new HomeNet_Model_Component_Service();
        $object = $service->getObjectById($this->_getParam('id'));

        $start = new Zend_Date($this->_getParam('start'),Zend_Date::TIMESTAMP);
        $end   = new Zend_Date($this->_getParam('end'),  Zend_Date::TIMESTAMP);

        $this->view->graphURL = $object->getGraph($start, $end);
    }

    public function datasetAction()
    {
        $this->_helper->layout()->disableLayout();

        $service = new HomeNet_Model_Component_Service();
        $object = $service->getObjectById($this->_getParam('id'));

        $start = new Zend_Date($this->_getParam('start'),Zend_Date::TIMESTAMP);
        $end   = new Zend_Date($this->_getParam('end'),  Zend_Date::TIMESTAMP);

        $this->view->dataset = debugArray($object->getDataPoints($start, $end,100));
    }

}