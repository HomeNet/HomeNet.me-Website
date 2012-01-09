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
class Core_Form_Registration extends CMS_Form
{

    public function init()
    {
        $oldpassword = $this->createElement('password', 'oldpassord');
        $oldpassword->setLabel('Current Password: ');
        $oldpassword->setRequired('true');
        $this->addElement($oldpassword);



        $password = $this->createElement('password', 'newpassword');
        $password->setLabel('New Password: ');
        $password->setRequired('true');
        $this->addElement($password);
        
        $password2 = $this->createElement('password', 'newpassword2');
        $password2->setLabel('Repeat Password: ');
        $password2->setRequired('true');
        if(!empty($_POST['password'])){
            $password2->addValidator(new Zend_Validate_Identical($_POST['password']));
        }
        $password2->addErrorMessage('Password doesn\'t match');
        $this->addElement($password2);
        
        $group = $this->addDisplayGroup($this->getElements(), 'pasword', array ( 'legend' => 'Login Details'));

        $submit = $this->addElement('submit', 'submit', array('label' => 'Update'));        
    }
}

