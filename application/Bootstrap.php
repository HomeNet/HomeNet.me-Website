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
 * @package Core
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    public static function autoload($class) {
       // die('using my autoload');
        include str_replace('_', '/', $class) . '.php';
        return $class;
    }
    
    protected function _initSession(){
        Zend_Session::start();
    }
    
    protected function _initLogging(){
        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);
        Zend_Registry::set('logger', $logger);

    
    }

    protected function _initLoaderResource() {
//        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
//                    'basePath' => APPLICATION_PATH,
//                    'namespace' => '',
//                ));
//
//        var_dump($resourceLoader->getDefaultResourceType());
//        exit;
//          $resourceLoader->addResourceType('acl', 'acls/', 'Acl')
//          ->addResourceType('form', 'forms/', 'Form')
//          ->addResourceType('model', 'models/', 'Model');
         
    }
    
    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
        return $config;
    }

    

    protected function _initView() {
        $options = Zend_Registry::get('config');
        if (isset($options->resources->view)) {
            $view = new Zend_View($options->resources->view);
        } else {
            $view = new Zend_View;
        }
        if (isset($options->resources->view->doctype)) {
            $view->doctype($options->resources->view->doctype);
        }
        if (isset($options->resources->view->contentType)) {
            $view->headMeta()->appendHttpEquiv('Content-Type', $options->resources->view->contentType);
        }

        //setup title
        $view->headTitle($options->site->name);
        $view->headTitle()->setDefaultAttachOrder('PREPEND');
        $view->headTitle()->setSeparator(' | ');

        //Setup jquery
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
        $view->jQuery()->enable();
        $view->jQuery()->setVersion('1.7.1');
        $view->jQuery()->useCdn();
        $view->jQuery()->uiEnable();
        $view->jQuery()->setUiVersion('1.8.16');
        $view->jQuery()->useUiCdn();
        
        
        //setup themes
        $defaultTheme = 'default';
        if (isset($options->site->defaultTheme)) {
            $defaultTheme = $options->site->defaultTheme;
        }

        //$isMobile = true;
        if(APPLICATION_ENV == 'mobile'){

            $mobileTheme = 'mobile';
            if (isset($options->site->mobileTheme)) {
                $mobileTheme = $options->site->mobileTheme;
            }

            $theme = $mobileTheme;
        } else {
            $theme = null;
            if (isset($options->site->theme)) {
                $theme = $options->site->theme;
            }
        }
        
        $layout = Zend_Layout::startMvc();
        
        $base = APPLICATION_PATH.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR;

        //add default path
        $layout->setLayoutPath($base.'default'.DIRECTORY_SEPARATOR.'layouts');
        $view->setScriptPath(  $base.'default'.DIRECTORY_SEPARATOR.'views');

        
       // $layout->add
        
        if($defaultTheme != 'default'){
            if(!file_exists($base.$defaultTheme)){
                throw new Zend_Exception('Theme folder Doesn&quot;t exsist: '.$base.$defaultTheme);
            }
            //$layout->addLayoutPath($base.$defaultTheme.'/layouts/scripts/');
            $layout->setLayoutPath($base.$defaultTheme.'/layouts/');
            
            $view->addScriptPath($base.$defaultTheme.'/views/');
        }

        if(!empty($theme)){
            if(!file_exists($base.$theme)){
                throw new Zend_Exception('Theme folder Doesn&quot;t exsist: '.$base.$theme);
            }
            
           // $layout->addLayoutPath($base.$theme.'/layouts/scripts/');
            $layout->setLayoutPath($base.$theme.'/layouts/');
            
            $view->addScriptPath($base.$theme.'/views/');
        }
        
        $layout->setLayout('one-column');
        
        //setup our custom helpers
        $view->addHelperPath('CMS/View/Helper/', 'CMS_View_Helper');

        //setup viewrender
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        $viewRenderer->setViewScriptPathNoControllerSpec('generic/:action.:suffix');

 //
        //:moduleDir
        if(!empty($theme)){
            $viewRenderer->setViewBasePathSpec($base.$theme.'/modules/:module/views');
        }
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        Zend_Registry::set('layout', $layout);
        Zend_Registry::set('view', $view);
        return $view;
    }

    protected function _initModifiedFrontController() {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        if(APPLICATION_ENV == 'testing'){

            $front->setParam('noErrorHandler', true);
            $front->throwExceptions(true);
            $routes = $front->getRouter()->getRoutes();
           // $params = $front->get
            $front->setRouter('CMS_Controller_Router_Test');
            $front->getRouter()->setRoutes($routes);
            $front->getRouter()->setFrontController($front);

            
            $response = new Zend_Controller_Response_HttpTestCase;
        } else {
            $response = new Zend_Controller_Response_Http;
        }
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
        $front->setResponse($response);
        //$front->setParam('prefixDefaultModule', false);
        $front->setParam('noViewRender',true);
    }
    
    
    

}

function _htmlspecialchars($item){
    
    if(is_object($item)){
        return $item;
    }
    
    if(is_array($item)){
        return array_map("_htmlspecialchars", $item);
    }
     return htmlspecialchars($item);
    //return htmlspecialchars($item);
}


function debugArray($array) {
    if(is_array($array)){
   $array = array_map("_htmlspecialchars", $array);
    }
    return '<pre>' . print_r($array, 1) . '</pre>';
}

class NotAllowedException extends Exception {
      protected $code = 403;  
}

class NotSupportedException extends Exception {
    
}

class NotFoundException extends DomainException {
    protected $code = 404;  
}

class DuplicateEntryException extends DomainException {
    
}

class RequiresFurtherActionException extends DomainException {
    
}

function delete_directory($dirname) {
   if (is_dir($dirname))
      $dir_handle = opendir($dirname);
   if (!$dir_handle)
      return false;
   while($file = readdir($dir_handle)) {
      if ($file != "." && $file != "..") {
         if (!is_dir($dirname."/".$file))
            unlink($dirname."/".$file);
         else
            delete_directory($dirname.'/'.$file);    
      }
   }
   closedir($dir_handle);
   rmdir($dirname);
}

//from http://neo22s.com/slug/
function cleanFilename($url) {
	// everything to lower and no spaces begin or end
	$url = strtolower(trim($url));
 
	//replace accent characters, depends your language is needed
	//$url=replace_accents($url);
 
	// decode html maybe needed if there's html I normally don't use this
	//$url = html_entity_decode($url,ENT_QUOTES,'UTF8');
 
	// adding - for spaces and union characters
	$find = array('&', '\r\n', '\n', '+',',');
	$url = str_replace ($find, '-', $url);
 
	//delete and replace rest of special chars
	$find = array('/[.]/','/[^a-z0-9_+\-]/', '/[\-]+/', '/<[^>]*>/');
	$repl = array('_','', '-', '');
	$url = preg_replace ($find, $repl, $url);
 
	//return the friendly url
	return $url; 
}

function cleanDir($url) {
	// everything to lower and no spaces begin or end
	$url = strtolower(trim($url));
 
	//replace accent characters, depends your language is needed
	//$url=replace_accents($url);
 
	// decode html maybe needed if there's html I normally don't use this
	//$url = html_entity_decode($url,ENT_QUOTES,'UTF8');
 
	// adding - for spaces and union characters
	$find = array(' ', '&', '\r\n', '\n', '+',',');
	$url = str_replace ($find, '-', $url);
 
	//delete and replace rest of special chars
	$find = array('/[^a-z0-9\-<>\/]/');
	$repl = array('');
	$url = preg_replace ($find, $repl, $url);
 
	//return the friendly url
	return $url; 
}

function attachmentHash($source){
     $source = strtolower($source);
        $config = Zend_Registry::get('config');
        $salt = $config->site->salt;

        $code = 'CTO5K18"->>_1;O5|-)l9>.!1c2P5h' . $salt . 'o]r7^3!3CYMG.hef%d3B20jdd7RuL' .
                $source . '/&"|l.N> U' . $salt . ')WOwl1<NS5XrBN"l|uV/h|7p;r`J4}l*@p"6.x+O7g=T`R<>K.O:l' .
                $source . '<{3|W[=;0w,+[:YY It-(*&rWu"{"R' . $salt . ',*5%8\.?{.#@2:@&1t3.2(f-[&T|1?' .
                $source . 'rC&RuBt@/)"|&,>j\Kr$DWJYL[tSQ5' . $salt . ']e\,"G$-e*{`2i"G(PBTk~ 8p)Vk@@<o' .
                $source . '/l[vr\_8!O}?lKf.' . $salt . 'k5!;JK00Ex<-CO+ji43.][\3%#}x# 41^7EY@Q00/{o9mY';
        return substr(md5($code), 0, 5);
}
function imageHash($source, $width, $height, $type){
     $source = strtolower($source);
        $config = Zend_Registry::get('config');
        $salt = $config->site->salt;
  $code = 'Oqez_H8QnGn|Np8[n-Vp\'=7) yd+xx' . $salt . 'Tw|C$2l{;G*n="rGY=w,:Q?aF@; kTO}c' .
            $width  . '/&"|l.N> U' . $salt . ')WOwl1<NS5XrBN"l|uV/h|7p;r`J4}l*@p"6.x+O7g=T`R<>K.O:l' .
            $height . '0Q`U%;LFIs)(8D0*y]E%RmO#wTZFW0}ME"8-!5i' . $salt . ',&d]Rr6`x~-|Ca)0zkWAKR-q' .
            $type . 'rC&RuBt@/)"|&,>j\Kr$DWJYL[tSQ5' . $salt . ']e\,"G$-e*{`2i"G(PBTk~ 8p)Vk@@<o' .
            $source . '/l[vr\_8!O}?lKf.' . $salt . 'k5!;JK00Ex<-CO+jUe.Q;lHQA=me)o}Y0-$e$IZLmK;N\AY';

        return substr(md5($code), 0, 5);
}
function securityHash($source){
    
    if(is_array($source)){
        $source = implode("", $source);
    }
    
     $source = strtolower($source);
        $config = Zend_Registry::get('config');
        $salt = $config->site->salt;
        
        $user = Core_Model_User_Manager::getUser();
        $user->id;

        $code = 'CTO5K18"->>_1;O5|-)l9>.!1c2P5h' . $salt . 'o]r7^3!3CYMG.hef%d3B20jdd7RuL' .
                $source . '/&"|l.N> U' . $salt . ')WOwl1<NS5XrBN"l|uV/h|7p;r'.$user->id.'`J4}l*@p"6.x+O7g=T`R<>K.O:l' .
                $source . '<{3|W[=;0w,+[:YY It-(*&rWu"{"R' . $salt . ',*5%8\.?{.#@2:@&1t3.2(f-[&T|1?' .
                $source . 'rC&RuBt@/)"|&,>j\Kr$DWJYL[tSQ5' . $salt . ']e\,"G$-e*{`2i'.$user->id.'"G(PBTk~ 8p)Vk@@<o' .
                $source . '/l[vr\_8!O}?lKf.' . $salt . 'k5!;JK00Ex<-CO+ji43.][\3%#}x# 41^7EY@Q00/{o9mY';
        return substr(md5($code), 0, 10);
}
