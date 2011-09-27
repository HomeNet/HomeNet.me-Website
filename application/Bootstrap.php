<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    public static function autoload($class) {
        include str_replace('_', '/', $class) . '.php';
        return $class;
    }
    
    protected function _initSession(){
        Zend_Session::start();
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
    
    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
        return $config;
    }

    

    protected function _initView() {
        $options = Zend_Registry::get('config');
        if (isset($options->resources->view)) {
            $view = new Zend_View($options->resources->view);
        } else {
            $view = new Zend_View;
        }
        if (isset($options->resources->view->doctype)) {
            $view->doctype($options->resources->view->doctype);
        }
        if (isset($options->resources->view->contentType)) {
            $view->headMeta()->appendHttpEquiv('Content-Type', $options->resources->view->contentType);
        }

        //setup title
        $view->headTitle($options->site->name);
        $view->headTitle()->setDefaultAttachOrder('PREPEND');
        $view->headTitle()->setSeparator(' | ');

        //Setup jquery
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
        $view->jQuery()->enable();
        $view->jQuery()->setVersion('1.6.2');
        $view->jQuery()->useCdn();
        $view->jQuery()->uiEnable();
        $view->jQuery()->setUiVersion('1.8.16');
        $view->jQuery()->useUiCdn();
        
        
        //setup themes
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
        
        $layout = Zend_Layout::startMvc();

        //add default path
        $layout->setLayoutPath(APPLICATION_PATH.'/layouts/scripts/');
        $view->setScriptPath(APPLICATION_PATH.'/views/scripts');

        
        if($defaultTheme != 'default'){
            if(!file_exists(APPLICATION_PATH.'/themes/'.$defaultTheme)){
                throw new Zend_Exception('Theme folder Doesn&quot;t exsist: '.APPLICATION_PATH.'/themes/'.$defaultTheme);
            }
            $layout->addLayoutPath(APPLICATION_PATH.'/themes/'.$defaultTheme.'/layouts/scripts/');
            $view->addScriptPath(APPLICATION_PATH.'/themes/'.$defaultTheme.'/views/scripts');
        }

        if(!empty($theme)){
            if(!file_exists(APPLICATION_PATH.'/themes/'.$theme)){
                throw new Zend_Exception('Theme folder Doesn&quot;t exsist: '.APPLICATION_PATH.'/themes/'.$theme);
            }
            
            $layout->addLayoutPath(APPLICATION_PATH.'/themes/'.$theme.'/layouts/scripts/');
            $view->addScriptPath(APPLICATION_PATH.'/themes/'.$theme.'/views/scripts');
        }
        
        $layout->setLayout('one-column');
        
        //setup our custom helpers
        $view->addHelperPath('CMS/View/Helper/', 'CMS_View_Helper');

        //setup viewrender
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        $viewRenderer->setViewScriptPathNoControllerSpec('generic/:action.:suffix');

 
        //:moduleDir
        if(!empty($theme)){
            $viewRenderer->setViewBasePathSpec(APPLICATION_PATH.'/themes/'.$theme.'/modules/:module/views');
        }
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

class RequiresFurtherActionException extends DomainException {
    
}

function delete_directory($dirname) {
   if (is_dir($dirname))
      $dir_handle = opendir($dirname);
   if (!$dir_handle)
      return false;
   while($file = readdir($dir_handle)) {
      if ($file != "." && $file != "..") {
         if (!is_dir($dirname."/".$file))
            unlink($dirname."/".$file);
         else
            delete_directory($dirname.'/'.$file);    
      }
   }
   closedir($dir_handle);
   rmdir($dirname);
}

