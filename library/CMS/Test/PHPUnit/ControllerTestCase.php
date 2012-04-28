<?php

abstract class CMS_Test_PHPUnit_ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase {

    /**
     * Dispatch the MVC
     *
     * If a URL is provided, sets it as the request URI in the request object.
     * Then sets test case request and response objects in front controller,
     * disables throwing exceptions, and disables returning the response.
     * Finally, dispatches the front controller.
     * 
     * 
     * Modfied to throw Exceptions so I can find issues
     *
     * @param  string|null $url
     * @return void
     */
    public function dispatch($url = null) {
        // redirector should not exit
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->setExit(false);

        // json helper should not exit
        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;

        $request = $this->getRequest();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);

        $controller = $this->getFrontController();
        $this->frontController
                ->setRequest($request)
                ->setResponse($this->getResponse())
                ->throwExceptions(true)//the line that changed
                ->returnResponse(false);

        if ($this->bootstrap instanceof Zend_Application) {
            $this->bootstrap->run();
        } else {
            $this->frontController->dispatch();
        }
    }

    private $_module;
    private $_controller;
    private $_action;

    protected function setModule($module) {
        $this->_module = $module;
        $this->getRequest()->setModuleName($module);
    }

    protected function setController($controller) {
        $this->_controller = $controller;
        $this->getRequest()->setControllerName($controller);
    }

    protected function setAction($action) {
        $this->_action = $action;
        $this->getRequest()->setActionName($action);
    }

    protected function assertACM($action = null, $controller = null, $module = null) {

        if ($action === null) {
            $this->assertAction($this->_action);
        } else {
            $this->assertAction($action);
        }

        if ($controller === null) {
            $this->assertController($this->_controller);
        } else {
            $this->assertController($controller);
        }

        if ($module === null) {
            $this->assertModule($this->_module);
        } else {
            $this->assertModule($module);
        }
    }
    
    protected function _getTestData($seed = 0) {
        
        return array();
    }

    protected function _fillObject($object, $seed = 0) {
        $data = $this->_getTestData($seed);
        foreach ($data as $key => $value) {
            $object->$key = $value;
        }
        return $object;
    }

    protected function _fillArray($array, $seed = 0) {
        if (is_object($array)) {
            $array = $array->toArray();
        }
        return array_merge($array, $this->_getTestData($seed));
    }

}
