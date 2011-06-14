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
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: ColorPicker.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
//require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Color Picker View Helper
 *
 * @uses 	   Zend_View_Helper_FormText
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CMS_View_Helper_SimpleColorPicker extends ZendX_JQuery_View_Helper_UiWidget
{
	/**
	 * Render a Color Picker in an FormText field.
	 *
	 * @link   http://docs.jquery.com/UI/ColorPicker
	 * @param  string $id
	 * @param  string $value
	 * @param  array  $params
	 * @param  array  $attribs
	 * @return string
	 */
    public function simpleColorPicker($name, $value = null, $attribs = null)
    {
	   // $attribs = $this->_prepareAttributes($id, $value, $attribs);

//	    if(strlen($value) >= 6) {
//	        $params['color'] = $value;
//	    }
//
//	    if(count($params) > 0) {
//            $params = ZendX_JQuery::encodeJson($params);
//	    } else {
//	        $params = "{}";
//	    }

//        $js = sprintf('%s("#%s").colorpicker(%s);',
//            ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
//            $attribs['id'],
//            $params
//        );

        $this->jquery->addJavascriptFile('http://meta100.github.com/mColorPicker/javascripts/mColorPicker_min.js');

       // $this->jquery->addOnLoad($js);

        $attribs = array_merge($attribs,array('data-text'=>'hidden', 'data-hex'=>'true'));

	return $this->view->formColor($name, $value, $attribs);
    }
}