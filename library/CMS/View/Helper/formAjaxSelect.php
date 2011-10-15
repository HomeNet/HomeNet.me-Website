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
class CMS_View_Helper_FormAjaxSelect extends CMS_View_Helper_FormAjaxElement
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
    public function formAjaxSelect($name, $value = null, $attribs = null, $params = null)
    {
        extract($this->_prepareArgs($name, $value, $attribs, $params));
        
       // $attribs = $this->_prepareAttributes($name, $value, $attribs);
     //   die(debugArray($attribs));        
        
        $jquery = $this->view->jQuery();
        $jquery->enable();
        
        $jqHandler = (ZendX_JQuery_View_Helper_JQuery::getNoConflictMode()==true)?'$j':'$';



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

        //
        // Detect the callbacks:
        // Just those two callbacks, beforeSend and complete can be defined for the $.get and $.post options.
        // Pick all the defined callbacks and put them on their respective stacks.
        //
        $callbacks = array('beforeSend' => null, 'complete' => null);
        if(isset($params['beforeSend'])) {
            $callbacks['beforeSend'] = $params['beforeSend'];
        }
        if(isset($params['complete'])) {
            $callbacks['complete'] = $params['complete'];
        }

        $updateContainer = false;
        if(!empty($params['update']) && is_string($params['update'])) {
            $updateContainer = $params['update'];

            // Additionally check if there is a callback complete that is a shortcut to be executed
            // on the specified update container
            if(!empty($callbacks['complete'])) {
                
                $callbacks['complete'] = "$('".$updateContainer."')";
                
                switch(strtolower($callbacks['complete'])) {
                    case 'show':
                        $callbacks['complete'] .= ".show();";
                        break;
                    case 'showslow':
                        $callbacks['complete'] .= ".show('slow');";
                        break;
                    case 'shownormal':
                        $callbacks['complete'] .= ".show('normal');";
                        break;
                    case 'showfast':
                        $callbacks['complete'] .= ".show('fast');";
                        break;
                    case 'fadein':
                        $callbacks['complete'] .= ".fadeIn('normal');";
                        break;
                    case 'fadeinslow':
                        $callbacks['complete'] .= ".fadeIn('slow');";
                        break;
                    case 'fadeinfast':
                        $callbacks['complete'] .= ".fadeIn('fast');";
                        break;
                    case 'slidedown':
                        $callbacks['complete'] .= ".slideDown('normal');";
                        break;
                    case 'slidedownslow':
                        $callbacks['complete'] .= ".slideDown('slow');";
                        break;
                    case 'slidedownfast':
                        $callbacks['complete'] .= ".slideDown('fast');";
                        break;
                }
            }
        }

        if(empty($params['dataType'])) {
            $params['dataType'] = "html";
        }
        
//        if(!empty($options['url'])) {
//            $url$options['url'];
//        }
        
        $options = "{element:$('select#".$name."').val()}";

        $requestHandler = $this->_determineRequestHandler($params, true);

        $callbackCompleteJs = array();
        
        if($updateContainer != false) {
            if($params['dataType'] == "text") {
                $callbackCompleteJs[] = "$('".$updateContainer."').text(data);";
            } else {
                $callbackCompleteJs[] = "$('".$updateContainer."').html(data);";
            }
        }
        if($callbacks['complete'] != null) {
            $callbackCompleteJs[] = $callbacks['complete'];
        }

        $js = array();
        if($callbacks['beforeSend'] != null) {
            switch(strtolower($callbacks['beforeSend'])) {
                case 'fadeout':
                    $js[] = "$(this).fadeOut();";
                    break;
                case 'fadeoutslow':
                    $js[] = "$(this).fadeOut('slow');";
                    break;
                case 'fadeoutfast':
                    $js[] = "$(this).fadeOut('fast');";
                    break;
                case 'hide':
                    $js[] = "$(this).hide();";
                    break;
                case 'hideslow':
                    $js[] = "$(this).hide('slow');";
                    break;
                case 'hidefast':
                    $js[] = "$(this).hide('fast');";
                    break;
                case 'slideup':
                    $js[] = "$(this).slideUp(1000);";
                    break;
                default:
                    $js[] = $callbacks['beforeSend'];
                    break;
            }
        }
        
        $callbackCompleteJs = implode(" ", $callbackCompleteJs);

        switch($requestHandler) {
            case 'GET':
                $js[] = " $.get('".$params['url']."', ".$options.", function(data, textStatus) { ".$callbackCompleteJs." }, '".$params['dataType']."');return false;";
                break;
            case 'POST':
                $js[] = "$.post('".$params['url']."', ".$options.", function(data, textStatus) { ".$callbackCompleteJs." }, '".$params['dataType']."');return false;";
                break;
        }

        $js = implode($js);

        $jquery->addOnLoad("$('select#".$name."').change(function() { ".$js." });");
        

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
        $options = $params['options'];
        //unset($attribs['options']);
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