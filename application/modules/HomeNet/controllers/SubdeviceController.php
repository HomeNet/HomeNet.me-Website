<?php

class HomeNet_SubdeviceController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('graph', 'html')
                ->initContext();
    }

    public function indexAction()
    {
        // action body
    }

    public function graphAction()
    {
        $this->_helper->layout()->disableLayout();

        $sService = new HomeNet_Model_Subdevice_Service();
        $driver = $sService->getObjectById($this->_getParam('subdevice'));

        $start = new Zend_Date($this->_getParam('start'),Zend_Date::TIMESTAMP);
        $end   = new Zend_Date($this->_getParam('end'),  Zend_Date::TIMESTAMP);

        $this->view->graphURL = $driver->getGraph($start, $end);
    }

    public function datasetAction()
    {
        $this->_helper->layout()->disableLayout();

        $sService = new HomeNet_Model_Subdevice_Service();
        $driver = $sService->getObjectById($this->_getParam('subdevice'));

        $start = new Zend_Date($this->_getParam('start'),Zend_Date::TIMESTAMP);
        $end   = new Zend_Date($this->_getParam('end'),  Zend_Date::TIMESTAMP);

        $this->view->dataset = debugArray($driver->getDataPoints($start, $end,100));
    }

}