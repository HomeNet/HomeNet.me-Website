<?php
/*
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
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package Core
 * @subpackage User
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Admin_Form_User extends CMS_Form
{

    public function init()
    {
        $this->setMethod('post');
        $id = $this->createElement('hidden', 'id');
        $id->setDecorators(array('ViewHelper'));
        $this->addElement($id);

        $name = $this->createElement('text','name');
        $name->setLabel('First/Last Name: ');
        $name->setRequired('true');
        $name->addFilter('StripTags');
        $this->addElement($name);

        $email = $this->createElement('text','email');
        $email->setLabel('Email Address: ');
        $email->setRequired('true');
        $email->addFilter('StripTags');
        $email->addValidator('EmailAddress',true);
        $email->addErrorMessage('A valid email address is required!');
        $this->addElement($email);

        $location = $this->createElement('text','location');
        $location->setLabel('Location: ');
        $location->setRequired('true');
        $location->addFilter('StripTags');
        $this->addElement($location);
        
         $group = $this->createElement('select', 'primary_group');
        $group->setLabel('Primary Group: ');
        $group->setRequired('true');
        
        
        $groupService = new Core_Model_Group_Service();
        $objects = $groupService->getObjectsByType(array(1,2));

        $options = array();
        foreach($objects as $value){
            $options[$value->id] = $value->title;
        }

        //$template->addMultiOption('None','');
        $group->setMultiOptions($options);
        $this->addElement($group);

        $username = $this->createElement('text','username');
        $username->setLabel('Username: ');
        $username->setRequired('true');
        $username->addFilter('StripTags');
        $username->addErrorMessage('The username is required!');
        $this->addElement($username);



        $password = $this->createElement('password', 'password');
        $password->setLabel('Password: ');
       // $password->setRequired('true');
        $this->addElement($password);
        
        $password2 = $this->createElement('password', 'password2');
        $password2->setLabel('Repeat Password: ');
        //$password2->setRequired('true');
        if(!empty($_POST['password'])){
            $password2->addValidator(new Zend_Validate_Identical($_POST['password']));
        }
        $password2->addErrorMessage('Password doesn\'t match');
        $this->addElement($password2);
      
        $group = $this->addDisplayGroup(array('username', 'name', 'email', 'location','primary_group'), 'profile',array ( 'legend' => 'User Profile'));
        $group = $this->addDisplayGroup(array('password', 'password2'), 'setpassword', array ( 'legend' => 'Account Password'));
    }
}

