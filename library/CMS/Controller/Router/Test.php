<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Rewrite.php 23775 2011-03-01 17:25:24Z ralph $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Ruby routing based Router.
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class CMS_Controller_Router_Test extends Zend_Controller_Router_Rewrite
{
    public function setRoutes($routes)
    {
        $this->_routes = $routes;
    }

    public function route(Zend_Controller_Request_Abstract $request)
    {
       

       if(!is_null($request->getModuleName()) && !is_null($request->getControllerName()) && !is_null($request->getActionName())){
           
           $request->setParam($request->getModuleKey(),     $request->getModuleName());
           $request->setParam($request->getControllerKey(), $request->getControllerName());
           $request->setParam($request->getActionKey(),     $request->getActionName());
           
            return $request;
        }
      return parent::route($request);
    }   
}
