<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    public static function autoload($class) {
        include str_replace('_', '/', $class) . '.php';
        return $class;
    }

    protected function _initLoaderResource() {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                    'basePath' => APPLICATION_PATH,
                    'namespace' => '',
                ));

        /*
          $resourceLoader->addResourceType('acl', 'acls/', 'Acl')
          ->addResourceType('form', 'forms/', 'Form')
          ->addResourceType('model', 'models/', 'Model');
         */
    }

    protected function _initView() {
        $options = $this->getOptions();
        if (isset($options['resources']['view'])) {
            $view = new Zend_View($options['resources']['view']);
        } else {
            $view = new Zend_View;
        }
        if (isset($options['resources']['view']['doctype'])) {
            $view->doctype($options['resources']['view']['doctype']);
        }
        if (isset($options['resources']['view']['contentType'])) {
            $view->headMeta()->appendHttpEquiv('Content-Type', $options['resources']['view']['contentType']);
        }

        //$this->bootstrap('FrontController');
        //$front = $this->getResource('FrontController');
        // $request = $front->getRequest();
        //$view = $this->getResource('View');
        // Zend_Controller_Front::getInstance()->getRequest();
        $view->headTitle($options['site']['name']);
        //$view->headTitle($request->getActionName());
//->headTitle($request->getModuleName())
        // $view->headTitle()->PREPEND//
        $view->headTitle()->setSeparator(' | ');

        //Setup jquery
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
        $view->jQuery()->enable();
        $view->jQuery()->setVersion('1.4.2');
        $view->jQuery()->useCdn();
        $view->jQuery()->uiEnable();
        $view->jQuery()->setUiVersion('1.8.9');
        $view->jQuery()->useUiCdn();

        $defaultTheme = 'default';
        if (isset($options->site->defaultTheme)) {
            $defaultTheme = $options->site->defaultTheme;
        }

        //$isMobile = true;

        if(APPLICATION_ENV == 'mobile'){

            $mobileTheme = 'mobile';
            if (isset($options->site->mobileTheme)) {
                $mobileTheme = $options->site->mobileTheme;
            }

            $theme = $mobileTheme;
        } else {
            $theme = null;
            if (isset($options->site->theme)) {
                $theme = $options->site->theme;
            }
        }

        //add default path
        Zend_Layout::startMvc()->setLayoutPath(APPLICATION_PATH.'/layouts/scripts/');
        $view->setScriptPath(APPLICATION_PATH.'/views/scripts');

        
        if($defaultTheme != 'default'){
            if(!file_exists(APPLICATION_PATH.'/themes/'.$defaultTheme)){
                throw new Zend_Exception('Theme folder Doesn&quot;t exsist: '.APPLICATION_PATH.'/themes/'.$defaultTheme);
            }
            Zend_Layout::startMvc()->setLayoutPath(APPLICATION_PATH.'/themes/'.$defaultTheme.'/layouts/scripts/');
            $view->addScriptPath(APPLICATION_PATH.'/themes/'.$defaultTheme.'/views/scripts');
        }

        if(!empty($theme)){
            if(!file_exists(APPLICATION_PATH.'/themes/'.$theme)){
                throw new Zend_Exception('Theme folder Doesn&quot;t exsist: '.APPLICATION_PATH.'/themes/'.$theme);
            }
            Zend_Layout::startMvc()->setLayoutPath(APPLICATION_PATH.'/themes/'.$theme.'/layouts/scripts/');
            
            $view->addScriptPath(APPLICATION_PATH.'/themes/'.$theme.'/views/scripts');
            
            //$view->
        }

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

       // die(debugArray($view->getScriptPaths()));

        //die($viewRenderer->getModule());
//:moduleDir
        if(!empty($theme)){
            $viewRenderer->setViewBasePathSpec(APPLICATION_PATH.'/themes/'.$theme.'/modules/:module/views');
        }

        
        

        //die($viewRenderer->getViewBasePathSpec());
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        return $view;
    }

    protected function _initModifiedFrontController() {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $response = new Zend_Controller_Response_Http;
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
        $front->setResponse($response);
        //$front->setParam('prefixDefaultModule', false);
        $front->setParam('noViewRender',true);
    }

}

function debugArray($array) {
    return '<pre>' . print_r($array, 1) . '</pre>';
}

class NotFoundException extends DomainException {
    
}

class DuplicateEntryException extends DomainException {
    
}

