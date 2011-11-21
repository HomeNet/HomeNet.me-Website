<?php

error_reporting(E_ALL);
date_default_timezone_set('America/New_York');
ini_set("memory_limit", "16M");
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('APPLICATION_ROOT')
        || define('APPLICATION_ROOT', realpath(dirname(__FILE__) . '/..'));


// Ensure library/ is on include_path
set_include_path(
        APPLICATION_ROOT . '/library' . PATH_SEPARATOR
        . APPLICATION_PATH . '/modules' . PATH_SEPARATOR
        . get_include_path()
);
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'); //
$application->bootstrap();

$config = Zend_Registry::get('config');

function getRequestHeaders() {
    if (function_exists("apache_request_headers")) {
        if ($headers = apache_request_headers()) {
            return $headers;
        }
    }
    $headers = array();
    // Grab the IF_MODIFIED_SINCE header
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        $headers['If-Modified-Since'] = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
    }
    return $headers;
}


//echo $_SERVER['HTTP_REFERER'];

if (isset($_GET['s']) && isset($_GET['h'])) {
    $hash = attachmentHash($_GET['s']);
    if($hash != $_GET['h']){
        die('Invalid Hash '.$hash.' '.$_GET['h']);
    }
} else {
    die('Missing Data');
}
//friendly_url($url)
//breakdown the path and build the path
$source = pathinfo(strtolower($_GET['s']));
$allowed = array('jpg', 'jpeg', 'png', 'bmp', 'gif', 'pdf', 'doc', 'docx', 'xls','xlsx', 'ppt',  'pptx','txt');

if (!in_array($source['extension'], $allowed)) {
    die('Unsupported type');
}
$ext = $source['extension'];

$filePath = $config->site->uploadDirectory . '/' . cleanDir($source['dirname']) . '/' . cleanFilename($source['filename']) . '.' . $source['extension'];

if (!file_exists($filePath)) {

    die('File does not exist '.$filePath);
}


//check cache
header('Cache-Control: private, max-age=10800, pre-check=10800');
header('Pragma: private');
//header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));


$headers = getRequestHeaders();

if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($filePath))) {
    // send the last mod time of the file back
    header("Expires: " . date(DATE_RFC822, strtotime(' 30 days')));
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT', true, 304);
    exit;
}

$mime = array(
    'pdf' => 'application/pdf',
    'txt' => 'text/plain',
    'html' => 'text/html'
);

$type = 'application/octet-stream';
if (array_key_exists($ext, $mime)) {
    $type = $mime[$ext];
}

//header("Pragma: public"); // required

//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//header('Cache-Control: private',false); // required for certain browsers


header('Content-Transfer-Encoding: binary');



//if($ext == 'pdf'){
//    header('Content-Disposition: inline; filename="'.basename($filePath).'";' );
//} else {
    header('Content-Disposition: attachment; filename="'.basename($filePath).'";' );
//}

header('Content-type: '.$type);
header('Content-Length: '.filesize($filePath)); 
readfile($filePath);
exit;
