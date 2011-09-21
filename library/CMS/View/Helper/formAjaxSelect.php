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
 * @package CMS
 * @subpackage View
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class CMS_View_Helper_FormAjaxSelect extends ZendX_JQuery_View_Helper_UiWidget
{
	/**
	 * Render a Ajax Select Element.
	 *
	 * @link   http://docs.jquery.com/UI/ColorPicker
	 * @param  string $id
	 * @param  string $value
	 * @param  array  $params
	 * @param  array  $attribs
	 * @return string
	 */
    public function formAjaxSelect($name, $value = null, $attribs = null, $options = null, $params = null)
    {
        //unset($attribs['par'])//
        
       // $attribs = $this->_prepareAttributes($name, $value, $attribs);
     //   die(debugArray($attribs));        
        
        $jquery = $this->view->jQuery();
        $jquery->enable();
        
        $jqHandler = (ZendX_JQuery_View_Helper_JQuery::getNoConflictMode()==true)?'$j':'$';

//        $attribs = array();
//        if(isset($options['attribs']) && is_array($options['attribs'])) {
//            $attribs = $options['attribs'];
//        }
//
//        //
//        // The next following 4 conditions check for html attributes that the link might need
//        //
//        if(empty($options['noscript']) || $options['noscript'] == false) {
//            $attribs['href'] = "#";
//        } else {
//            $attribs['href'] = $url;
//        }
//
//        if(!empty($options['title'])) {
//            $attribs['title'] = $options['title'];
//        }

        // class value is an array because the jQuery CSS selector
        // click event needs its own classname later on
        if(!isset($attribs['class'])) {
            $attribs['class'] = array();
        } elseif(is_string($attribs['class'])) {
            $attribs['class'] = explode(" ", $attribs['class']);
        }
        if(!empty($options['class'])) {
            $attribs['class'][] = $options['class'];
        }

        if(!empty($options['id'])) {
            $attribs['id'] = $options['id'];
        }

        //
        // Execute Javascript inline?
        //
        $inline = false;
        if(!empty($options['inline']) && $options['inline'] == true) {
            $inline = true;
        }

        //
        // Detect the callbacks:
        // Just those two callbacks, beforeSend and complete can be defined for the $.get and $.post options.
        // Pick all the defined callbacks and put them on their respective stacks.
        //
        $callbacks = array('beforeSend' => null, 'complete' => null);
        if(isset($options['beforeSend'])) {
            $callbacks['beforeSend'] = $options['beforeSend'];
        }
        if(isset($options['complete'])) {
            $callbacks['complete'] = $options['complete'];
        }

        $updateContainer = false;
        if(!empty($options['update']) && is_string($options['update'])) {
            $updateContainer = $options['update'];

            // Additionally check if there is a callback complete that is a shortcut to be executed
            // on the specified update container
            if(!empty($callbacks['complete'])) {
                switch(strtolower($callbacks['complete'])) {
                    case 'show':
                        $callbacks['complete'] = sprintf("%s('%s').show();", $jqHandler, $updateContainer);
                        break;
                    case 'showslow':
                        $callbacks['complete'] = sprintf("%s('%s').show('slow');", $jqHandler, $updateContainer);
                        break;
                    case 'shownormal':
                        $callbacks['complete'] = sprintf("%s('%s').show('normal');", $jqHandler, $updateContainer);
                        break;
                    case 'showfast':
                        $callbacks['complete'] = sprintf("%s('%s').show('fast');", $jqHandler, $updateContainer);
                        break;
                    case 'fadein':
                        $callbacks['complete'] = sprintf("%s('%s').fadeIn('normal');", $jqHandler, $updateContainer);
                        break;
                    case 'fadeinslow':
                        $callbacks['complete'] = sprintf("%s('%s').fadeIn('slow');", $jqHandler, $updateContainer);
                        break;
                    case 'fadeinfast':
                        $callbacks['complete'] = sprintf("%s('%s').fadeIn('fast');", $jqHandler, $updateContainer);
                        break;
                    case 'slidedown':
                        $callbacks['complete'] = sprintf("%s('%s').slideDown('normal');", $jqHandler, $updateContainer);
                        break;
                    case 'slidedownslow':
                        $callbacks['complete'] = sprintf("%s('%s').slideDown('slow');", $jqHandler, $updateContainer);
                        break;
                    case 'slidedownfast':
                        $callbacks['complete'] = sprintf("%s('%s').slideDown('fast');", $jqHandler, $updateContainer);
                        break;
                }
            }
        }

        if(empty($options['dataType'])) {
            $options['dataType'] = "html";
        }
        
//        if(!empty($options['url'])) {
//            $url$options['url'];
//        }
        
        $params[$name] = '%%REPLACE%%';
        $replace = sprintf("%s('select#%s').val()", $jqHandler, $name);

        $requestHandler = $this->_determineRequestHandler($options, (count($params)>0)?true:false);

        $callbackCompleteJs = array();
        if($updateContainer != false) {
            if($options['dataType'] == "text") {
                $callbackCompleteJs[] = sprintf("%s('%s').text(data);", $jqHandler, $updateContainer);
            } else {
                $callbackCompleteJs[] = sprintf("%s('%s').html(data);", $jqHandler, $updateContainer);
            }
        }
        if($callbacks['complete'] != null) {
            $callbackCompleteJs[] = $callbacks['complete'];
        }

        if(isset($params) && count($params) > 0) {
            $params = ZendX_JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }
        
        $params = str_replace('"%%REPLACE%%"', $replace, $params);

        $js = array();
        if($callbacks['beforeSend'] != null) {
            switch(strtolower($callbacks['beforeSend'])) {
                case 'fadeout':
                    $js[] = sprintf("%s(this).fadeOut();", $jqHandler);
                    break;
                case 'fadeoutslow':
                    $js[] = sprintf("%s(this).fadeOut('slow');", $jqHandler);
                    break;
                case 'fadeoutfast':
                    $js[] = sprintf("%s(this).fadeOut('fast');", $jqHandler);
                    break;
                case 'hide':
                    $js[] = sprintf("%s(this).hide();", $jqHandler);
                    break;
                case 'hideslow':
                    $js[] = sprintf("%s(this).hide('slow');", $jqHandler);
                    break;
                case 'hidefast':
                    $js[] = sprintf("%s(this).hide('fast');", $jqHandler);
                    break;
                case 'slideup':
                    $js[] = sprintf("%s(this).slideUp(1000);", $jqHandler);
                    break;
                default:
                    $js[] = $callbacks['beforeSend'];
                    break;
            }
        }

        switch($requestHandler) {
            case 'GET':
                $js[] = sprintf("%s.get('%s', %s, function(data, textStatus) { %s }, '%s');return false;",
                    $jqHandler, $options['url'], $params, implode(" ", $callbackCompleteJs), $options['dataType']);
                break;
            case 'POST':
                $js[] = sprintf("%s.post('%s', %s, function(data, textStatus) { %s }, '%s');return false;",
                    $jqHandler,$options['url'], $params, implode(" ", $callbackCompleteJs), $options['dataType']);
                break;
        }

        $js = implode($js);

        if($inline == true) {
            $attribs['onclick'] = $js;
        } else {
//            if(!isset($attribs['id'])) {
//                $clickClass = sprintf("ajaxLink%d", ZendX_JQuery_View_Helper_AjaxLink::$currentLinkCallbackId);
//                ZendX_JQuery_View_Helper_AjaxLink::$currentLinkCallbackId++;
//
//                $attribs['class'][] = $clickClass;
//                $onLoad = sprintf("%s('input.%s').click(function() { %s });", $jqHandler, $clickClass, $js);
//            } else {
                $onLoad = sprintf("%s('select#%s').change(function() { %s });", $jqHandler, $name, $js);
//            }

            $jquery->addOnLoad($onLoad);
        }

        if(count($attribs['class']) > 0) {
            $attribs['class'] = implode(" ", $attribs['class']);
        } else {
            unset($attribs['class']);
        }

//        $html = '<a'
//            . $this->_htmlAttribs($attribs)
//            . '>'
//            . $label
//            . '</a>';
//        return $html;
//
//        $attribs = array_merge($attribs,array('data-text'=>'hidden', 'data-hex'=>'true'));
        $options = $attribs['options'];
        unset($attribs['options']);
	return $this->view->formSelect($name, $value, $attribs, $options);
    }
    /**
     * Determine which request method (GET or POST) should be used.
     *
     * Normally the request method is determined implicitly by the rule,
     * if addiotional params are sent, POST, if not GET. You can overwrite
     * this behaviiour by implicitly setting $options['method'] = "POST|GET";
     *
     * @param  Array   $options
     * @param  Boolean $hasParams
     * @return String
     */
    protected function _determineRequestHandler($options, $hasParams)
    {
        if(isset($options['method']) && in_array(strtoupper($options['method']), array('GET', 'POST'))) {
            return strtoupper($options['method']);
        }
        $requestHandler = "GET";
        if($hasParams == true) {
            $requestHandler = "POST";
        }
        return $requestHandler;
    }
}