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
class Core_Model_Auth_Internal implements Core_Model_Auth_Interface {

    /**
     * @param String Password
     * @return String
     */
    public function hashPassword($username, $password) {
        $username = strtolower($username);
        $config = Zend_Registry::get('config');
        $salt = $config->site->salt;
        return sha1($salt . $username . $salt. $username. $password . $salt. $password. $password . $salt . $password . $salt. $salt);
    }

    public function add($credentials) {

        if (empty($credentials['username'])) {
            throw new InvalidArgumentException('Username Required');
        }

        if (empty($credentials['password'])) {
            throw new InvalidArgumentException('Password Required');
        }

        if (empty($credentials['id'])) {
            throw new InvalidArgumentException('User Id Required');
        }

        // create a new row
        $table = new Core_Model_Auth_Internal_DbTable();
        $row = $table->createRow();
        //     echo $username;

        $row->id = $credentials['id'];
        $row->username = strtolower($credentials['username']);
        $row->password = $this->hashPassword($credentials['username'], $credentials['password']);
        try {
            $row->save();
        } catch (Exception $e) {
            if (strstr($e->getMessage(), '1062 Duplicate')) {
                throw new DuplicateEntryException("Username Already Exists");
            } elseif (strstr($e->getMessage(), '1048 Column')) {
                throw new InvalidArgumentException("Invalid Column");
            } else {
                throw new Exception($e->getMessage());
            }
        };
    }

    /**
     *
     * @param array Credentials: Username and Password
     * @return int User Id
     */
    public function login($credentials) {

        if (empty($credentials['username'])) {
            throw new InvalidArgumentException('Username Required');
        }

        if (empty($credentials['password'])) {
            throw new InvalidArgumentException('Password Required');
        }
        
        $credentials['username'] = strtolower($credentials['username']);

        // get the default db adapter
        $db = Zend_Db_Table::getDefaultAdapter();
        //create the auth adapter
        $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'auth_internal', 'username', 'password');
        //set the username and password
        $authAdapter->setIdentity($credentials['username']);
        $authAdapter->setCredential($this->hashPassword($credentials['username'], $credentials['password']));
        //authenticate
        $result = $authAdapter->authenticate();

        //new Zend_Session_Namespace('User');

        if (!$result->isValid()) {

            switch ($result->getCode()) {

                case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                    throw new NotFoundException("Username not found",Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND);
                    break;

                case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                    throw new CMS_Exception("Invalid Password",Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID);
                    break;

                default:
                    throw new CMS_Exception("Sorry, your username or password was incorrect");
                    break;
            }
        }

        // store the username, first and last names of the user
        $auth = Zend_Auth::getInstance();
        $storage = $auth->getStorage();
        $storage->write($authAdapter->getResultRowObject(array('id', 'username')));
        $u = $storage->read();

        //$uService = new Core_Model_User_Service();
        //$user = $uService->getObjectById($u->id);
        //$user->login();

        return $u->id;
    }
    
    public function changeUsername($id, $newUsername, $password) {
        $table = new Core_Model_Auth_Internal_DbTable();
        $object = $table->find($id)->current();
        $object->username = $newUsername;
        $object->password = $this->hashPassword($newUsername, $password);
        $object->save();
    }
    
    public function changePassword($id, $newPassword, $oldPassword = false) {
        $table = new Core_Model_Auth_Internal_DbTable();
        $object = $table->find($id)->current();
        if(($oldPassword === false) || ($object->password ==  $this->hashPassword($object->username,  $oldPassword))){
            $object->password = $this->hashPassword($object->username, $newPassword);
            $object->save();
            return true;
        } 
        return false;
    }

    public function delete($id) {
        $table = new Core_Model_Auth_Internal_DbTable();
        $table->find($id)->current()->delete();
    }

    public function logout() {
//        $authAdapter = Zend_Auth::getInstance();
//        $authAdapter->clearIdentity();
//        $sessions = Zend_Session::destroy(true);
    }

    public function deleteAll() {

         if(APPLICATION_ENV == 'production'){
            throw new Exception("Not Allowed");
        }
            $table = new Core_Model_Auth_Internal_DbTable();
            $table->getAdapter()->query('TRUNCATE TABLE `' . $table->info('name') . '`');
        
    }

}

