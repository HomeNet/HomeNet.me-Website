<?php

class Content_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {       
        //get section
        $section = $this->_getParam('section');
        if($section === null){
            throw new InvalidArgumentException('Missing Section in Route');
        }
             
        //get template
         $template = $this->_getParam('template', 'index');
        //default is index
         
        //validate acl
        $aManager = Core_Model_Acl_Manager::getInstance();

        $acl = $aManager->getUserAcl('content');

        $cResource = new CMS_Acl_Resource_Controller($section);

        //check to see if resource exists, if it doesn't add it and let it inhert the default rules
        if (!$acl->has($cResource)) {
            $acl->add($cResource);
        }

       // $action = 'index';
        
        $user = Core_Model_User_Manager::getUser();
        
      //  $uRole = new CMS_Acl_Role_User($_SESSION['User']['id']);

        if (!$acl->isAllowed($user, $cResource, $template)) {
            
            $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();

            $config = Zend_Registry::get('config');
            
             if ($user->id == $config->site->user->guest) { //if guest
                return $this->_redirect($this->view->url(array(),'login'));// 
            } else {                
                return $this->_forward('noauth', 'error', 'core');
            }
        }
         
         $service = new Content_Model_Template_Service();
        $path = $service->getPathBySectionTemplate($section, $template);
        $view = $this->_helper->viewRenderer->view;
        
        $paths = array_reverse($view->getScriptPaths());
        foreach($paths as $value){
            $view->addScriptPath($value.'generic/');
        }
   
         
       //  $this->_helper->viewRenderer->setNoController(true);
         
        $view->addScriptPath($path);
        // $this->_helper->viewRenderer->setNoRender();  
     //  die(debugArray($this->_helper->viewRenderer->view->getScriptPaths())); 
         
         
        
        //categories
        $this->view->params = $this->_getAllParams();
        //tags
        return $this->renderScript($service->getTemplate($template));
        //return $this->renderScript($path);
        
    }


}

