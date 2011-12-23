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
    public $house2;
    public $room;
    public $node;
    public $device;
    public $nodeModel;
    public $deviceModel;
     public $apikey;

    /*
     * @todo autpo grant privliges
     */

    public function __construct() {

    }

    public function getAdminBlocks() {
        return array(
          //  array('module' => 'Content', 'widget' => 'AdminSections')
        );
    }

    public function getAdminLinks() {
        return array(
            array('title' => 'Node Models',      'route' => 'homenet-admin', 'options' => array('controller' => 'node-model')),
            array('title' => 'Device Models',    'route' => 'homenet-admin', 'options' => array('controller' => 'device-model')),
            array('title' => 'Component Models', 'route' => 'homenet-admin', 'options' => array('controller' => 'component-model'))
        );
    }

    public function getOptionalContent() {
        return array('node_models', 'device_models', 'component_models');
    }

    public function installOptionalContent(array $list) {

        if (in_array('node_models', $list)) {
            $nodeModels = array();

            $nodeModels[] = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::INTERNET, 'plugin' => 'Processing', 'name' => 'Proccessing HomeNet App', 'description' => 'The original, basic Internet Node', 'image' => null, 'max_devices' => 0, 'settings' => null);
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

            $deviceModels[] = array('id' => 1,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubBool', 'name' => 'Boolean',                  'description' => 'Stub for logging Booleans',               'image' => null, 'settings' => array('components' => array(1),      'driver' => 'Boolean'));
            $deviceModels[] = array('id' => 2,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubByte', 'name' => 'Byte',                     'description' => 'Stub for logging byte values',            'image' => null, 'settings' => array('components' => array(2),      'driver' => 'Byte'));
            $deviceModels[] = array('id' => 3,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubInt', 'name' => 'Integer',                   'description' => 'Stub for logging integer values',         'image' => null, 'settings' => array('components' => array(3),      'driver' => 'Int'));
            $deviceModels[] = array('id' => 4,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubFloat', 'name' => 'Float',                   'description' => 'Stub for logging float values',           'image' => null, 'settings' => array('components' => array(4),      'driver' => 'Float'));
            $deviceModels[] = array('id' => 5,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubLong', 'name' => 'Long',                     'description' => 'Stub for logging long values',            'image' => null, 'settings' => array('components' => array(5),      'driver' => 'Long'));
            $deviceModels[] = array('id' => 6,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubFloat', 'name' => 'Temperature Sensor',      'description' => 'Stub for logging temperature data, Gives the option to convert values between C and F', 'image' => null, 'settings' => array('components' => array(11), 'driver' => 'Temp'));
            $deviceModels[] = array('id' => 7,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'plugin' => 'StubOnOff', 'name' => 'On/Off',                  'description' => 'Stub for controlling an on/off device',   'image' => null, 'settings' => array('components' => array(6),      'driver' => 'OnOff'));
            $deviceModels[] = array('id' => 8,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 1, 'plugin' => 'GenericSensor', 'name' => 'TMP37 (F)',           'description' => '',                                        'image' => null, 'settings' => array('components' => array(11),     'driver' => 'TMP37'));
            $deviceModels[] = array('id' => 9,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 1, 'plugin' => 'Generic', 'name' => 'TMP421 (F)',                'description' => '',                                        'image' => null, 'settings' => array('components' => array(11),     'driver' => 'TMP421'));
            $deviceModels[] = array('id' => 10, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 2, 'plugin' => 'GenericSensor', 'name' => 'SHT21',               'description' => '',                                        'image' => null, 'settings' => array('components' => array(11, 9),  'driver' => 'SHT21', 'component1name' => 'Humidity'));
            $deviceModels[] = array('id' => 11, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 3, 'plugin' => 'GenericSensor', 'name' => 'BMP085',              'description' => '',                                        'image' => null, 'settings' => array('components' => array(19),     'driver' => 'BMP085'));
            $deviceModels[] = array('id' => 12, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 4, 'plugin' => 'GenericSensor', 'name' => 'LDR',                 'description' => '',                                        'image' => null, 'settings' => array('components' => array(9),      'driver' => 'LDR', 'component0name' => 'Light Level'));
            $deviceModels[] = array('id' => 13, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 6, 'plugin' => 'Generic', 'name' => 'Generic Switch',            'description' => 'Single Light Switch',                     'image' => null, 'settings' => array('components' => array(6),      'driver' => 'Switch'));
            $deviceModels[] = array('id' => 14, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'CharLcd', 'name' => 'Char LCD',                  'description' => '2 line character display',                'image' => null, 'settings' => array('components' => array(12),     'driver' => 'LCD'));
            $deviceModels[] = array('id' => 15, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'Generic', 'name' => 'Simple Led',                'description' => '',                                        'image' => null, 'settings' => array('components' => array(13),     'driver' => 'LED'));
            $deviceModels[] = array('id' => 16, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'Generic', 'name' => 'Status Lights',             'description' => '',                                        'image' => null, 'settings' => array('components' => array(13, 13), 'driver' => 'StatusLights', 'component0name' => 'Green LED', 'component1name' => 'Red LED'));
            $deviceModels[] = array('id' => 17, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'Generic', 'name' => 'RGB LED',                   'description' => '',                                        'image' => null, 'settings' => array('components' => array(14),     'driver' => 'RGBLED'));
            $deviceModels[] = array('id' => 18, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'Generic', 'name' => 'Mood Light',                'description' => '',                                        'image' => null, 'settings' => array('components' => array(15),     'driver' => 'MoodLight'));
            $deviceModels[] = array('id' => 19, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'plugin' => 'Generic', 'name' => 'Servo Open/Close',          'description' => '',                                        'image' => null, 'settings' => array('components' => array(7),      'driver' => 'ServoOpenClose', 'includes' => array('Servo')));
            $deviceModels[] = array('id' => 20, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 8, 'plugin' => 'Generic', 'name' => 'Contact Switch: Door',      'description' => '',                                        'image' => null, 'settings' => array('components' => array(17),     'driver' => 'ContactSwitch'));
            $deviceModels[] = array('id' => 21, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 8, 'plugin' => 'Generic', 'name' => 'Contact Switch: Window',    'description' => '',                                        'image' => null, 'settings' => array('components' => array(16),     'driver' => 'ContactSwitch'));
            $deviceModels[] = array('id' => 22, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 8, 'plugin' => 'Generic', 'name' => 'Motion Sensor',             'description' => '',                                        'image' => null, 'settings' => array('components' => array(18),     'driver' => 'MotionSensor'));

            $service = new HomeNet_Model_DeviceModel_Service();

            foreach ($deviceModels as $object) {
                $service->create($object);
            }
        }
        if (in_array('component_models', $list)) {

            $componentModels = array();

            $componentModels[] = array('id' => 1,  'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic',           'name' => 'Generic Boolean',        'datatype'=> HomeNet_Model_Component::BOOLEAN,  'description' => 'Logs boolean value 0/1, true/false',      'settings' => null);
            $componentModels[] = array('id' => 2,  'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic',           'name' => 'Generic Byte',           'datatype'=> HomeNet_Model_Component::BYTE,     'description' => 'Logs byte value 0 to 255',                'settings' => null);
            $componentModels[] = array('id' => 3,  'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic',           'name' => 'Generic Int',            'datatype'=> HomeNet_Model_Component::INTEGER,  'description' => 'Logs Integer Values -10,000 to +10,000',  'settings' => null);
            $componentModels[] = array('id' => 4,  'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic',           'name' => 'Generic Float',          'datatype'=> HomeNet_Model_Component::FLOAT,    'description' => 'Logs Floating point Number 12.34',        'settings' => null);
            $componentModels[] = array('id' => 5,  'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic',           'name' => 'Generic Long',           'datatype'=> HomeNet_Model_Component::LONG,     'description' => 'Logs Long Numbers, ',                     'settings' => null);
            $componentModels[] = array('id' => 6,  'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'GenericOnOff',      'name' => 'Generic On/Off',         'datatype'=> HomeNet_Model_Component::BOOLEAN,  'description' => 'On/Off Control',                          'settings' => array('on' => 'On', 'off' => 'Off'));
            $componentModels[] = array('id' => 7,  'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'GenericOpenClose',  'name' => 'Generic Open/Close',     'datatype'=> HomeNet_Model_Component::BOOLEAN,  'description' => 'Open/Close Control',                      'settings' => null);
            $componentModels[] = array('id' => 8,  'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'GenericSwitch',     'name' => 'Generic Switch',         'datatype'=> HomeNet_Model_Component::BOOLEAN,  'description' => 'On/Off Control',                          'settings' => null);
            $componentModels[] = array('id' => 9,  'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic',           'name' => 'Generic Percentage',     'datatype'=> HomeNet_Model_Component::BYTE,     'description' => 'Logs a value 0-100%',                     'settings' => array('units' => '%', 'start' => '0', 'end' => '100'));
            $componentModels[] = array('id' => 10, 'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'GenericTemp',       'name' => 'Temperature Sensor C',   'datatype'=> HomeNet_Model_Component::FLOAT,    'description' => 'Logs Temperature in C',                   'settings' => array('units' => 'C', 'convert' => '' ));
            $componentModels[] = array('id' => 11, 'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'GenericTemp',       'name' => 'Temperature Sensor F',   'datatype'=> HomeNet_Model_Component::FLOAT,    'description' => 'Logs Temperature in F',                   'settings' => array('units' => 'F', 'convert' => 'ctof'));
            $componentModels[] = array('id' => 12, 'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'CharLcd',           'name' => '2 Line Char LCD',        'datatype'=> HomeNet_Model_Component::STRING,   'description' => 'Control a Character LCD Display',         'settings' => null);
            $componentModels[] = array('id' => 13, 'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Led',               'name' => 'Simple Led',             'datatype'=> HomeNet_Model_Component::BYTE,     'description' => 'Control a LED\'s Brightness',             'settings' => null);
            $componentModels[] = array('id' => 14, 'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'RGBLED',            'name' => 'RGB',                    'datatype'=> HomeNet_Model_Component::NONE,     'description' => 'Control a Red/Green/Blue LED',            'settings' => null);
            $componentModels[] = array('id' => 15, 'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'MoodLight',         'name' => 'MoodLight',              'datatype'=> HomeNet_Model_Component::NONE,     'description' => 'Control a Moodlight',                     'settings' => null);
            $componentModels[] = array('id' => 16, 'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'ContactSwitch',     'name' => 'Contact Switch: Window', 'datatype'=> HomeNet_Model_Component::BOOLEAN,  'description' => 'Logs a window&quot;s State',              'settings' => null);
            $componentModels[] = array('id' => 17, 'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'ContactSwitch',     'name' => 'Contact Switch: Door',   'datatype'=> HomeNet_Model_Component::BOOLEAN,  'description' => 'Logs a door&quot;s state',                'settings' => null);
            $componentModels[] = array('id' => 18, 'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'MotionSensor',      'name' => 'Motion Sensor',          'datatype'=> HomeNet_Model_Component::BOOLEAN,  'description' => 'Logs motion activity',                    'settings' => null);
            $componentModels[] = array('id' => 19, 'status' => HomeNet_Model_ComponentModel::LIVE, 'plugin' => 'Generic',           'name' => 'Barometric Pressure',    'datatype'=> HomeNet_Model_Component::LONG,     'description' => 'Logs barometic pressure in Pa',           'settings' => array('units' => 'Pa'));

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
            $this->house = $service->create($array);


            $array = array(
                'status' => 1,
                'url' => 'my-house2',
                'name' => 'My House2',
                'description' => 'My Description2',
                'location' => 'My Location2',
                'gps' => '0, 0',
                'type' => 'house',
                'regions' => array(1,2));
            $this->house2 = $service->create($array);
        }
        if (in_array('apikey', $list)) {
            $service = new HomeNet_Model_Apikey_Service;
            $this->apikey = $service->createApikeyForHouse($this->house->id);
        }
        
        if (in_array('room', $list)) {

            $service = new HomeNet_Model_Room_Service;
             $array = array(
            'house' => $this->house->id,
            'region' => '1',
            'name' => 'My Room',
            'description' => 'My Description');

            $this->room = $service->create($array);


             $array = array(
            'house' => $this->house->id,
            'region' => '2',
            'name' => 'My Room2',
            'description' => 'My Description2');

            $this->room = $service->create($array);
        }

        if (in_array('node', $list)) {
            $service = new HomeNet_Model_NodeModel_Service;
            $array = array('status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR, 'plugin' => 'Jeenode', 'name' => 'JeeNode (915mhz)', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => array('rf12b' => true, 'rf12b_freq' => 915));
            $this->nodeModel = $service->create($array);

            $service = new HomeNet_Model_Node_Service;
            $array = array('address' => 1,
                'model' => $this->nodeModel->id,
                'uplink' => 1,
                'house' => $this->house->id,
                'room' => $this->room->id,
                'description' => 'test description',
                'settings' => array('key' => 'value'));
            $this->node = $service->create($array);
            //var_dump($this->node);
            //exit;
        }

        if (in_array('device', $list)) {

            $this->installOptionalContent(array('device_models', 'component_models'));

            $service = new HomeNet_Model_Device_Service;
            $object = $service->newObjectFromModel(3);
            $object->house = $this->house->id;
            $object->room = 1;
            $object->node = $this->node->id;
            $object->position = 1;
 
            $this->device = $service->create($object);
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
