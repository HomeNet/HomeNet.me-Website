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
 * @package HomeNet
 * @subpackage Nodes
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Node extends HomeNet_Model_Node_Abstract {
    
public $type = HomeNet_Model_Node_Service::INTERNET;

//    public $id = null;
//    public $node;
//    public $model;
//    public $uplink;
//    public $house;
//    public $room;
//    public $description;
//    public $created;
//    public $settings;
//
//    public $modelName = null;
//    public $driver = null;
//
//    public function  __construct(array $config = array()) {
//        if(isset($config['data'])){
//            $this->fromArray($config['data']);
//        }
//        //load model
//        if (isset($config['model']) && $config['model'] instanceof HomeNet_Model_ComponentModel_Interface) {
//            $this->loadModel($config['model']);
//        }
//    }
//
//   public function fromArray(array $array) {
//
//        $vars = array('id', 'node', 'model', 'uplink', 'house', 'room', 'description', 'created', 'settings', 'modelName', 'driver');
//
//        foreach ($array as $key => $value) {
//            if (in_array($key, $vars)) {
//                $this->$key = $value;
//            }
//        }
//    }
//
//    /**
//     * @return array
//     */
//    public function toArray() {
//
//        return array(
//            'id' => $this->id,
//            'node' => $this->node,
//            'model' => $this->model,
//            'uplink' => $this->uplink,
//            'house' => $this->house,
//            'room' => $this->room,
//            'description' => $this->description,
//            'created' => $this->created,
//            'settings' => $this->settings,
//            'modelName' => $this->modelName,
//            'driver' => $this->driver );
//    }
//
//    /**
//     * @param HomeNet_Model_Node_Abstract
//     */
//    public function getDriver() {
//         if(empty($this->driver)){
//            throw new HomeNet_Model_Exception('Missing Node Driver');
//        }
//
//        if(!class_exists($this->driver)){
//            throw new HomeNet_Model_Exception('Node Driver '.$this->driver.' Doesn\'t Exist');
//        }
//
//        return new $this->driver(array('data' => $this->toArray()));
//    }
    public function getMaxPorts(){
        return 0;
    }


}
