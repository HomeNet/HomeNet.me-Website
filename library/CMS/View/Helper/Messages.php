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
class CMS_View_Helper_Messages extends Zend_View_Helper_HtmlElement
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
    public function messages()
    {
        return $this;
    }
    
     public function add($title = null, $content = null, $type = 'notice', $persistent = false)
    {
        $hash = md5($title . $content);
        if(!isset($_SESSION['messages'])){
            $_SESSION['messages'] = array();
        }
        $_SESSION['messages'][$hash] = array('title'=>$title, 'content'=> $content, 'type' => $type, 'persistent' => $persistent);
        
    }
    
     public function getContainer(){
        if(!isset($_SESSION['messages'])){
            $_SESSION['messages'] = array();
        }
        return $_SESSION['messages'];
    }
    
    public function __toString() {
        try {
        return $this->render();
        } catch(Exception $e){
             trigger_error($e);
        }
    }
    
    public function render(){
        
        if(empty($_SESSION['messages'])){
            return '';
        }
        
        $messages = array();
        
        foreach($_SESSION['messages'] as $hash => $message){
            $messages[] = $this->renderMessage($hash, $message['title'], $message['content'], $message['type'], $message['persistent']);
            if(!$message['persistent']){
                unset($_SESSION['messages'][$hash]);
            }
        }
        
        return implode("\n",$messages);
        
    }
    
    public function renderMessage($hash, $title, $content, $type, $persistent){
        $validType = array('notice','warning','fatal');
        if(!in_array($type, $validType)){
            return '';
            //throw new CMS_Exception('Invalid Message Class: '.$type);
        }

        $uiClass = 'highlight';
        $icon = 'info';

        if($type != 'notice'){
            $uiClass = 'error';
            $icon = 'alert';
        }

        $xhtml = '<div class="ui-widget ui-state-'.$uiClass.' ui-corner-all cms-message" >';
        $xhtml .= '<span class="ui-icon ui-icon-'.$icon.'"></span>';
        $xhtml .= '<strong>'.$title.'</strong>';
        if(!empty($content)){
            $xhtml .= $content;
        }     
        $xhtml .= '</div>';
        return $xhtml;
    }
}