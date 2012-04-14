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
 * @subpackage Auth
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Core_Model_Auth_Facebook implements Core_Model_Auth_Interface
{
    /**
     * @param String Password
     * @return String
     */
    public function hashPassword($password) {
        return md5('saltisgood' . $password);
    }

    public function add($credentials) {

        // create a new row
        $table = new Core_Model_DbTable_AuthInternal();
        $row = $table->createRow();
        echo $username;
        if ($row) {
            $row->id = $id;
            $row->username = $username;
            $row->password = $this->hashPassword($password);
            $row->save();
            //return the new user
            return true;
        } else {
            throw new Zend_Exception("Could not create user!");
        }
    }

    public function login($credentials){
        // get the default db adapter
        $db = Zend_Db_Table::getDefaultAdapter();
        //create the auth adapter
        $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'auth_internal', 'username', 'password');
        //set the username and password
        $authAdapter->setIdentity($username);
        $authAdapter->setCredential($this->hashPassword($password)); 
        //authenticate
        $result = $authAdapter->authenticate();

        //new Zend_Session_Namespace('User');

        if (!$result->isValid()) {

          switch ($result->getCode()) {

                case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                    throw new CMS_Exception("Username not found");
                    break;

                case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                    throw new CMS_Exception("Invalid Password");
                    break;

                default:
                    throw new CMS_Exception("Sorry, your username or password was incorrect");
                    break;
            }
        }

        // store the username, first and last names of the user
        $auth = Zend_Auth::getInstance();
        $storage = $auth->getStorage();
        $storage->write($authAdapter->getResultRowObject(array('id','username')));
        $u = $storage->read();

        $table = new Core_Model_DbTable_Users();
        $user = $table->fetchUserById($u->id);
        $user->login();



        return true;
        
    }

    public function logout(){
        
        

    }

}

