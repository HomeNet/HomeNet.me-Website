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
ini_set("memory_limit", "16M");
error_reporting(E_ALL);
date_default_timezone_set('America/New_York');
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

defined('APPLICATION_ROOT')
        || define('APPLICATION_ROOT', realpath(dirname(__FILE__) . '/..'));


// Ensure library/ is on include_path
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/modules'),
            get_include_path(),
        )));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setDefaultAutoloader(create_function('$class', "include str_replace('_', '/', \$class) . '.php';"));

/** Zend_Application */
//require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'); //
$application->bootstrap();

//$application->getBootstrap()->bootstrap('db');
//echo "hello xmlrpc client";

class HomeNetXmlRpc {

    /**
     * hello test
     *
     * @param struct $value Value
     * @return string 
     */
    public function hello($value) {
        return "Hello World";
    }

    /**
     * ping test
     *
     * @param string $value Value
     * @return string
     */
    public function ping($value) {
        return $value;
    }

    /**
     * Add Packet to log
     *
     * @param struct $value Value
     * @return string on success
     */
    public function packet($value) {
        if (empty($_GET['apikey'])) {
            return 'No API Key Supplied';
        }
       
        $aService = new HomeNet_Model_Apikey_Service();


        try {
            $apikey = $aService->validate($_GET['apikey']);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        //return $decoded = htmlspecialchars(print_r($value,1));

        $packet = new HomeNet_Model_Packet();

        try {
            $packet->loadXmlRpc($value);
            $packet->save();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $decoded = htmlspecialchars(print_r($packet->getArray(), 1));
        //$decoded = chunk_split(bin2hex(base64_decode($value['packet'])),2,',');
        //file_put_contents(APPLICATION_PATH . '/packet.log', print_r($packet->toArray(),true)."\r\n Base64 decoded: ".$decoded."\r\n",FILE_APPEND);
        //  return $apikey->house .'-'. $packet->fromNode .'-'. $packet->fromDevice;
try {

        $nService = new HomeNet_Model_Node_Service();

        $node = $nService->getObjectByHouseNode($apikey->house, $packet->fromNode);

        $uplinkNode = $nService->getObjectById($node->uplink);

        if($uplinkNode->ipaddress != $_SERVER['REMOTE_ADDR']){
            $uplinkNode->ipaddress = $_SERVER['REMOTE_ADDR'];
            $nService->update($uplinkNode);
        }

 } catch (Zend_Exception $e) {
            return $e->getMessage();
        }


        $dService = new HomeNet_Model_Device_Service();
   
        try {
            $driver = $dService->getObjectByHouseNodeDevice($apikey->house, $packet->fromNode, $packet->fromDevice);
           // return "true";

            $driver->processPacket($packet);
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }

       // return htmlspecialchars(print_r(error_get_last(),true));

        //return print_r($packet->payload->getValue(),1);

        return "true";
    }

    /**
     * test connection
     *
     * @param string $value Value Ip address
     * @return string on success
     */
    public function testConnection($value) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];

        $client = new Zend_XmlRpc_Client('http://' . $ipaddress . ':2443/RPC2');
        //$client->
        //$arg1 = 5;

        try {

            $result = $client->call('HomeNet.testConnection', "test");
        } catch (Exception $e) {
            //return "Can't access your HomeNet at $ipaddress |Try forwarding port 8081 to ".$value;
            return $e->getMessage();
        }

        return $result;
    }

    /**
     * validate api key
     *
     * @param string $value Value Ip address
     * @return string on success
     */
    public function validateApikey($key) {

        if (empty($_GET['apikey'])) {
            return 'No API Key Supplied';
        }
        if (!preg_match('/\b([a-f0-9]{40})\b/', $_GET['apikey'])) {
            return 'Invalid Format';
        }

        //return 'true';

        $aService = new HomeNet_Model_Apikey_Service();

        try {
            $aService->validate($_GET['apikey']);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        // return $key;
        return 'true';
    }

}

$server = new Zend_XmlRpc_Server();
$server->setClass('HomeNetXmlRpc', 'HomeNet');
echo $server->handle();