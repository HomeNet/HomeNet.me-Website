<?php

class CMS_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        // set up acl
        $acl = new Zend_Acl();

        // add the roles
        $acl->addRole(new Zend_Acl_Role('guest'));
        $acl->addRole(new Zend_Acl_Role('user'), 'guest');
        $acl->addRole(new Zend_Acl_Role('administrator'), 'user');
        // add the resources
        $acl->add(new Zend_Acl_Resource('m_core'));
        $acl->add(new Zend_Acl_Resource('m_core_c_index'),'m_core');
        $acl->add(new Zend_Acl_Resource('m_core_c_register'),'m_core');
        $acl->add(new Zend_Acl_Resource('m_core_c_error'),'m_core');
        $acl->add(new Zend_Acl_Resource('m_core_c_login'),'m_core');
        $acl->add(new Zend_Acl_Resource('m_core_c_user'),'m_core');
        $acl->add(new Zend_Acl_Resource('m_core_c_contact'),'m_core');

     
     
        $acl->allow(null, array('m_core_c_index', 'm_core_c_error'));
        // a guest can only read content and login
        $acl->allow('guest',  array('m_core_c_login','m_core_c_user', 'm_core_c_register'), null);
        $acl->allow('guest',  array('m_core_c_user'), array('next-steps'));
        // cms users can also work with content
       // $acl->allow('user', 'page', array('list', 'create', 'edit', 'delete'));
        // administrators can do anything
       // $acl->allow('administrator', null);
        $acl->allow('user');//, null);
/*
        $acl->allow('guest', 'homenet_index', null);
        $acl->allow('guest', 'homenet_device', null);
        $acl->allow('guest', 'homenet_house', null);
        $acl->allow('guest', 'homenet_room', null);*/

        // fetch the current user

        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $role = 'user';
        } else {
            $role = 'guest';
        }
        $module = '_'.strtolower($request->module);
        if(empty($module)){
            $module = 'm_core';
        }
        
        //test dynamicly building acl
        
        //add module resource
        if(!$acl->has($module)){
            $acl->add(new Zend_Acl_Resource($module));
        }
        
        
        $controller = 'c_'.strtolower($request->controller);
        
        if(!$acl->has($module.'_'.$controller)){
            $acl->add(new Zend_Acl_Resource($module.'_'.$controller), $module);
        }
        
        
        $action = strtolower($request->action);
        if (!$acl->isAllowed($role, $module.'_'.$controller, $action)) {
            if ($role == 'guest') {
                $request->setControllerName('login');
                $request->setActionName('index');
                $request->setParam('forward', true);
            } else {
                $request->setControllerName('error');
                $request->setActionName('noauth');
            }
        }
    }

}