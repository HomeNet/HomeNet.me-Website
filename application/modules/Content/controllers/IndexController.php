<?php

class Content_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {       
        //get section
        $section = $this->_getParam('section');
        if(is_null($section)){
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

        $action = 'index';
        
        $uRole = new CMS_Acl_Role_User($_SESSION['User']['id']);

        if (!$acl->isAllowed($uRole, $cResource, $action)) {
            
            $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();

            $config = Zend_Registry::get('config');
            
             if ($_SESSION['User']['id'] == $config->site->user->guest) { //if guest
               return $this->_redirect($this->view->url(array(),'login'));// 
               
            } else {                
                return $this->_forward('noauth', 'error', 'core');

            }
        }
         
         
         
         
         
        $this->_helper->viewRenderer->view->setScriptPath(APPLICATION_PATH);
        // $this->_helper->viewRenderer->setNoRender();  
       // die(debugArray($this->_helper->viewRenderer->view->getScriptPaths())); 
         
         
        $service = new Content_Model_Template_Service();
        $path = $service->getPathBySectionUrl($section, $template);
        //categories
        $this->view->params = $this->_getAllParams();
        //tags
        return $this->renderScript($path);
        //return $this->renderScript($path);
        
    }


}

