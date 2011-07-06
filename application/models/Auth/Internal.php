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
    public function hashPassword($password) {
        return sha1('saltisgood' . $password);
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
        $table = new Core_Model_DbTable_AuthInternal();
        $row = $table->createRow();
        //     echo $username;

        $row->id = $credentials['id'];
        $row->username = $credentials['username'];
        $row->password = $this->hashPassword($credentials['password']);
        try {
            $row->save();
        } catch (Exception $e) {
            if (strstr($e->getMessage(), '1062 Duplicate')) {
                throw new DuplicateEntryException("URL Already Exists");
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

        // get the default db adapter
        $db = Zend_Db_Table::getDefaultAdapter();
        //create the auth adapter
        $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'auth_internal', 'username', 'password');
        //set the username and password
        $authAdapter->setIdentity($credentials['username']);
        $authAdapter->setCredential($this->hashPassword($credentials['password']));
        //authenticate
        $result = $authAdapter->authenticate();

        //new Zend_Session_Namespace('User');

        if (!$result->isValid()) {

            switch ($result->getCode()) {

                case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                    throw new NotFoundException("Username not found");
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
        $storage->write($authAdapter->getResultRowObject(array('id', 'username')));
        $u = $storage->read();

        //$uService = new Core_Model_User_Service();
        //$user = $uService->getObjectById($u->id);
        //$user->login();

        return $u->id;
    }

    public function delete($id) {
        $table = new Core_Model_DbTable_AuthInternal();
        $table->find($id)->current()->delete();
    }

    public function logout() {
        $authAdapter = Zend_Auth::getInstance();
        $authAdapter->clearIdentity();
        $sessions = Zend_Session::destroy(true);
    }

    public function deleteAll() {

        if (APPLICATION_ENV == 'testing') {
            $table = new Core_Model_DbTable_AuthInternal();
            $table->getAdapter()->query('TRUNCATE TABLE `' . $table->info('name') . '`');
        }
    }

}

