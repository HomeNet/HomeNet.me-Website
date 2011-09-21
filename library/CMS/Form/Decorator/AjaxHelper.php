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
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Decorator_Abstract */
require_once 'Zend/Form/Decorator/Abstract.php';

/**
 * Zend_Form_Decorator_Image
 *
 * Accepts the options:
 * - separator: separator to use between image and content (defaults to PHP_EOL)
 * - placement: whether to append or prepend label to content (defaults to append)
 * - tag: if set, used to wrap the label in an additional HTML tag
 *
 * Any other options passed will be used as HTML attributes of the image tag.
 *
 * @package    CMS
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Image.php 23775 2011-03-01 17:25:24Z ralph $
 */
class CMS_Form_Decorator_AjaxHelper extends Zend_Form_Decorator_Abstract
{
    /**
     * Attributes that should not be passed to helper
     * @var array
     */
    protected $_attribBlacklist = array('helper', 'placement', 'separator', 'tag');

    /**
     * Default placement: append
     * @var string
     */
    protected $_placement = 'APPEND';

    /**
     * HTML tag with which to surround image
     * @var string
     */
    protected $_tag;
    
    
    /**
     * View helper to use when rendering
     * @var string
     */
    protected $_helper;

    /**
     * Set view helper to use when rendering
     *
     * @param  string $helper
     * @return Zend_Form_Decorator_Element_ViewHelper
     */
    public function setHelper($helper)
    {
        $this->_helper = (string) $helper;
        return $this;
    }

    /**
     * Retrieve view helper for rendering element
     *
     * @return string
     */
    public function getHelper()
    {
        if (null === $this->_helper) {
            $options = $this->getOptions();
            if (isset($options['helper'])) {
                $this->setHelper($options['helper']);
                $this->removeOption('helper');
            } else {
                $element = $this->getElement();
                if (null !== $element) {
                    if (null !== ($helper = $element->getAttrib('helper'))) {
                        $this->setHelper($helper);
                    } else {
                        $type = $element->getType();
                        if ($pos = strrpos($type, '_')) {
                            $type = substr($type, $pos + 1);
                        }
                        $this->setHelper('form' . ucfirst($type));
                    }
                }
            }
        }
        return $this->_helper;
    }
    
     public function getElementAttribs()
    {
        if (null === ($element = $this->getElement())) {
            return null;
        }

        $attribs = $element->getAttribs();
        if (isset($attribs['helper'])) {
            unset($attribs['helper']);
        }

        if (method_exists($element, 'getSeparator')) {
            if (null !== ($listsep = $element->getSeparator())) {
                $attribs['listsep'] = $listsep;
            }
        }

        if (isset($attribs['id'])) {
            return $attribs;
        }

        $id = $element->getName();

        if ($element instanceof Zend_Form_Element) {
            if (null !== ($belongsTo = $element->getBelongsTo())) {
                $belongsTo = preg_replace('/\[([^\]]+)\]/', '-$1', $belongsTo);
                $id = $belongsTo . '-' . $id;
            }
        }

        $element->setAttrib('id', $id);
        $attribs['id'] = $id;

        return $attribs;
    }

//    /**
//     * Set HTML tag with which to surround label
//     *
//     * @param  string $tag
//     * @return Zend_Form_Decorator_Image
//     */
//    public function setTag($tag)
//    {
//        $this->_tag = (string) $tag;
//        return $this;
//    }
//
//    /**
//     * Get HTML tag, if any, with which to surround label
//     *
//     * @return void
//     */
//    public function getTag()
//    {
//        if (null === $this->_tag) {
//            $tag = $this->getOption('tag');
//            if (null !== $tag) {
//                $this->removeOption('tag');
//                $this->setTag($tag);
//            }
//            return $tag;
//        }
//
//        return $this->_tag;
//    }
//
//    /**
//     * Get attributes to pass to image helper
//     *
//     * @return array
//     */
//    public function getAttribs()
//    {
//        $attribs = $this->getOptions();
//
//        if (null !== ($element = $this->getElement())) {
//            $attribs['alt'] = $element->getLabel();
//            $attribs = array_merge($attribs, $element->getAttribs());
//        }
//
//        foreach ($this->_attribBlacklist as $key) {
//            if (array_key_exists($key, $attribs)) {
//                unset($attribs[$key]);
//            }
//        }
//
//        return $attribs;
//    }

    /**
     * Render a form image
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
       
        
        $element = $this->getElement();

        $view = $element->getView();
        if (null === $view) {
            require_once 'Zend/Form/Decorator/Exception.php';
            throw new Zend_Form_Decorator_Exception('ViewHelper decorator cannot render without a registered view object');
        }

        $helper        = $this->getHelper();
        $separator     = $this->getSeparator();
        $value         = $element->getValue();
        $attribs       = $this->getElementAttribs();
        $name          = $element->getFullyQualifiedName();
        $id            = $element->getId();
        $attribs['id'] = $id;

        $helperObject  = $view->getHelper($helper);
        if (method_exists($helperObject, 'setTranslator')) {
            $helperObject->setTranslator($element->getTranslator());
        }
        
      //  return "Loop 1";

        $elementContent = $view->$helper($name, $value, $element->getAttribs(), $element->getOptions(), $element->getParams());
        switch ($this->getPlacement()) {
            case self::APPEND:
                return $content . $separator . $elementContent;
            case self::PREPEND:
                return $elementContent . $separator . $content;
            default:
                return $elementContent;
        }
    }
}
