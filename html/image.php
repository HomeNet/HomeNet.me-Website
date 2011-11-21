<?php

error_reporting(E_ALL);
date_default_timezone_set('America/New_York');
ini_set("memory_limit", "16M");
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

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



function error($message, $w = 320, $h = 240) {
   // die($message);

    $img = imagecreate($w, $h);
    imagecolorallocate($img, 0, 0, 0);
    $c2 = imagecolorallocate($img, 70, 70, 70);
    $white = imagecolorallocate($img, 255, 255, 255);
    imagestring($img, 5, 10, 0, $message, $white);
    imageline($img, 0, 0, $w, $h, $c2);
    imageline($img, $w, 0, 0, $h, $c2);
    // imagettftext($img, 10, 0, 2, 12, $white, realpath('arial.ttf'), 'Missing Var');
    header("Content-type: image/jpeg");
    imagejpeg($img);
    exit;
}



//echo $_SERVER['HTTP_REFERER'];

if (isset($_GET['s']) && isset($_GET['w']) && isset($_GET['h']) && isset($_GET['t']) && isset($_GET['m'])) {
    $hash = imageHash($_GET['s'], $_GET['w'], $_GET['h'], $_GET['t']);
    if($hash != $_GET['m']){
        error('Invalid Hash', $_GET['w'], $_GET['h']);
    }
    
    
    
} else {
    error('Missing Data');
}
$max_width =  (int)$_GET['w'];
$max_height = (int)$_GET['h'];
$type =       (int)$_GET['t'];

//friendly_url($url)


//breakdown the path and build the path
$source = pathinfo(strtolower($_GET['s']));
$allowed = array('jpg','jpeg','png','bmp','gif');

if(!in_array($source['extension'],$allowed)){
    error('Unsupported image type', $max_width, $max_height);
}

$filePath = $config->site->uploadDirectory . '/' . cleanDir($source['dirname']) .'/'.cleanFilename($source['filename']).'.'.$source['extension'];

if (!file_exists($filePath)) {
    
    error('missing image', $max_width, $max_height);
}


//check cache
header("Cache-Control: private, max-age=10800, pre-check=10800");
header("Pragma: private");
//header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));


$headers = getRequestHeaders();

if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($filePath))) {
    // send the last mod time of the file back
    header("Expires: " . date(DATE_RFC822, strtotime(" 30 days")));
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT', true, 304);
    exit;
}





//get the md5 hash of image to see if a image has already been cached
//$md5 = md5_file($image_path);
//die($md5);
$cacheName = md5_file($filePath).$source['extension'];

$folder = $type . '-' . $max_width . 'x' . $max_height;

$compiled = $config->site->image->cacheDirectory.'/' . $folder . '/' . $cacheName;
if (file_exists($compiled)) {

    header("Content-type: image/jpeg");
    readfile($compiled);
    exit;
}
//make dir if needed
if (!file_exists($config->site->image->cacheDirectory.'/' . $folder . '/')) {
//        if (!file_exists($this->site->image->cacheDirectory.'/')) {
//            if (!@mkdir($this->site->image->cacheDirectory.'/', 0777)) {
//                die('Error: Failed to Create mcd_uploader Dir');
//            }
//        }
    if (!@mkdir($config->site->image->cacheDirectory.'/' . $folder . '/', 0777)) {
        error('can not create sub dir: '.$config->site->image->cacheDirectory.'/' . $folder . '/', $max_width, $max_height);
    }
}

if (!is_writable($config->site->image->cacheDirectory.'/' . $folder . '/')) {
    error('cache folder not writeable', $max_width, $max_height);
}

    
    
    
ini_set("memory_limit", "50M");
// Load image
$img = NULL;

$ext = $source['extension'];
//$ext = 'jpg';

if ($ext == 'jpg' || $ext == 'jpeg') {
    $img = imagecreatefromjpeg($filePath);
} else if ($ext == 'png') {
    $img = @imagecreatefrompng($filePath);
// Only if your version of GD includes GIF support
} else if ($ext == 'gif') {
    $img = @imagecreatefrompng($filePath);
}

if(!$img){
    error('can not create image', $max_width, $max_height);
}
// If an image was successfully loaded, test the image for size

    // Get image size and scale ratio
    $width = imagesx($img);
    $height = imagesy($img);

    $scale = min($max_width / $width, $max_height / $height);

    // If the image is larger than the max shrink it
    if ($scale < 1) {
        $new_width = floor($scale * $width);
        $new_height = floor($scale * $height);
        $tempImg = imagecreatetruecolor($new_width, $new_height);

        imagecopyresampled($tempImg, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($img);
        $width = $new_width;
        $height = $new_height;
        $img = $tempImg;
    }


if ($type == 2) {
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    //imagettftext($img, 48, 0, 6, $height - 24, $black, realpath('arial.ttf'),'MCD');
    //imagettftext($img, 48, 0, 5, $height - 25, $white, realpath('arial.ttf'),'MCD');
    //imagettftext($img, 20, 0, 6, $height - 4, $black, realpath('Edwardian_Script_ITC.ttf'),'Fat Ground');
    //imagettftext($img, 20, 0, 5, $height - 5, $white, realpath('Edwardian_Script_ITC.ttf'),'Fat Ground');
    //imagettftext($img, 8, 0, $width - 109, $height - 4, $black, realpath('arial.ttf'),'�'.date("Y").' Walter Joseph');
    //imagettftext($img, 8, 0, $width - 110, $height - 5, $white, realpath('arial.ttf'),'�'.date("Y").' Walter Joseph');
} else {
    /*
      $white = imagecolorallocate($img,255,255,255);
      $black = imagecolorallocate($img,0,0,0);
      imagettftext($img, 8, 0, 6, $height - 4, $black, realpath('arial.ttf'),'FAT');
      imagettftext($img, 8, 0, 5, $height - 5, $white, realpath('arial.ttf'),'FAT');
     */
}

// Display the image
//$img2 = $img;
//fixColor($img);

imagejpeg($img, $compiled, 80);

header("Content-type: image/jpeg");
readfile($compiled);
//imagejpeg($img, NULL, 80);
//$handle = fopen ($compiled, "rb");
//echo fread ($handle, filesize ($compiled));
//fclose ($handle);
//echo 'test';
//echo $compiled;