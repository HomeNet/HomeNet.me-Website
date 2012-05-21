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
        $this->device = new stdClass();
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
        return array('node_models', 'device_models', 'component_models', 'network_types');
    }
    
    public function getGroupAcl() {
        
        
        
        //$config = Zend_Registry::get('config');
       // $guests = $config->site->group->guests;
       // $members = $config->site->group->default;
        
        
        return array(
            array('group' => null, 'module' => 'homenet', 'controller' => 'apikey', 'action' => null, 'permission' => 1),
            array('group' => null, 'module' => 'homenet', 'controller' => 'component', 'action' => null, 'permission' => 1),
            array('group' => null, 'module' => 'homenet', 'controller' => 'device', 'action' => null, 'permission' => 1),
            array('group' => null, 'module' => 'homenet', 'controller' => 'house', 'action' => null, 'permission' => 1),
            array('group' => null, 'module' => 'homenet', 'controller' => 'index', 'action' => null, 'permission' => 1),
            array('group' => null, 'module' => 'homenet', 'controller' => 'node', 'action' => null, 'permission' => 1),
            array('group' => null, 'module' => 'homenet', 'controller' => 'room', 'action' => null, 'permission' => 1),
            array('group' => null, 'module' => 'homenet', 'controller' => 'setup', 'action' => null, 'permission' => 1),
        );
    }

    public function installOptionalContent(array $list) {

        if (in_array('node_models', $list)) {
            $nodeModels = array();
//        array('id'=>0, 'status' => HomeNet_Model_NetworkType::LIVE, 'name'=>'IP (Ethernet/Wifi)', 'plugin'=>'Todo'),
//            array('id'=>1, 'status' => HomeNet_Model_NetworkType::LIVE, 'name'=>'Serial 232', 'plugin'=>'Todo'),
//            array('id'=>2, 'status' => HomeNet_Model_NetworkType::LIVE, 'name'=>'RFM12B 915 mhz', 'plugin'=>'Rfm12b'),
//            array('id'=>3, 'status' => HomeNet_Model_NetworkType::LIVE, 'name'=>'RFM12B 868 mhz', 'plugin'=>'Rfm12b'),
//            array('id'=>4, 'status' => HomeNet_Model_NetworkType::LIVE, 'name'=>'RFM12B 434 mhz', 'plugin'=>'Rfm12b'),
//            array('id'=>5, 'status' => HomeNet_Model_NetworkType::LIVE, 'name'=>'Serial 485 *Placeholder*', 'plugin'=>'Todo'),
//            array('id'=>6, 'status' => HomeNet_Model_NetworkType::LIVE, 'name'=>'Zigbee 2.4 ghz *Placeholder*', 'plugin'=>'ZigBee'),
            
            $statusLights = array('position'=>1,'model'=>16, 'settings'=>null);

            $nodeModels[] = array('id'=>1,  'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::INTERNET,    'network_types' => array(1, 2), 'devices'=>null,           'plugin' => 'Controller', 'name' => 'HomeNet App', 'description' => 'HomeNet Desktop App', 'image' => null, 'max_devices' => 0, 'settings' => null);
            
            $nodeModels[] = array('id'=>2,  'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR, 'network_types' => array(2),    'devices'=>null,           'plugin' => 'Arduino', 'name' => 'Arduino Node, Barebone', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            
            $nodeModels[] = array('id'=>3,  'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'network_types' => array(2),    'devices'=>array($statusLights),  'plugin' => 'Arduino', 'name' => 'Arduino Node, w/Status Lights', 'description' => '', 'image' => null,  'max_devices' => 4,'settings' => null);
            
            $nodeModels[] = array('id'=>4,  'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR,      'network_types' => array(3),    'devices'=>null,           'plugin' => 'Jeenode', 'name' => 'JeeNode (915mhz) Barebone', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            $nodeModels[] = array('id'=>5,  'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR,      'network_types' => array(4),    'devices'=>null,           'plugin' => 'Jeenode', 'name' => 'JeeNode (868mhz) Barebone', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            $nodeModels[] = array('id'=>6,  'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR,      'network_types' => array(5),    'devices'=>null,           'plugin' => 'Jeenode', 'name' => 'JeeNode (433mhz) Barebone', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            
            $nodeModels[] = array('id'=>7,  'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR,      'network_types' => array(3),    'devices'=>array($statusLights),  'plugin' => 'Jeenode', 'name' => 'JeeNode (915mhz)  w/Status Lights', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            $nodeModels[] = array('id'=>8,  'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR,      'network_types' => array(4),    'devices'=>array($statusLights),  'plugin' => 'Jeenode', 'name' => 'JeeNode (868mhz)  w/Status Lights', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            $nodeModels[] = array('id'=>9,  'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::SENSOR,      'network_types' => array(5),    'devices'=>array($statusLights),  'plugin' => 'Jeenode', 'name' => 'JeeNode (433mhz)  w/Status Lights', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            
            //$nodeModels[] = array('id'=>10, 'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'network_types' => array(2, 3), 'devices'=>null,           'plugin' => 'Jeenode', 'name' => 'JeeNode (915mhz) Barebone', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            //$nodeModels[] = array('id'=>11, 'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'network_types' => array(2, 4), 'devices'=>null,           'plugin' => 'Jeenode', 'name' => 'JeeNode (868mhz) Barebone', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            //$nodeModels[] = array('id'=>12, 'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'network_types' => array(2, 5), 'devices'=>null,           'plugin' => 'Jeenode', 'name' => 'JeeNode (433mhz) Barebone', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            
            $nodeModels[] = array('id'=>10, 'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'network_types' => array(2, 3), 'devices'=>array($statusLights),  'plugin' => 'Jeenode', 'name' => 'JeeNode (915mhz) w/Status Lights', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            $nodeModels[] = array('id'=>11, 'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'network_types' => array(2, 4), 'devices'=>array($statusLights),  'plugin' => 'Jeenode', 'name' => 'JeeNode (868mhz) w/Status Lights', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            $nodeModels[] = array('id'=>12, 'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'network_types' => array(2, 5), 'devices'=>array($statusLights),  'plugin' => 'Jeenode', 'name' => 'JeeNode (433mhz) w/Status Lights', 'description' => '', 'image' => null, 'max_devices' => 4, 'settings' => null);
            $statusLights['fixed'] = true;
            $nodeModels[] = array('id'=>13, 'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'network_types' => array(2, 3), 'devices'=>array($statusLights),  'plugin' => 'Jeenode', 'name' => 'JeeLink (915mhz)', 'description' => '', 'image' => null, 'max_devices' => 1, 'settings' => array('node' => 'JeeLink'));
            $nodeModels[] = array('id'=>14, 'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'network_types' => array(2, 4), 'devices'=>array($statusLights),  'plugin' => 'Jeenode', 'name' => 'JeeLink (868mhz)', 'description' => '', 'image' => null, 'max_devices' => 1, 'settings' => array('node' => 'JeeLink'));
            $nodeModels[] = array('id'=>15, 'status' => HomeNet_Model_NodeModel::LIVE, 'type' => HomeNet_Model_Node::BASESTATION, 'network_types' => array(2, 5), 'devices'=>array($statusLights),  'plugin' => 'Jeenode', 'name' => 'JeeLink (433mhz)', 'description' => '', 'image' => null, 'max_devices' => 1, 'settings' => array('node' => 'JeeLink'));
            
            $service = new HomeNet_Model_NodeModel_Service();

            foreach ($nodeModels as $object) {
                $service->create($object);
            }
        }

        if (in_array('device_models', $list)) {
            $deviceModels = array();

            $deviceModels[] = array('id' => 1,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'components'=>array(array('position'=>0,'model'=>1)),  'plugin' => 'StubBool',        'name' => 'Boolean',                'description' => 'Stub for logging Booleans',               'image' => null, 'settings' => array('driver' => 'Boolean'));
            $deviceModels[] = array('id' => 2,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'components'=>array(array('position'=>0,'model'=>2)),  'plugin' => 'StubByte',        'name' => 'Byte',                   'description' => 'Stub for logging byte values',            'image' => null, 'settings' => array('driver' => 'Byte'));
            $deviceModels[] = array('id' => 3,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'components'=>array(array('position'=>0,'model'=>3)),  'plugin' => 'StubInt',         'name' => 'Integer',                'description' => 'Stub for logging integer values',         'image' => null, 'settings' => array('driver' => 'Int'));
            $deviceModels[] = array('id' => 4,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'components'=>array(array('position'=>0,'model'=>4)),  'plugin' => 'StubFloat',       'name' => 'Float',                  'description' => 'Stub for logging float values',           'image' => null, 'settings' => array('driver' => 'Float'));
            $deviceModels[] = array('id' => 5,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'components'=>array(array('position'=>0,'model'=>5)),  'plugin' => 'StubLong',        'name' => 'Long',                   'description' => 'Stub for logging long values',            'image' => null, 'settings' => array('driver' => 'Long'));
            $deviceModels[] = array('id' => 6,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'components'=>array(array('position'=>0,'model'=>11)), 'plugin' => 'StubFloat',       'name' => 'Temperature Sensor',     'description' => 'Stub for logging temperature data,',      'image' => null, 'settings' => array('driver' => 'Temp'));
            $deviceModels[] = array('id' => 7,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 0, 'components'=>array(array('position'=>0,'model'=>6)),  'plugin' => 'StubOnOff',       'name' => 'On/Off',                 'description' => 'Stub for controlling an on/off device',   'image' => null, 'settings' => array('driver' => 'OnOff'));
            $deviceModels[] = array('id' => 8,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 1, 'components'=>array(array('position'=>0,'model'=>11)), 'plugin' => 'GenericSensor',   'name' => 'TMP37 (F)',              'description' => '',                                        'image' => null, 'settings' => array('driver' => 'TMP37'));
            $deviceModels[] = array('id' => 9,  'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 1, 'components'=>array(array('position'=>0,'model'=>11)), 'plugin' => 'Generic',         'name' => 'TMP421 (F)',             'description' => '',                                        'image' => null, 'settings' => array('driver' => 'TMP421'));
            $deviceModels[] = array('id' => 10, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 2, 'components'=>array(array('position'=>0,'model'=>11),
                                                                                                                                  array('position'=>1,'model'=>9, 'name' => 'Humidity')),  
                                                                                                                                                                     'plugin' => 'GenericSensor',   'name' => 'SHT21',                  'description' => '',                                        'image' => null, 'settings' => array('driver' => 'SHT21'));
            $deviceModels[] = array('id' => 11, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 3, 'components'=>array(array('position'=>0,'model'=>19)), 'plugin' => 'GenericSensor',   'name' => 'BMP085',                 'description' => '',                                        'image' => null, 'settings' => array('driver' => 'BMP085'));
            $deviceModels[] = array('id' => 12, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 4, 'components'=>array(array('position'=>0,'model'=>9, 'name'=>'Light Level')),  
                                                                                                                                                                     'plugin' => 'GenericSensor',   'name' => 'LDR',                    'description' => '',                                        'image' => null, 'settings' => array('driver' => 'LDR'));
            $deviceModels[] = array('id' => 13, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 6, 'components'=>array(array('position'=>0,'model'=>6)),  'plugin' => 'Generic',         'name' => 'Generic Switch',         'description' => 'Single Light Switch',                     'image' => null, 'settings' => array('driver' => 'Switch'));
            $deviceModels[] = array('id' => 14, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'components'=>array(array('position'=>0,'model'=>12)), 'plugin' => 'CharLcd',         'name' => 'Char LCD',               'description' => '2 line character display',                'image' => null, 'settings' => array('driver' => 'LCD'));
            $deviceModels[] = array('id' => 15, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'components'=>array(array('position'=>0,'model'=>13)), 'plugin' => 'Generic',         'name' => 'Simple Led',             'description' => '',                                        'image' => null, 'settings' => array('driver' => 'LED'));
            $deviceModels[] = array('id' => 16, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'components'=>array(array('position'=>0,'model'=>13, 'name' => 'Red LED'),
                                                                                                                                  array('position'=>1,'model'=>13, 'name' => 'Green LED')), 
                                                                                                                                                                     'plugin' => 'Generic',         'name' => 'Status Lights',          'description' => '',                                        'image' => null, 'settings' => array('driver' => 'StatusLights', 'component0name' => 'Green LED', ));
            $deviceModels[] = array('id' => 17, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'components'=>array(array('position'=>0,'model'=>14)), 'plugin' => 'Generic',         'name' => 'RGB LED',                'description' => '',                                        'image' => null, 'settings' => array('driver' => 'RGBLED'));
            $deviceModels[] = array('id' => 18, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'components'=>array(array('position'=>0,'model'=>15)), 'plugin' => 'Generic',         'name' => 'Mood Light',             'description' => '',                                        'image' => null, 'settings' => array('driver' => 'MoodLight'));
            $deviceModels[] = array('id' => 19, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 7, 'components'=>array(array('position'=>0,'model'=>7)),  'plugin' => 'Generic',         'name' => 'Servo Open/Close',       'description' => '',                                        'image' => null, 'settings' => array('driver' => 'ServoOpenClose', 'includes' => array('Servo')));
            $deviceModels[] = array('id' => 20, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 8, 'components'=>array(array('position'=>0,'model'=>17)), 'plugin' => 'Generic',         'name' => 'Contact Switch: Door',   'description' => '',                                        'image' => null, 'settings' => array('driver' => 'ContactSwitch'));
            $deviceModels[] = array('id' => 21, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 8, 'components'=>array(array('position'=>0,'model'=>16)), 'plugin' => 'Generic',         'name' => 'Contact Switch: Window', 'description' => '',                                        'image' => null, 'settings' => array('driver' => 'ContactSwitch'));
            $deviceModels[] = array('id' => 22, 'status' => HomeNet_Model_DeviceModel::LIVE, 'category' => 8, 'components'=>array(array('position'=>0,'model'=>18)), 'plugin' => 'Generic',         'name' => 'Motion Sensor',          'description' => '',                                        'image' => null, 'settings' => array('driver' => 'MotionSensor'));
            
            $service = new HomeNet_Model_DeviceModel_Service();

            foreach ($deviceModels as $object) {
                $service->create($object);
            }
            
            $this->deviceModel = $service->getObjectById(3);
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
        
        
        if (in_array('network_types', $list)) {
            $network_types = array( //id 	status 	name 	description 	plugin 	settings
            array('id'=>1, 'status' => HomeNet_Model_NetworkType::LIVE, 'class'=>HomeNet_Model_NetworkType::SYSTEM, 'name'=>'xIP (Ethernet/Wifi)', 'plugin'=>'Todo'),
            array('id'=>2, 'status' => HomeNet_Model_NetworkType::LIVE, 'class'=>HomeNet_Model_NetworkType::SYSTEM,  'name'=>'Serial 232', 'plugin'=>'Todo'),
            array('id'=>3, 'status' => HomeNet_Model_NetworkType::LIVE, 'class'=>HomeNet_Model_NetworkType::USER, 'name'=>'RFM12B 915 mhz', 'plugin'=>'Rfm12b'),
            array('id'=>4, 'status' => HomeNet_Model_NetworkType::LIVE, 'class'=>HomeNet_Model_NetworkType::USER, 'name'=>'RFM12B 868 mhz', 'plugin'=>'Rfm12b'),
            array('id'=>5, 'status' => HomeNet_Model_NetworkType::LIVE, 'class'=>HomeNet_Model_NetworkType::USER, 'name'=>'RFM12B 434 mhz', 'plugin'=>'Rfm12b'),
            array('id'=>6, 'status' => HomeNet_Model_NetworkType::LIVE, 'class'=>HomeNet_Model_NetworkType::USER, 'name'=>'Serial 485 *Placeholder*', 'plugin'=>'Todo'),
            array('id'=>7, 'status' => HomeNet_Model_NetworkType::LIVE, 'class'=>HomeNet_Model_NetworkType::USER, 'name'=>'Zigbee 2.4 ghz *Placeholder*', 'plugin'=>'ZigBee'),

           
            );
            
            $service = new HomeNet_Model_NetworkType_Service();

            foreach ($network_types as $object) {
                $service->create($object);
            }
        }
        
    }

    function installTest($list = array()) {

        $this->uninstallTest(); //remove any old data
        $this->installOptionalContent($this->getOptionalContent());
        
        
        
        

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
                'regions' => array(1,2));
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
            
            
             $service = new HomeNet_Model_HouseUser_Service();
            $array = array('user' => Core_Model_User_Manager::getUser()->id,
                           'house' => $this->house->id,
                           'permissions'=>array(HomeNet_Model_HouseUser::PERMISSION_ADMIN)
                    );
           $service->create($array);
            
        }
        
        
        
//        if (in_array('house_user', $list)) {
//
//            $service = new HomeNet_Model_HouseUser_Service;
//            $array = array(
//                'user' => 1,
//                'url' => 'my-house',
//                'name' => 'My House',
//                'description' => 'My Description',
//                'location' => 'My Location',
//                'gps' => '0, 0',
//                'type' => 'house',
//                'regions' => array(1));
//            $this->house = $service->create($array);
//        }
        
        
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

            $service = new HomeNet_Model_Node_Service($this->house->id);
            $array = array(
                'status' => HomeNet_Model_Node::STATUS_LIVE,
                'address' => 1,
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

           // $this->installOptionalContent(array('device_models', 'component_models'));
            
            $service = new HomeNet_Model_Device_Service($this->house->id);
            
            //HomeNet_Model_Component::BOOLEAN, HomeNet_Model_Component::BYTE,   HomeNet_Model_Component::INTEGER, HomeNet_Model_Component::FLOAT,    HomeNet_Model_Component::LONG
            $devices = array();
            $devices['boolean'] = $service->newObjectFromModel(1);
            $devices['byte'] = $service->newObjectFromModel(2);
            $devices['int'] = $service->newObjectFromModel(3);
            $devices['float'] = $service->newObjectFromModel(4);
            $devices['long'] = $service->newObjectFromModel(5);
            
            $count = 1;
            foreach($devices as $key => $object){
                $object->house = $this->house->id;
                $object->setRoomId(1);
                $object->node = $this->node->id;
                $object->position = $count;
                $count++;
                $this->device->$key = $service->create($object);
            }

            
            
 
            
        }
    }

    function uninstallTest() {
        
        $service = new Core_Model_Acl_User_Service();
        $service->deleteByModule('homenet');
        
        $service = new Core_Model_Acl_Group_Service();
        $service->deleteByModule('homenet');
        
        $service = new HomeNet_Model_Apikey_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_Component_Service();
        $service->deleteAll();

        $service = new HomeNet_Model_ComponentModel_Service();
        $service->deleteAll();
        
        $service = new HomeNet_Model_Network_Service();
        $service->deleteAll();
        
        $service = new HomeNet_Model_NetworkType_Service();
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
