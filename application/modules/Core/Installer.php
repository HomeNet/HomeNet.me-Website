<?php

/*
 * Installer.php
 * 
 * Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * 
 * This file is part of HomeNet.
 * 
 * HomeNet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * HomeNet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * Description of Installer
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Core_Installer extends CMS_Installer_Abstract {

    public $group;
    public $user;

    public function __construct() {
        $this->user = new StdClass;
        $this->group = new StdClass;
        ;
    }
    
    
    public function installContent() {
        //create groups
        $group_acls = array();
        $gService = new Core_Model_Group_Service();

        $everyone = array(
            'parent' => null,
            'type' => 0,
            'title' => 'Everyone',
            'description' => 'Privileges granted to every user',
            'visible' => false,
            'user_count' => 0,
            'settings' => array());

        $this->group->everyone = $result = $gService->create($everyone);

        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'error', 'action' => null, 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'login', 'action' => 'logout', 'permission' => 1);

        $owner = array(
            'parent' => null,
            'type' => 0,
            'title' => 'Owner',
            'description' => 'Privileges granted to the creator of a content item',
            'visible' => false,
            'user_count' => 0,
            'settings' => array());

        $this->group->owner = $result = $gService->create($owner);

        $group_acls[] = array('group' => $result->id, 'module' => null, 'controller' => null, 'action' => 'edit', 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => null, 'controller' => null, 'action' => 'delete', 'permission' => 1);

        $guest = array(
            'parent' => null,
            'type' => 1,
            'title' => 'Guests',
            'description' => 'Users that have not been Authenticated',
            'visible' => false,
            'user_count' => 0,
            'settings' => array());

        $this->group->guests = $result = $gService->create($guest);

        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'index', 'action' => null, 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'login', 'action' => null, 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'index', 'action' => null, 'permission' => 1);


        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'user', 'action' => 'new', 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'user', 'action' => 'next-steps', 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'user', 'action' => 'send-activation', 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'user', 'action' => 'activate', 'permission' => 1);

        $member = array(
            'parent' => null,
            'type' => 1,
            'title' => 'Members',
            'description' => 'Authenticated Users',
            'visible' => true);

        $this->group->members = $result = $gService->create($member);
   
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'index', 'action' => null, 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'user', 'action' => null, 'permission' => 1);
        //  $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'login', 'action' => null, 'permission' => 0);

        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'gTest', 'action' => 'index', 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'gTest', 'action' => 'add', 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'gTest', 'action' => 'edit', 'permission' => 0);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'gTest', 'action' => 'edit', 'object' => 1, 'permission' => 1);
        $group_acls[] = array('group' => $result->id, 'module' => 'core', 'controller' => 'gTest', 'action' => 'edit', 'object' => 2, 'permission' => 0);


        $contentAdmin = array(
            'parent' => null,
            'type' => 1,
            'title' => 'Admins',
            'description' => 'Admin Site Content',
            'visible' => true);

        $this->group->admins = $result = $gService->create($contentAdmin);
 
        $siteAdmin = array(
            'parent' => null,
            'type' => 1,
            'title' => 'Site Admins',
            'description' => 'Full access to Site',
            'visible' => false);

        $this->group->superAdmins = $result = $gService->create($siteAdmin);

        $group_acls[] = array('group' => $result->id, 'module' => null, 'controller' => null, 'action' => null, 'permission' => 1);

        $agService = new Core_Model_Acl_Group_Service();
        foreach ($group_acls as $acl) {
            $agService->create($acl);
        }
        
        
        //create user
        $uService = new Core_Model_User_Service();

        $guest = array(
            'status' => 1,
            'primary_group' => $this->group->guests->id,
            'username' => 'Guest',
            'name' => 'Guest',
            'location' => 'Guest Location',
            'email' => 'guest@mcdportal.com',
            'settings' => array());

        $this->user->guest = $uService->create($guest);
    }
    
    
    
    public function loginAsSuperAdmin(){
        unset($_SESSION);
        $manager = new Core_Model_User_Manager();
        $manager->login(array('username'=>'testSuperAdmin','password'=>'password'));
    }

    public function installTest() {

        $this->uninstallTest(); //remove any old data
        
        $this->installContent();
        
        //create test users
        $user_acls = array();
        $user_auth_internal = array();

        $uService = new Core_Model_User_Service();

       
        //member
        $user = array(
            'status' => 1,
            'primary_group' => $this->group->members->id,
            'username' => 'TestUser1',
            'name' => 'Test User',
            'location' => 'Random',
            'email' => 'testuser1@mcdportal.com',
            'settings' => array());

        $this->user->member = $result = $uService->create($user);

        $user_auth_internal[] = array('id' => $result->id, 'username' => 'TestUser1', 'password' => 'password');

        $user_acls[] = array('user' => $result->id, 'module' => 'core', 'controller' => 'uTest', 'action' => 'index', 'permission' => 1);
        $user_acls[] = array('user' => $result->id, 'module' => 'core', 'controller' => 'uTest', 'action' => 'add', 'permission' => 1);
        $user_acls[] = array('user' => $result->id, 'module' => 'core', 'controller' => 'uTest', 'action' => 'edit', 'permission' => 0);
        $user_acls[] = array('user' => $result->id, 'module' => 'core', 'controller' => 'uTest', 'action' => 'edit', 'object' => 1, 'permission' => 1);
        $user_acls[] = array('user' => $result->id, 'module' => 'core', 'controller' => 'uTest', 'action' => 'edit', 'object' => 2, 'permission' => 0);

        //user
        $user = array(
            'status' => 1,
            'primary_group' => $this->group->members->id,
            'username' => 'TestUser2',
            'name' => 'Test Special User ',
            'location' => 'Random',
            'email' => 'testuser2@mcdportal.com',
            'settings' => array());

         $this->user->member1 = $result = $uService->create($user);
    
        $user_auth_internal[] = array('id' => $result->id, 'username' => 'TestUser2', 'password' => 'password');

        $user_acls[] = array('user' => $result->id, 'module' => 'core', 'controller' => 'test', 'action' => 'special', 'permission' => 1);

        //admin
        $admin = array(
            'status' => 1,
            'primary_group' => $this->group->admins->id,
            'username' => 'TestAdmin',
            'name' => 'Test User',
            'location' => 'Random',
            'email' => 'testadmin@mcdportal.com',
            'settings' => array());

        $this->user->admin = $result = $uService->create($admin);

        $user_auth_internal[] = array('id' => $result->id, 'username' => 'TestAdmin', 'password' => 'password');

        //super admin
        $superAdmin = array(
            'status' => 1,
            'primary_group' => $this->group->superAdmins->id,
            'username' => 'TestSuperAdmin',
            'name' => 'Test Super User',
            'location' => 'Random',
            'email' => 'testsuperadmin@mcdportal.com',
            'settings' => array());

        $this->user->superAdmin = $result = $uService->create($superAdmin);

        $user_auth_internal[] = array('id' => $result->id, 'username' => 'TestSuperAdmin', 'password' => 'password');


        $auService = new Core_Model_Acl_User_Service();
        foreach ($user_acls as $acl) {
            $auService->create($acl);
        }

        $aiService = new Core_Model_Auth_Internal();
        foreach ($user_auth_internal as $auth) {
            $aiService->add($auth);
        }
    }

    public function uninstallTest() {
        $gService = new Core_Model_Group_Service();
        $gService->deleteAll();

        $agService = new Core_Model_Acl_Group_Service();
        $agService->deleteAll();

        $auService = new Core_Model_Acl_User_Service();
        $auService->deleteAll();

        $uService = new Core_Model_User_Service();
        $uService->deleteAll();

        $umService = new Core_Model_User_Membership_Service();
        $umService->deleteAll();

        $authInternal = new Core_Model_Auth_Internal();
        $authInternal->deleteAll();
    }

}