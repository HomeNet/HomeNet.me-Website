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
        $acl->add(new Zend_Acl_Resource('default_index'));
        $acl->add(new Zend_Acl_Resource('default_register'));
        $acl->add(new Zend_Acl_Resource('default_error'));
        $acl->add(new Zend_Acl_Resource('default_login'));
        $acl->add(new Zend_Acl_Resource('default_user'));
        $acl->add(new Zend_Acl_Resource('default_contact'));

        $acl->add(new Zend_Acl_Resource('homenet_'));
        $acl->add(new Zend_Acl_Resource('homenet_index'));
        $acl->add(new Zend_Acl_Resource('homenet_house'));
        $acl->add(new Zend_Acl_Resource('homenet_room'));//
        $acl->add(new Zend_Acl_Resource('homenet_node'));
        $acl->add(new Zend_Acl_Resource('homenet_device'));
        $acl->add(new Zend_Acl_Resource('homenet_subdevice'));//
        $acl->add(new Zend_Acl_Resource('homenet_apikeys'));


        $acl->add(new Zend_Acl_Resource('homenet_node-models'));
        $acl->add(new Zend_Acl_Resource('homenet_device-models'));
        $acl->add(new Zend_Acl_Resource('homenet_subdevice-models'));

        $acl->add(new Zend_Acl_Resource('homenet_setup'));
        $acl->
        $acl->allow(null, array('default_index', 'default_error'));
        // a guest can only read content and login
        $acl->allow('guest',  array('default_login','default_user', 'default_register'), null);
        $acl->allow('guest',  array('default_user'), array('next-steps'));
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
        $module = strtolower($request->module);
        if(empty($module)){
            $module = 'default';
        }
        $controller = strtolower($request->controller);
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