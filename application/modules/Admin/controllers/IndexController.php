<?php

class Admin_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $service = new Core_Model_Reflection_Service();
        $installers = $service->getModuleInstallers();
        
        $links = array();
        $widgets = array();
       
        
        foreach($installers as $key => $installer){
            /**
             * @var $installer CMS_Installer_Abstract 
             */
            $links[$key] = $installer->getAdminLinks();
            $widgets[$key] = $installer->getAdminBlocks();
        }
        
        $this->view->links = $links;
        $this->view->widgets = $widgets;
    }
}