<?php

/*
 * Abstract.php
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
 * Base for HomeNet Node Drivers
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
abstract class HomeNet_Model_Network_Abstract implements HomeNet_Model_Network_Interface {

    public $id;
    public $name;
    public $driver;
    public $description = '';
    public $created;
    public $settings = array();

   // public $internet = false;
  //  public $ipaddress;
  //  public $status = 1;
  //  public $direction = 1;

    public $plugin;
    public $type_name;
    public $type_settings = array();
    
    public $type;


    protected $_devices;
    
    //types
    const INTERNET = 3;
    const BASESTATION = 2;
    const SENSOR = 1;
    
    const STATUS_LIVE = 1;
    const STATUS_TRASHED = 0;

    public function  __construct(array $config = array()) {

        //load type
        if (isset($config['type']) && $config['type'] instanceof HomeNet_Model_NetworkType_Interface) {
            $this->loadType($config['type']);
        }

        if(isset($config['data'])){
            $this->fromArray($config['data']);
        }

    }

   public function fromArray(array $array) {

        $vars = array('id', 'status', 'type', 'house', 'description', 'created', 'updated','plugin');

        foreach ($array as $key => $value) {
            if (in_array($key, $vars)) {
                $this->$key = $value;
            }
        }
        
       //merge in type settings
        if(!empty($array['type_settings']) && is_array($array['type_settings'])){
            $this->settings = array_merge($this->settings, $array['type_settings']);
        }
        
        
        if(!empty($array['settings']) && is_array($array['settings'])){
            $this->settings = array_merge($this->settings, $array['settings']);
        }
    }

    /**
     * @return array
     */
    public function toArray() {

        return array(
            'id' => $this->id,
            'type' => $this->type,
            'house' => $this->house,
            'description' => $this->description,
            'created' => $this->created,
            'settings' => $this->settings
           );
    }

    public function loadType(HomeNet_Model_NetworkType_Interface $type){

        $this->type_name = $type->name;
        $this->plugin = $type->plugin;
        $this->type = $type->id;
        //$this->type = $type->type;
        $this->settings = array_merge($this->settings, $type->settings);
    }

    public function getSetting($setting){
        if(!empty($this->settings[$setting])){
            return $this->settings[$setting];
        }
        return null;
    }

    public function setSetting($setting, $value){
        $this->settings[$setting] = $value;
    }

    /**
     * Generate code for the node
     *
     * @return CMS_Form_SubForm
     */
    public function getSetupForm() {

        $form = new CMS_Form_SubForm;


        return $form;
    }

}