<?php

class CMS_Controller_Plugin_Ajax extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        
        $action = strtolower($request->action);
        
        if(strstr($action,'ajax')){
          Zend_Layout::getMvcInstance()->disableLayout();
           // $view->dis
//            Zend_Controller_Front::getInstance()
//            ->setParam('noViewRenderer', true);
            
            
        }
        
        if($request->isXmlHttpRequest()){

            $view = Zend_Layout::getMvcInstance()->getView();
           $view->layout()->disableLayout();

            //set to ajax
            
            $request->setActionName($request->action.'-ajax');
            

        }

    }
}