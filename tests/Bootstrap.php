<?php
//
//// Define path to application directory
//defined('APPLICATION_PATH')
//    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
//
//// Define application environment
//defined('APPLICATION_ENV')
//    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));
//
//// Ensure library/ is on include_path
//set_include_path(implode(PATH_SEPARATOR, array(
//    realpath(APPLICATION_PATH . '/../library'),
//    get_include_path(),
//)));
//
//require_once 'Zend/Loader/Autoloader.php';
//Zend_Loader_Autoloader::getInstance();

echo "loading bootstrap\n";

ini_set("memory_limit", "256M");
error_reporting(E_ALL);
date_default_timezone_set('America/New_York');
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
echo APPLICATION_PATH."\n";
// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));
echo APPLICATION_ENV."\n";
defined('APPLICATION_ROOT')
        || define('APPLICATION_ROOT', realpath(dirname(__FILE__) . '/..'));

echo APPLICATION_ROOT."\n";
// Ensure library/ is on include_path
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/../application/modules'),
            get_include_path(),
        )));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setDefaultAutoloader(create_function('$class', "include str_replace('_', '/', \$class) . '.php';"));

///** Zend_Application */
////require_once 'Zend/Application.php';
//// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'); //
$application->bootstrap();
