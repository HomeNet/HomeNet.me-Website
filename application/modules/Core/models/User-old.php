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
class Core_Model_User extends Zend_Db_Table_Row_Abstract {

    const ERROR_BANNED = 2;
    const ERROR_NOT_ACTIVATED = 1;

    private $_settings = null;

    public function getSetting($setting, $module = 'Core'){

        if(is_null($this->_settings)){
            $this->loadSettings();
        }

        if(!empty($this->_settings[$module][$setting])){
            return $this->_settings[$module][$setting];
        }
        return null;
    }

    public function setSetting($setting, $value, $module = 'Core'){

        if(is_null($this->_settings)){
            $this->loadSettings();
        }

        $this->_settings[$module][$setting] = $value;
    }

    public function loadSettings(){

        if(empty($this->settings)){
            $this->_settings = array();
            return;
        } 

        $this->_settings = unserialize($this->settings);
    }

    public function saveSettings(){
        $this->settings = serialize($this->_settings);
    }

    public function getSettings(){
        return $this->_settings;
    }

    public function removeSetting($setting, $module = 'Core'){

        if(is_null($this->_settings)){
            $this->loadSettings();
        }

        if(isset($this->_settings[$module][$setting])){
            unset($this->_settings[$module][$setting]);
        }
        return true;
    }


    public function importArray($array) {

        $this->name = $array['name'];
        $this->location = $array['location'];
        $this->username = $array['username'];
        $this->email = $array['email'];       

        $this->permissions = '';
    }
    
    
    public function login() {

        if(!$this->isConnected() || empty($this->id)){
            throw new Zend_Exception("User Not Loaded");
        }

        $_SESSION['User'] = $this->toArray();
        $_SESSION['User']['settings'] = $this->getSettings();

         if($this->status == -1){
             $this->logout();
            throw new CMS_Exception("Account Not Activated", self::ERROR_NOT_ACTIVATED);
        } elseif($this->status < -1){
            $this->logout();
            throw new CMS_Exception("User Banned", self::ERROR_BANNED);
        }

        
        $_SESSION['User'] = $this->toArray();
        $_SESSION['User']['settings'] = $this->getSettings();

    }
/**
 * @todo move this to an auth model
 */
    public function logout() {

        //Zend_Session::destroy(true);

        $authAdapter = Zend_Auth::getInstance();
        $authAdapter->clearIdentity();
    }

    /**
     * @param Array $values User Info
     * @return boolean
     */
    public function add($values = null) {
        
        if(!$this->isConnected() || !empty($this->id)){
            throw new Zend_Exception("User Not Loaded");
        }       
        
        if(is_array($values)){
            $this->importArray($values);
        }
        
        /**
         * @todo check to make sure username doesn't exsist
         */
        $this->save();

        $auth = new Core_Model_AuthInternal();
        $auth->add($this->id, $this->username, $values['password']);

        $this->sendActivationEmail();
    }

    /**
     * @param int $id User ID
     * @param Array $user User Info
     * @return boolean
     */
    public function update($values = null) {

        if(!$this->isConnected() || empty($this->id)){
            throw new Zend_Exception("User Not Loaded");
        }

        if(is_array($values)){
            $this->importArray($values);
        }

        $this->save();
    }

    /**
     * @param string $oldpassword
     * @param string $newpassword
     * @return boolean
     */
    public function changePassword($oldpassword, $newpassword) {
/*
        if(!$this->isConnected() || empty($this->id)){
            throw new Zend_Exception("User Not Loaded");
        }

        if($this->password !== $this->hashPassword($oldpassword)){
            throw new CMS_Exception("Old password doesn't match");
        }

        $this->password = $this->hashPassword($newpassword);
        $this->save();*/
    }


    /**
     * @param int $id User ID
     */
    public function save() {
        $this->saveSettings();
        parent::save();

    }

    /**
     * @param int $id User ID
     */
    public function delete() {
        /**
         * @todo also delete auth table entries
         * probably should setup the zend_table to cascade deletes
         */
        parent::delete();
        
    }

    /**
     * @param string $key
     * @throw CMS_Exception
     */
    public function sendActivationEmail() {

        if (!$this->isConnected() || empty($this->id)) {
            throw new Zend_Exception("User Not Loaded");
        }

        if ($this->status > 0) {
            // throw new CMS_Exception("User Already Activated");
        }

        $key = uniqid('', true);

        $this->setSetting('activationKey', $key);
        $this->save();

        //send email
        $mail = new CMS_HtmlEmail();
        $mail->setSubject('Activate your HomeNet.me Account');
        $mail->addTo($this->email, $this->name);

        $mail->setViewParam('id', $this->id);
        $mail->setViewParam('name', $this->name);
        $mail->setViewParam('email', $this->email);
        $mail->setViewParam('username', $this->username);

        $url = Zend_Layout::getMvcInstance()->getView()->url(array('user' => $this->id, 'action'=>'activate', 'key' => $key), 'core-user');

        $mail->setViewParam('activationUrl', $url);

        $mail->sendHtmlTemplate('activate.phtml');



    }


    /**
     * @param string $key
     * @throw CMS_Exception
     */
    public function activate($key) {

        if(!$this->isConnected() || empty($this->id)){
            throw new Zend_Exception("User Not Loaded");
        }

        if($this->status > 0){
            throw new CMS_Exception("User Already Activated");
        }

        $userkey = $this->getSetting('activationKey');

        if($userkey === $key){
            $this->status = 1;
            $this->save();
            return;
        }

        throw new CMS_Exception("Invalid Activation Key");
    }

}