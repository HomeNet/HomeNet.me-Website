<?php

abstract class cms_Test_PHPUnit_ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

    final public function bootstrap()
    {
        $this->reset();
        if (null !== $this->bootstrap) {
            if ($this->bootstrap instanceof Zend_Application) {
                $this->bootstrap->bootstrap();
                $this->_frontController = $this->bootstrap->getBootstrap()->getResource('frontcontroller');
            } elseif (is_callable($this->bootstrap)) {
                call_user_func($this->bootstrap);
            } elseif (is_string($this->bootstrap)) {
                require_once 'Zend/Loader.php';
                if (Zend_Loader::isReadable($this->bootstrap)) {
                    include $this->bootstrap;
                }
            }
        }
        $this->frontController
             ->setRequest($this->getRequest())
             ->setResponse($this->getResponse());
    }

    /**
     * Dispatch the MVC
     *
     * If a URL is provided, sets it as the request URI in the request object.
     * Then sets test case request and response objects in front controller,
     * disables throwing exceptions, and disables returning the response.
     * Finally, dispatches the front controller.
     *
     * @param  string|null $url
     * @return void
     */
    public function dispatch($url = null)
    {
        // redirector should not exit
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->setExit(false);

        // json helper should not exit
        $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
        $json->suppressExit = true;

        $request    = $this->getRequest();
        if (null !== $url) {
            $request->setRequestUri($url);
        }
        $request->setPathInfo(null);

        $controller = $this->getFrontController();
        $this->frontController
             ->setRequest($request)
             ->setResponse($this->getResponse())
             ->throwExceptions(false)
             ->returnResponse(false);

        if ($this->bootstrap instanceof Zend_Application) {
            $this->bootstrap->run();
        } else {
            $this->frontController->dispatch();
        }
    }

    /**
     * Retrieve test case request object
     *
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        if (null === $this->_request) {
            require_once 'Zend/Controller/Request/HttpTestCase.php';
            $this->_request = new Zend_Controller_Request_HttpTestCase;
        }
        return $this->_request;
    }

    /**
     * Retrieve test case response object
     *
     * @return Zend_Controller_Response_Abstract
     */
    public function getResponse()
    {
        if (null === $this->_response) {
            require_once 'Zend/Controller/Response/HttpTestCase.php';
            $this->_response = new Zend_Controller_Response_HttpTestCase;
        }
        return $this->_response;
    }
}
