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
ini_set("memory_limit", "32M");
error_reporting(E_ALL);
date_default_timezone_set('America/New_York');
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment

if(isset($_GET['setenv']) && $_GET['setenv'] == 'testing'){
    define('APPLICATION_ENV', 'testing');
}


defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('APPLICATION_ROOT')
        || define('APPLICATION_ROOT', realpath(dirname(__FILE__) . '/..'));


// Ensure library/ is on include_path
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/modules'),
            get_include_path(),
        )));

//require_once 'Zend/Loader/Autoloader.php';
//$autoloader = Zend_Loader_Autoloader::getInstance();
//$autoloader->setDefaultAutoloader(create_function('$class', "include str_replace('_', '/', \$class) . '.php';"));

/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'); //
$application->bootstrap();
Zend_Registry::set('cachemanager', $application->getBootstrap()->getResource('cachemanager'));


$config = Zend_Registry::get('config');

$cacheFile = $config->site->cacheDirectory . '/xmlrpc.cache';
$server = new Zend_XmlRpc_Server();
Zend_XmlRpc_Server_Fault::attachFaultException('NotFoundException');
Zend_XmlRpc_Server_Fault::attachFaultException('InvalidArgumentException');
Zend_XmlRpc_Server_Fault::attachFaultException('NotSupportedException');
Zend_XmlRpc_Server_Fault::attachFaultException('UnexpectedValueException');

if(APPLICATION_ENV == 'testing'){
    Zend_XmlRpc_Server_Fault::attachFaultException('Exception');
}

 
if (!Zend_XmlRpc_Server_Cache::get($cacheFile, $server)) {

    $server->setClass('HomeNet_Model_Apikey_XmlRpc', 'homenet.apikey');
    $server->setClass('HomeNet_Model_Packet_XmlRpc', 'homenet.packet');
    $server->setClass('HomeNet_Model_Test_XmlRpc', 'homenet.test');
 
    Zend_XmlRpc_Server_Cache::save($cacheFile, $server);
}
//echo '<pre>';
//print_r(unserialize(file_get_contents($cacheFile)));
//exit;
echo $server->handle();