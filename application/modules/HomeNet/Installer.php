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
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Installer extends CMS_Installer_Abstract {

    public $house;
    public $room;
    public $node;
    public $device;
    public $nodeModel;
    public $deviceModel;

    /*
     * @todo autpo grant privliges
     */

    public function __construct() {
        $this->house = new stdClass;
        $this->room = new stdClass;
        $this->node = new stdClass;
        $this->device = new stdClass;
        $this->nodeModel = new stdClass;
        $this->deviceModel = new stdClass;
    }

    public function getAdminBlocks() {
        return array(
            array('module' => 'Content', 'widget' => 'AdminSections')
        );
    }

    public function getAdminLinks() {
        return array(
            array('title' => 'Category Sets', 'route' => 'content-admin', 'options' => array('controller' => 'category-set')),
            array('title' => 'Content Sections', 'route' => 'content-admin', 'options' => array('controller' => 'section'))
        );
    }

    public function getOptionalContent() {
        return array('node_models', 'device_models', 'component_models');
    }

    public function installOptionalContent(array $list) {

        if (in_array('node_models', $list)) {
            $nodeModels = array();

            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::INTERNET, 'plugin' => 'Processing', 'name' => 'Proccessing HomeNet App', 'description' => 'The original, basic Internet Node', 'image' => '', 'max_devices' => 0, 'settings' => null);
            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'plugin' => 'Arduino', 'name' => 'Arduino Node', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => array('serial' => true));
            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR, 'plugin' => 'Jeenode', 'name' => 'JeeNode (915mhz)', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => array('rf12b' => true, 'rf12b_freq' => 915));
            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR, 'plugin' => 'Jeenode', 'name' => 'JeeNode (433mhz)', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => array('rf12b' => true, 'rf12b_freq' => 433));
            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR, 'plugin' => 'Jeenode', 'name' => 'JeeNode (868mhz)', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => array('rf12b' => true, 'rf12b_freq' => 868));
            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'plugin' => 'Jeenode', 'name' => 'JeeNode (915mhz)', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => array('rf12b' => true, 'rf12b_freq' => 915, 'serial' => true));
            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'plugin' => 'Jeenode', 'name' => 'JeeNode (433mhz)', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => array('rf12b' => true, 'rf12b_freq' => 433, 'serial' => true));
            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'plugin' => 'Jeenode', 'name' => 'JeeNode (868mhz)', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => array('rf12b' => true, 'rf12b_freq' => 868, 'serial' => true));
            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'plugin' => 'Jeenode', 'name' => 'JeeLink (915mhz)', 'description' => '', 'image' => null, 'max_devices' => 1, 'settings' => array('rf12b' => true, 'rf12b_freq' => 915, 'serial' => true, 'node' => 'JeeLink'));
            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'plugin' => 'Jeenode', 'name' => 'JeeLink (433mhz)', 'description' => '', 'image' => null, 'max_devices' => 1, 'settings' => array('rf12b' => true, 'rf12b_freq' => 433, 'serial' => true, 'node' => 'JeeLink'));
            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'plugin' => 'Jeenode', 'name' => 'JeeLink (868mhz)', 'description' => '', 'image' => null, 'max_devices' => 1, 'settings' => array('rf12b' => true, 'rf12b_freq' => 868, 'serial' => true, 'node' => 'JeeLink'));

            $service = new HomeNet_Model_NodeModel_Service();

            foreach ($nodeModels as $object) {
                $service->create($object);
            }
        }

        if (in_array('device_models', $list)) {
            $deviceModels = array();

            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubInt', 'name' => 'Integer', 'description' => 'Stub for storing Integer values (Whole numbers)', 'image' => null, 'settings' => array('components' => array(7), 'driver' => 'Int'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubFloat', 'name' => 'Float', 'description' => 'Stub for storing floats', 'image' => null, 'settings' => array('components' => array(8), 'driver' => 'Float'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubByte', 'name' => 'Byte', 'description' => 'Stub for storing bytes', 'image' => null, 'settings' => array('components' => array(9), 'driver' => 'Byte'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubLong', 'name' => 'Long', 'description' => 'Stub for a Large Value', 'image' => null, 'settings' => array('components' => array(14), 'driver' => 'Long'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubFloat', 'name' => 'Temperature Sensor', 'description' => 'Stub for a Temperature Sensor, Gives the option to convert values between C and F', 'image' => null, 'settings' => array('components' => array(4), 'driver' => 'Temp'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubBool', 'name' => 'Boolean', 'description' => 'Stub for storing Booleans', 'image' => null, 'settings' => array('components' => array(11), 'driver' => 'Boolean'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubOnOff', 'name' => 'On/Off', 'description' => 'Stub for turning a device on and off', 'image' => null, 'settings' => array('components' => array(13), 'driver' => 'OnOff'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 1, 'plugin' => 'GenericSensor', 'name' => 'TMP37 (F)', 'description' => '', 'image' => null, 'settings' => array('components' => array(5), 'driver' => 'TMP37'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 1, 'plugin' => 'Generic', 'name' => 'TMP421 (F)', 'description' => '', 'image' => null, 'settings' => array('components' => array(5), 'driver' => 'TMP421'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 2, 'plugin' => 'GenericSensor', 'name' => 'SHT21', 'description' => '', 'image' => null, 'settings' => array('components' => array(5, 2), 'driver' => 'SHT21', 'component1name' => 'Humidity'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 3, 'plugin' => 'GenericSensor', 'name' => 'BMP085', 'description' => '', 'image' => null, 'settings' => array('components' => array(22), 'driver' => 'BMP085'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 4, 'plugin' => 'GenericSensor', 'name' => 'LDR', 'description' => '', 'image' => null, 'settings' => array('components' => array(2), 'driver' => 'LDR', 'component0name' => 'Light Level'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 6, 'plugin' => 'Generic', 'name' => 'Generic Switch', 'description' => 'Single Light Switch', 'image' => null, 'settings' => array('components' => array(18)));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'CharLcd', 'name' => 'Char LCD', 'description' => '2 line character display', 'image' => null, 'settings' => array('components' => array(6), 'driver' => 'LCD'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'Generic', 'name' => 'Simple Led', 'description' => '', 'image' => null, 'settings' => array('components' => array(3), 'driver' => 'LED'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'Generic', 'name' => 'Status Lights', 'description' => '', 'image' => null, 'settings' => array('components' => array(3, 3), 'driver' => 'StatusLights', 'component0name' => 'Green LED', 'component1name' => 'Red LED'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'Generic', 'name' => 'RGB LED', 'description' => '', 'image' => null, 'settings' => array('components' => array(15), 'driver' => 'RGBLED'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'Generic', 'name' => 'Mood Light', 'description' => '', 'image' => null, 'settings' => array('components' => array(16), 'driver' => 'MoodLight'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'Generic', 'name' => 'Servo Open/Close', 'description' => '', 'image' => null, 'settings' => array('components' => array(17), 'driver' => 'ServoOpenClose', 'includes' => array('Servo')));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 8, 'plugin' => 'Generic', 'name' => 'Contact Switch: Door', 'description' => '', 'image' => null, 'settings' => array('components' => array(20), 'driver' => 'ContactSwitch'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 8, 'plugin' => 'Generic', 'name' => 'Contact Switch: Window', 'description' => '', 'image' => null, 'settings' => array('components' => array(19), 'driver' => 'ContactSwitch'));
            $deviceModels[] = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 8, 'plugin' => 'Generic', 'name' => 'Motion Sensor', 'description' => '', 'image' => null, 'settings' => array('components' => array(21), 'driver' => 'MotionSensor'));

            $service = new HomeNet_Model_DeviceModel_Service();

            foreach ($deviceModels as $object) {
                $service->create($object);
            }
        }
        if (in_array('component_models', $list)) {

            $componentModels = array();

            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic', 'name' => 'Sensor Format Percentage', 'description' => 'Shows a Value 0-100%', 'settings' => array('datatype' => 'byte', 'units' => '%', 'start' => '0', 'end' => '100'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Led', 'name' => 'Simple Led', 'description' => 'Control an LED', 'settings' => array('datatype' => 'byte'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'GenericTemp', 'name' => 'Temperature Sensor C', 'description' => 'Display Temperature in C', 'settings' => array('datatype' => 'float', 'convert' => '', 'units' => 'C'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'GenericTemp', 'name' => 'Temperature Sensor F', 'description' => 'Converts Temperature Sensor C to F', 'settings' => array('datatype' => 'float', 'convert' => 'ctof', 'units' => 'F'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'CharLcd', 'name' => '2 Line Char LCD', 'description' => '', 'settings' => null);
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic', 'name' => 'Generic Int', 'description' => '', 'settings' => array('datatype' => 's:3:"int'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic', 'name' => 'Generic Float', 'description' => '', 'settings' => array('datatype' => 'float'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic', 'name' => 'Generic Byte', 'description' => '', 'settings' => array('datatype' => 's:4:"byte'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic', 'name' => 'Generic Boolean', 'description' => 'Store 0/1, true/false, on/off', 'settings' => array('datatype' => 'boolean'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'GenericOnOff', 'name' => 'Generic On/Off', 'description' => '', 'settings' => array('datatype' => 'boolean', 'on' => 'On', 'off' => 'Off'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic', 'name' => 'Generic Long', 'description' => '', 'settings' => array('datatype' => 'long'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'RGBLED', 'name' => 'RGB', 'description' => '', 'settings' => null);
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'MoodLight', 'name' => 'MoodLight', 'description' => '', 'settings' => null);
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'GenericOpenClose', 'name' => 'Generic Open/Close', 'description' => '', 'settings' => null);
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'GenericSwitch', 'name' => 'Generic Switch', 'description' => '', 'settings' => array('datatype' => 'boolean'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'ContactSwitch', 'name' => 'Contact Switch: Window', 'description' => '', 'settings' => array('datatype' => 'boolean'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'ContactSwitch', 'name' => 'Contact Switch: Door', 'description' => '', 'settings' => array('datatype' => 'boolean'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'MotionSensor', 'name' => 'Motion Sensor', 'description' => '', 'settings' => array('datatype' => 'boolean'));
            $componentModels[] = array('status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic', 'name' => 'Barometric Pressure', 'description' => '', 'settings' => array('datatype' => 'long', 'units' => 'Pa'));

            $service = new HomeNet_Model_ComponentModel_Service();

            foreach ($componentModels as $object) {
                $service->create($object);
            }
        }
    }

    function installTest($list = array()) {

        $this->uninstallTest(); //remove any old data
        //$this->installOptionalContent($this->getOptionalContent());

        if (in_array('house', $list)) {

            $service = new HomeNet_Model_House_Service;
            $array = array(
                'status' => 1,
                'url' => 'my-house',
                'name' => 'My House',
                'description' => 'My Description',
                'location' => 'My Location',
                'gps' => '0, 0',
                'type' => 'house',
                'regions' => array(1));
            $this->house->test = $service->create($array);


            $array = array(
                'status' => 1,
                'url' => 'my-house2',
                'name' => 'My House2',
                'description' => 'My Description2',
                'location' => 'My Location2',
                'gps' => '0, 0',
                'type' => 'house',
                'regions' => array(1,2));
            $this->house->test2 = $service->create($array);
        }
        
        if (in_array('room', $list)) {

            $service = new HomeNet_Model_Room_Service;
             $array = array(
            'house' => $this->house->test->id,
            'region' => '1',
            'name' => 'My Room',
            'description' => 'My Description');

            $this->room->test = $service->create($array);


             $array = array(
            'house' => $this->house->test->id,
            'region' => '2',
            'name' => 'My Room2',
            'description' => 'My Description2');

            $this->room->test2 = $service->create($array);
        }

        if (in_array('node', $list)) {
            $service = new HomeNet_Model_NodeModel_Service;
            $array = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR, 'plugin' => 'Jeenode', 'name' => 'JeeNode (915mhz)', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => array('rf12b' => true, 'rf12b_freq' => 915));
            $this->nodeModel->test = $service->create($array);

            $service = new HomeNet_Model_Node_Service;
            $array = array('address' => 1,
                'model' => $this->nodeModel->test->id,
                'uplink' => 1,
                'house' => $this->house->test->id,
                'room' => $this->room->test->id,
                'description' => 'test description',
                'settings' => array('key' => 'value'));
            $this->node->test = $service->create($array);
        }

        if (in_array('device', $list)) {

            $service = new HomeNet_Model_DeviceModel_Service;
            $array = array('status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubInt', 'name' => 'Integer', 'description' => 'Stub for storing Integer values (Whole numbers)', 'image' => null, 'settings' => array('components' => array(7), 'driver' => 'Int'));
            $this->deviceModel->test = $service->create($array);

            $service = new HomeNet_Model_Device_Service;
            $array = array(
                'node' => $this->node->test->id,
                'model' => $this->deviceModel->test->id,
                'position' => 1);
            $this->device->test = $service->create($array);
        }
    }

    function uninstallTest() {
        $service = new HomeNet_Model_Apikey_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_Component_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_ComponentModel_Service();
        $service->deleteAll();

        //$service = new HomeNet_Model_Datapoint_Service();
        //$service->deleteAll();

        $service = new HomeNet_Model_Device_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_DeviceModel_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_House_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_HouseUser_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_Message_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_Node_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_NodeModel_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_Room_Service();
        $service->deleteAll();
    }

}
