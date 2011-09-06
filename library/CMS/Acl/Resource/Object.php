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
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Resource.php 23775 2011-03-01 17:25:24Z ralph $
 */


/**
 * @see Zend_Acl_Resource_Interface
 */
require_once 'Zend/Acl/Resource/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CMS_Acl_Resource_Object implements Zend_Acl_Resource_Interface, CMS_Acl_Parent_Interface
{
    /**
     * Unique id of Resource
     *
     * @var string
     */
    protected $_resourceId;
    protected  $_parent;

    /**
     * Sets the Resource identifier
     *
     * @param  string $controller
     * @param  string $object
     * @return void
     */
    public function __construct($controller, $object)
    {
        $this->_parent = new CMS_Acl_Resource_Controller($controller);
        $this->_resourceId = $this->_parent->getResourceId().'o'.(string) $object;
    }

    /**
     * Defined by Zend_Acl_Resource_Interface; returns the Resource identifier
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->_resourceId;
    }
    
     /**
     * Defined by Zend_Acl_Parent_Interface; returns the Resource identifier
     *
     * @return string
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Defined by Zend_Acl_Resource_Interface; returns the Resource identifier
     * Proxies to getResourceId()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getResourceId();
    }
}
