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
 * @subpackage Room
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Subdevice implements HomeNet_Model_Subdevice_Interface {

    public $id = null;
    public $device = null;
    public $model = null;
    public $position = null;
    public $room = null;
    public $order = null;
    public $name = null;
    public $settings = null;
    public $permissions = null;

    public $modelName = null;
    public $driver = null;

    public function __construct(array $config = array()) {
        if (isset($config['data'])) {
            $this->fromArray($config['data']);
        }
        //load model
        if (isset($config['model']) && $config['model'] instanceof HomeNet_Model_SubdeviceModelInterface) {
            $this->loadModel($config['model']);
        }
    }

    public function fromArray(array $array) {

        $vars = array('id', 'device', 'model', 'position', 'device', 'order', 'name', 'settings', 'permissions', 'modelName', 'driver');

        foreach ($array as $key => $value) {
            if (in_array($key, $vars)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray() {

        return array(
            'id' => $this->id,
            'device' => $this->device,
            'model' => $this->model,
            'position' => $this->position,
            'device' => $this->room,
            'order' => $this->order,
            'name' => $this->name,
            'settings' => $this->settings,
            'permissions' => $this->permissions,
            'modelName' => $this->modelName,
            'driver' => $this->driver );
    }

    /**
     * @param HomeNet_Model_SubdeviceModelInterface $model
     */
    public function loadModel(HomeNet_Model_SubdeviceModelInterface $model) {
        $this->modelName = $row->name;
        $this->model = $row->id;
    }

    /**
     * @param HomeNet_Model_Subdevice_Abstract
     */
    public function getDriver() {
         if(empty($this->driver)){
            throw new HomeNet_Model_Exception('Missing Subdevice Driver');
        }

        if(!class_exists($this->driver)){
            throw new HomeNet_Model_Exception('Subdevice Driver '.$this->driver.' Doesn\'t Exist');
        }

        return new $this->driver(array('data' => $this->toArray()));
    }

}
