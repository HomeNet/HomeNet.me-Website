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
class Core_Model_User implements Core_Model_User_Interface {

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $status = 0;

    /**
     * @var int
     */
    public $primary_group = null;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $name;

<<<<<<< .mine    public function setSetting($setting, $value, $module = 'Core'){

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
    
    
//    public function login() {
//
//        if(!$this->isConnected() || empty($this->id)){
//            throw new Zend_Exception("User Not Loaded");
//        }
//
//        $_SESSION['User'] = $this->toArray();
//        $_SESSION['User']['settings'] = $this->getSettings();
//
//         if($this->status == -1){
//             $this->logout();
//            throw new CMS_Exception("Account Not Activated", self::ERROR_NOT_ACTIVATED);
//        } elseif($this->status < -1){
//            $this->logout();
//            throw new CMS_Exception("User Banned", self::ERROR_BANNED);
//        }
//
//        
//        $_SESSION['User'] = $this->toArray();
//        $_SESSION['User']['settings'] = $this->getSettings();
//
//    }
///**
// * @todo move this to an auth model
// */
//    public function logout() {
//
//        //Zend_Session::destroy(true);
//
//        $authAdapter = Zend_Auth::getInstance();
//        $authAdapter->clearIdentity();
//    }

=======>>>>>>> .theirs    /**
     * @var string
     */
    public $location;

    /**
     * @var string
     */
    public $email;

    /**
     * @var Zend_Date
     */
    public $created = null;

    /**
     * @var CMS_ACL
     */
    public $permissions;

    /**
     * @var array
     */
    public $settings = array();

    /**
     * @var array
     */
    public $memberships;

    public function __construct(array $config = array()) {
        if (isset($config['data'])) {
            $this->fromArray($config['data']);
        }
    }

    public function fromArray(array $array) {

        $vars = get_object_vars($this);

        // die(debugArray($vars));

        foreach ($array as $key => $value) {
            if (array_key_exists($key, $vars)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray() {

        return get_object_vars($this);
    }

    public function getSetting($setting) {
        if (isset($this->settings[$setting])) {
            return $this->settings[$setting];
        }
        return null;
    }

    public function setSetting($setting, $value) {
        if (is_null($this->settings)) {
            $this->settings = array($setting => $value);
            return;
        }
        //die(debugArray($this->settings));

        $this->settings = array_merge($this->settings, array($setting => $value));
    }

    public function clearSetting($setting) {
        unset($this->settings[$setting]);
    }

    public function getMemberships() {
        if (is_null($this->memberships)) {
            $mService = new Core_Model_User_Membership_Service();
            $this->memberships = $mService->getGroupIdsByUser($this->id);

            //add 'everyone' group to the membership list
            $config = Zend_Registry::get('config');
            array_unshift($this->memberships, $config->site->group->everyone);
        }
        return $this->memberships;
    }
    
    public function getRoleId(){
        return 'u'.(string) $this->id;
    }

}